<?php
/**
 * Pump finder: searches the Pumps post type by pump name.
 *
 * Every pump stores a search key (its name in capitals with spaces and
 * punctuation removed), so "a10vso 28-dfr" finds "A10VSO28DFR". Searches go
 * to the pump finder page as ?pump=..., from the Pump Finder block's own form
 * or the pump finder CTA anywhere on the site.
 */

const HP_PUMP_KEY_META = '_pump_search_key';
const HP_PUMP_QUERY_VAR = 'pump';

/**
 * A pump name or search term as a search key: capitals and digits only.
 */
function hp_pump_search_key( $text ) {
	return preg_replace( '/[^A-Z0-9]/', '', strtoupper( remove_accents( (string) $text ) ) );
}

/**
 * Keep the search key in step with the title.
 */
add_action( 'save_post_pump', function ( $post_id, $post ) {
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}
	update_post_meta( $post_id, HP_PUMP_KEY_META, hp_pump_search_key( $post->post_title ) );
}, 10, 2 );

/**
 * The pump finder page: always the page at /pump-finder/.
 */
function hp_pump_finder_url() {
	$page = get_page_by_path( 'pump-finder' );
	return $page ? get_permalink( $page ) : home_url( '/pump-finder/' );
}

/**
 * The page's old address, /pumpfinder/, goes to the pump finder (keeping ?pump=).
 */
add_action( 'template_redirect', function () {
	if ( ! is_404() || trim( (string) wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ), '/' ) !== 'pumpfinder' ) {
		return;
	}
	$term = hp_pump_finder_term();
	$url  = hp_pump_finder_url();
	wp_safe_redirect( $term !== '' ? add_query_arg( HP_PUMP_QUERY_VAR, rawurlencode( $term ), $url ) . '#pump-finder' : $url, 301 );
	exit;
} );

/**
 * The search term from the URL, if any.
 */
function hp_pump_finder_term() {
	return isset( $_GET[ HP_PUMP_QUERY_VAR ] ) ? trim( sanitize_text_field( wp_unslash( $_GET[ HP_PUMP_QUERY_VAR ] ) ) ) : '';
}

/**
 * Find the pump whose name matches the search (ignoring case, spaces and punctuation).
 *
 * @return WP_Post|null
 */
function hp_pump_finder_search( $term ) {
	$key = hp_pump_search_key( $term );
	if ( $key === '' ) {
		return null;
	}

	$pumps = get_posts( array(
		'post_type'      => 'pump',
		'posts_per_page' => 1,
		'meta_key'       => HP_PUMP_KEY_META,
		'meta_value'     => $key,
		'no_found_rows'  => true,
	) );

	return $pumps[0] ?? null;
}

/**
 * Pump names containing the search, for the suggestions under the search bar.
 * Names starting with the search come first.
 *
 * @return array[] Each with name and brand.
 */
function hp_pump_finder_suggestions( $term, $limit = 8 ) {
	$key = hp_pump_search_key( $term );
	if ( strlen( $key ) < 2 ) {
		return array();
	}

	$pumps = get_posts( array(
		'post_type'      => 'pump',
		'posts_per_page' => 50, // Enough to sort the best ones to the top
		'orderby'        => 'title',
		'order'          => 'ASC',
		'no_found_rows'  => true,
		'meta_query'     => array(
			array(
				'key'     => HP_PUMP_KEY_META,
				'value'   => $key,
				'compare' => 'LIKE',
			),
		),
	) );

	usort( $pumps, function ( $a, $b ) use ( $key ) {
		$a_starts = str_starts_with( hp_pump_search_key( $a->post_title ), $key );
		$b_starts = str_starts_with( hp_pump_search_key( $b->post_title ), $key );
		return $b_starts <=> $a_starts ?: strnatcasecmp( $a->post_title, $b->post_title );
	} );

	return array_map( function ( $pump ) {
		$brands = get_the_terms( $pump, 'pump_brand' );
		return array(
			'name'  => $pump->post_title,
			'brand' => $brands && ! is_wp_error( $brands ) ? $brands[0]->name : '',
		);
	}, array_slice( $pumps, 0, $limit ) );
}

/**
 * Book a repair form: coming from a pump finder result
 * (?pump=A10VSO28DFR), fill in the pump's make (its brand) and model. The
 * form's pump make and model fields allow dynamic population with the
 * parameter names pump_make and pump_model. An unknown pump still fills the
 * model with what was searched.
 */
add_filter( 'gform_field_value_pump_model', function ( $value ) {
	$term = hp_pump_finder_term();
	if ( $term === '' ) {
		return $value;
	}
	$pump = hp_pump_finder_search( $term );
	return $pump ? $pump->post_title : $term;
} );

add_filter( 'gform_field_value_pump_make', function ( $value ) {
	$pump = hp_pump_finder_term() !== '' ? hp_pump_finder_search( hp_pump_finder_term() ) : null;
	if ( ! $pump ) {
		return $value;
	}
	$brands = get_the_terms( $pump, 'pump_brand' );
	return $brands && ! is_wp_error( $brands ) ? $brands[0]->name : $value;
} );

/**
 * GET /wp-json/hp/v1/pump-suggestions?search=a10
 */
add_action( 'rest_api_init', function () {
	register_rest_route( 'hp/v1', '/pump-suggestions', array(
		'methods'             => 'GET',
		'permission_callback' => '__return_true', // Public: pump names are shown to anyone who searches
		'args'                => array(
			'search' => array(
				'type'              => 'string',
				'required'          => true,
				'sanitize_callback' => 'sanitize_text_field',
			),
		),
		'callback'            => function ( WP_REST_Request $request ) {
			$response = rest_ensure_response( hp_pump_finder_suggestions( $request['search'] ) );
			$response->header( 'Cache-Control', 'public, max-age=300' );
			return $response;
		},
	) );
} );

/**
 * Admin list: brand and turnaround columns.
 */
add_filter( 'manage_pump_posts_columns', function ( $columns ) {
	$date = $columns['date'] ?? null;
	unset( $columns['date'] );
	$columns['title']      = __( 'Pump name', 'origin' );
	$columns['turnaround'] = __( 'Estimated turnaround', 'origin' );
	if ( $date ) {
		$columns['date'] = $date;
	}
	return $columns;
} );

add_action( 'manage_pump_posts_custom_column', function ( $column, $post_id ) {
	if ( $column === 'turnaround' ) {
		echo esc_html( get_field( 'turnaround', $post_id ) ?: '—' );
	}
}, 10, 2 );

add_filter( 'enter_title_here', function ( $text, $post ) {
	return $post->post_type === 'pump' ? __( 'Pump name, e.g. A10VSO28DFR', 'origin' ) : $text;
}, 10, 2 );
