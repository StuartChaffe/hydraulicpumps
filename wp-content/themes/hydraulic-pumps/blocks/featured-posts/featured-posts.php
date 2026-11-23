<?php
/** @var array $block Block settings, passed in by ACF. */
if ( origin_block_preview( $block ) ) return; // Inserter preview: show preview.jpg instead
// Featured Posts: a row of article cards. Shows the latest posts, the latest
// from a category, or hand-picked posts. Any slots the picked posts don't
// fill are topped up with the latest posts (like socialaf). The intro above
// is InnerBlocks.
$allowed_blocks = array( 'core/heading', 'core/paragraph' );
$template       = array( array( 'core/heading', array( 'level' => 2, 'fontSize' => 'x-large', 'placeholder' => 'Heading' ) ) );
$link           = get_field('link');
$has_link       = $link && ! empty( $link['url'] );
$show           = in_array( get_field('show'), array( 'featured', 'category' ), true ) ? get_field('show') : 'latest';
$count          = max( 1, (int) ( get_field('count') ?: 3 ) );
$posts          = array();

if ( $show === 'featured' ) {
	$ids = array_slice( array_filter( (array) get_field('featured') ), 0, $count );
	if ( $ids ) {
		$posts = get_posts( array( 'post__in' => $ids, 'orderby' => 'post__in', 'posts_per_page' => $count ) );
	}
}

$args = array( 'posts_per_page' => $count - count( $posts ), 'post__not_in' => wp_list_pluck( $posts, 'ID' ) );
if ( $show === 'category' && get_field('category') ) {
	$args['cat'] = (int) get_field('category');
}
if ( $args['posts_per_page'] > 0 ) {
	$posts = array_merge( $posts, get_posts( $args ) );
}
?>
<section class="block featured-posts">
	<div class="container">
		<?php if ( is_admin() || ! empty( $content ) || $has_link ) : // Heading on the left, button on the right ?>
			<div class="featured-posts__header">
				<div class="featured-posts__intro">
					<?php echo '<InnerBlocks allowedBlocks="' . esc_attr( wp_json_encode( $allowed_blocks ) ) . '" template="' . esc_attr( wp_json_encode( $template ) ) . '" />'; ?>
				</div>
				<?php if ( $has_link ) : ?>
					<a class="featured-posts__all btn btn--secondary" href="<?php echo esc_url( $link['url'] ); ?>"<?php echo ! empty( $link['target'] ) ? ' target="' . esc_attr( $link['target'] ) . '" rel="noopener"' : ''; ?>><?php echo esc_html( $link['title'] ?: __( 'View all', 'origin' ) ); ?></a>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $posts ) : ?>
			<div class="posts__grid">
				<?php foreach ( $posts as $post_item ) {
					get_template_part( 'partials/post-card', null, array( 'post' => $post_item ) );
				} ?>
			</div>
		<?php elseif ( is_admin() ) : ?>
			<p>No posts to show yet.</p>
		<?php endif; ?>
	</div>
</section>
