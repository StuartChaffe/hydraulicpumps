<?php

/**
 * Enable ACF options page
 */
add_action('acf/init', function () {
    if( function_exists('acf_add_options_page') ) {
        $parent = acf_add_options_page(array(
            'page_title'  => __('Theme Options'),
            'menu_title'  => __('Theme Options'),
            'redirect'    => false,
        ));
    }
});



/**
 * ACF JSON: load from global acf-json/ and each block's acf-json/ folder
 */
add_filter('acf/settings/load_json', function ($paths) {
	foreach ( glob( get_stylesheet_directory() . '/blocks/*/acf-json', GLOB_ONLYDIR ) as $path ) {
		$paths[] = $path;
	}
	return $paths;
});



/**
 * ACF JSON: save block field groups into their block folder, everything else
 * into acf-json/. Decided per item, so a bulk sync can't send later items to
 * the wrong folder.
 */
add_filter('acf/json/save_paths', function ($paths, $post) {
	$slug = _origin_block_slug_for_group( $post );
	if ( ! $slug ) {
		return array( get_stylesheet_directory() . '/acf-json' );
	}

	$path = get_stylesheet_directory() . '/blocks/' . $slug . '/acf-json';
	wp_mkdir_p( $path );
	return array( $path );
}, 10, 2);

function _origin_block_slug_for_group( $group ) {
	foreach ( $group['location'] ?? [] as $rules ) {
		foreach ( $rules as $rule ) {
			if ( $rule['param'] === 'block' && $rule['operator'] === '==' ) {
				return str_replace( 'acf/', '', $rule['value'] );
			}
		}
	}
	return null;
}


/**
 * Remove block class
 */
add_filter( 'acf/blocks/wrap_frontend_innerblocks', 'acf_should_wrap_innerblocks', 10, 2 );
	function acf_should_wrap_innerblocks( $wrap, $name ) {
	return false;
}

