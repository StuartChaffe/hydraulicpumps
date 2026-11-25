<?php

/**
 * Handy function for seeing if a page is parent of child
 * Usage: if ( is_tree(2) ) { ... }
 */
function is_tree( $pid ) {
	global $post;
	$ancestors = get_post_ancestors( $pid );
	return is_page() && ( is_page( $pid ) || $post->post_parent == $pid || in_array( $pid, $ancestors ) );
}



/**
 * SVG helper icon
 * Usage:
 */

function get_icon( $icon ) {
	return '<svg class="icon icon-' . $icon . '"><use href="#' . $icon . '"></use></svg>';
}



/**
 * Dump helper. Functions to dump variables to the screen, in a nicley formatted manner.
 * https://gist.github.com/accentinteractive/3838495
 */
if (!function_exists('dump')) {
    function dump ($var, $label = 'Dump', $echo = TRUE)
    {
        // Store dump in variable
        ob_start();
        var_dump($var);
        $output = ob_get_clean();

        // Add formatting
        $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
        $output = '<pre style="background: #FFFEEF; color: #000; border: 1px dotted #000; padding: 10px; margin: 10px 0; text-align: left;">' . $label . ' => ' . $output . '</pre>';

        // Output
        if ($echo == TRUE) {
            echo $output;
        }
        else {
            return $output;
        }
    }
}


if (!function_exists('dump_exit')) {
    function dump_exit($var, $label = 'Dump', $echo = TRUE) {
        dump ($var, $label, $echo);
        exit;
    }
}

/**
 * ID for a tab in the Tabs block, from its label (e.g. "Plant & Machinery" →
 * "plant-machinery"), so a link to #plant-machinery opens that tab. Shared by
 * the Tabs and Tab block templates so their IDs match.
 */
function origin_tab_id( $label ) {
	return sanitize_title( $label ) ?: 'tab';
}

/**
 * Block inserter preview. When the editor renders a block's "example" (the
 * preview shown when hovering over it in the inserter), show the block's
 * preview.jpg screenshot instead of rendering it. Each block.json sets:
 *
 *   "example": { "attributes": { "mode": "preview", "data": { "preview_image": "preview.jpg" } } }
 *
 * Call at the top of a block template: if ( origin_block_preview( $block ) ) return;
 *
 * @param array $block The block settings ACF passes to the template.
 * @return bool True if the preview image was output (so skip the block).
 */
function origin_block_preview( $block ) {
	$file = $block['data']['preview_image'] ?? '';
	if ( ! $file ) {
		return false;
	}

	$slug = str_replace( 'acf/', '', $block['name'] ?? '' );
	printf(
		'<img src="%s" alt="" style="display:block;width:100%%;height:auto">',
		esc_url( get_theme_file_uri( 'blocks/' . $slug . '/' . $file ) )
	);
	return true;
}

/**
 * A block field's value, or the field's default if the block hasn't saved it yet.
 *
 * ACF only stores a block's field values once the block is saved in the editor,
 * so a block added by code (or not yet edited) would get nothing from get_field()
 * and lose the default copy. A field the editor has cleared stays empty.
 *
 * @param array  $block The block settings ACF passes to the template.
 * @param string $name  The field name.
 * @param string $key   The field key.
 * @return mixed
 */
function origin_block_field( $block, $name, $key ) {
	$data = $block['data'] ?? array();
	if ( array_key_exists( $name, $data ) || array_key_exists( $key, $data ) ) {
		return get_field( $name );
	}
	$field = acf_get_field( $key );
	return $field['default_value'] ?? '';
}

/**
 * Login page: the logo and the theme's colours
 * (the values of the variables in src/styles/_variables.scss) and font.
 */
function my_login_logo() {
	$images = get_stylesheet_directory_uri() . '/src/images/';
	$fonts  = get_stylesheet_directory_uri() . '/assets/fonts/';
	?>
	<style type="text/css">
		@font-face {
			font-family: 'DM Sans';
			src: url('<?php echo esc_url( $fonts . 'DM-Sans-Normal.woff2' ); ?>') format('woff2');
			font-weight: 100 1000;
			font-style: normal;
			font-display: swap;
		}

		:root {
			--color-primary: #013473;        /* Navy */
			--color-primary-darker: #002A5D;
			--color-secondary: #EE3D33;      /* Red */
			--color-tertiary: #F5F5F5;       /* Panel grey */
			--radius-s: 5px;
			--radius: 20px;
		}

		body.login {
			background: var(--color-tertiary);
			font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
		}

		#login {
			width: 360px;
			max-width: calc(100% - 2rem);
		}

		#login h1 a, .login h1 a {
			width: 100%;
			height: 98px;
			margin-bottom: 24px;
			background: url('<?php echo esc_url( $images . 'logo.png' ); ?>') center / contain no-repeat;
		}

		.login form {
			margin-top: 0;
			padding: 30px;
			border: 0;
			border-radius: var(--radius);
			box-shadow: 0 8px 30px rgb(1 52 115 / 0.15);
		}

		.login label {
			color: var(--color-primary);
			font-size: 16px;
			font-weight: 700;
		}

		.login .forgetmenot label {
			font-weight: 400;
		}

		.login input[type=text],
		.login input[type=password],
		.login input[type=email] {
			border: 1px solid #D1D1D1;
			border-radius: var(--radius-s);
		}

		.login input[type=text]:focus,
		.login input[type=password]:focus,
		.login input[type=email]:focus,
		.login input[type=checkbox]:focus,
		.login .button.wp-hide-pw:focus {
			border-color: var(--color-primary);
			box-shadow: 0 0 0 1px var(--color-primary);
			outline: 0;
		}

		.login .button.wp-hide-pw {
			color: var(--color-primary);
		}

		/* Log in button: the site's red button */
		.wp-core-ui .button-primary {
			min-height: 0;
			padding: 10px 18px;
			background: var(--color-secondary);
			border: 0;
			border-radius: var(--radius-s);
			box-shadow: none;
			font-weight: 600;
			line-height: 1;
			text-shadow: none;
		}

		.wp-core-ui .button-primary:hover,
		.wp-core-ui .button-primary:focus {
			background: var(--color-primary-darker);
		}

		.wp-core-ui .button-primary:focus {
			box-shadow: 0 0 0 2px #fff, 0 0 0 4px var(--color-primary);
		}

		.login #nav,
		.login #backtoblog {
			padding: 0 30px;
		}

		.login #nav a,
		.login #backtoblog a,
		.login .privacy-policy-link {
			color: var(--color-primary);
		}

		.login #nav a:hover,
		.login #backtoblog a:hover,
		.login .privacy-policy-link:hover {
			color: var(--color-secondary);
		}

		/* Messages navy, errors red */
		.login .message,
		.login .notice,
		.login .success {
			border-left-color: var(--color-primary);
		}

		.login #login_error,
		.login .notice-error {
			border-left-color: var(--color-secondary);
		}

		/* One language, so no switcher */
		.login .language-switcher {
			display: none;
		}
	</style>
	<?php
}
add_action( 'login_head', 'my_login_logo', 20 ); // After core's login styles (printed at 9), so these win

// The logo links to the site, not WordPress.org
add_filter( 'login_headerurl', fn() => home_url( '/' ) );
add_filter( 'login_headertext', fn() => get_bloginfo( 'name' ) );



/**
 * Vimeo details for a video URL: its ID and title (for the player's iframe title).
 * Used by the Page Header block. The oEmbed lookup is cached for a week.
 * Returns null if the URL isn't a Vimeo video.
 */
function page_header_vimeo( $url ) {
	if ( ! $url || ! preg_match( '~vimeo\.com/(?:.*?/)?(\d+)~', $url, $match ) ) return null;
	$id  = $match[1];
	$key = 'vimeo_oembed_' . $id;

	$data = get_transient( $key );
	if ( $data === false ) {
		$data     = array();
		$response = wp_remote_get( 'https://vimeo.com/api/oembed.json?url=' . rawurlencode( 'https://vimeo.com/' . $id ) );
		if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
			$data = (array) json_decode( wp_remote_retrieve_body( $response ), true );
		}
		// Cache failures for an hour so a Vimeo outage doesn't slow every page
		set_transient( $key, $data, $data ? WEEK_IN_SECONDS : HOUR_IN_SECONDS );
	}

	return array(
		'id'    => $id,
		'title' => ! empty( $data['title'] ) ? $data['title'] : __( 'Video', 'origin' ),
	);
}
