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
