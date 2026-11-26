<?php

/**
 * Set image sizes
 */
function origin_register_image_sizes() {
	add_image_size( 'Banner', 600, 450, true );
	add_image_size( 'Square', 400, 400, true );
	add_image_size( 'Gallery', 1400, 800, true );
	add_image_size( 'Card', 900, 600, true ); // Article cards (3:2), cropped from the centre on upload
}
add_action( 'after_setup_theme', 'origin_register_image_sizes' );

function origin_image_defaults() {
	update_option( 'image_default_align', 'center' );
	update_option( 'image_default_link_type', 'none' );
	update_option( 'image_default_size', 'large' );
}
add_action( 'after_switch_theme', 'origin_image_defaults' );



/**
 * Remove image dimensions
 */
function origin_remove_thumbnail_dimensions( $html, $post_id, $post_image_id ) {
    $html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );
    return $html;
}
add_filter( 'post_thumbnail_html', 'origin_remove_thumbnail_dimensions', 10, 3 );



/**
 * Responsive image helper function
 * Based on https://www.awesomeacf.com/responsive-images-wordpress-acf/
 * Usage: <img class="my_class" <?php echo get_acf_responsive_image_path(get_field( 'image_1' ),'banner','1000px'); ?>  alt="text" /> 
 */
 function get_acf_responsive_image_path($image_id,$image_size,$max_width) {
	// check the image ID is not blank
	if($image_id != '') {
		// set the default src image size
		$image_src = wp_get_attachment_image_url( $image_id, $image_size );
		// set the srcset with various image sizes
		$image_srcset = wp_get_attachment_image_srcset( $image_id, $image_size );
		// generate the markup for the responsive image
		return 'src="'.$image_src.'" srcset="'.$image_srcset.'" sizes="(max-width: '.$max_width.') 100vw, '.$max_width.'"';
	}
 }



/**
 * SVG uploads, for icons (Icon List, Highlight Panel). Only for users who can
 * already post unfiltered HTML (admins and editors), since an SVG can carry
 * script. The icons are shown as a CSS mask, never inlined.
 */
add_filter( 'upload_mimes', function ( $mimes ) {
	if ( current_user_can( 'unfiltered_html' ) ) {
		$mimes['svg'] = 'image/svg+xml';
	}
	return $mimes;
} );

// PHP reports SVGs as image/svg+xml (or text/plain without the XML prolog),
// which WordPress's real-type check rejects; trust the extension instead.
add_filter( 'wp_check_filetype_and_ext', function ( $data, $file, $filename, $mimes ) {
	if ( ! $data['type'] && current_user_can( 'unfiltered_html' ) && 'svg' === strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) ) ) {
		$data['ext']  = 'svg';
		$data['type'] = 'image/svg+xml';
	}
	return $data;
}, 10, 4 );
