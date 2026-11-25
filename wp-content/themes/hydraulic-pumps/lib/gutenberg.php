<?php


/**
 * Add custom styles and colour palette
 */
function origin_gutenberg_setup() {
	add_theme_support( 'editor-styles' );
	add_editor_style( str_replace( trailingslashit( get_template_directory_uri() ), '', vite_asset( 'src/styles/editor-style.scss' ) ) );
}
add_action( 'after_setup_theme', 'origin_gutenberg_setup' );



/**
 * Add custom block category
 */
function origin_block_categories( $categories ) {
	return array_merge(
		array(
			array(
				'slug'  => 'custom-blocks',
				'title' => __( 'Custom Blocks', 'origin' ),
			),
		),
		$categories
	);
}
add_filter( 'block_categories_all', 'origin_block_categories' );



/**
 * Gutenberg scripts and styles
 */
function origin_gutenberg_scripts() {
	wp_enqueue_script( 'origin-editor', vite_asset( 'src/js/editor.js' ), array( 'wp-blocks', 'wp-dom' ), null, true );
}
add_action( 'enqueue_block_editor_assets', 'origin_gutenberg_scripts' );



/**
 * New pages start with a small page header holding the page title.
 * It's an ordinary block, so it can be removed or changed to large. The title
 * is a Post Title block: editing it updates the page title, and vice versa.
 */
function origin_page_template() {
	$page = get_post_type_object( 'page' );
	if ( ! $page ) return;

	$page->template = array(
		array( 'acf/page-header', array( 'data' => array( 'size' => 'small', '_size' => 'field_6808a8800007' ) ), array(
			array( 'core/post-title', array( 'level' => 1, 'style' => array( 'typography' => array( 'textAlign' => 'center' ) ) ) ),
		) ),
	);
}
add_action( 'init', 'origin_page_template', 20 );
