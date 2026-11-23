<?php
/** @var array $block Block settings, passed in by ACF. */
if ( origin_block_preview( $block ) ) return; // Inserter preview: show preview.jpg instead
// Posts: every post, with category filter buttons and "Load more" (MixItUp,
// see posts.js). The intro above is InnerBlocks. Without JavaScript every
// post shows.
$allowed_blocks = array( 'core/heading', 'core/paragraph' );
$per_page       = max( 1, (int) ( get_field('per_page') ?: 6 ) );
$posts          = get_posts( array(
	'post_type'      => 'post',
	'posts_per_page' => -1,
	'orderby'        => 'date',
	'order'          => 'DESC',
) );
// Categories that have posts, alphabetical, without Uncategorised
$categories = $posts ? get_terms( array(
	'taxonomy'   => 'category',
	'hide_empty' => true,
	'object_ids' => wp_list_pluck( $posts, 'ID' ),
	'exclude'    => array( (int) get_option( 'default_category' ) ),
) ) : array();
?>
<section class="block posts" data-per-page="<?php echo esc_attr( $per_page ); ?>" style="--posts-per-page: <?php echo esc_attr( $per_page ); ?>">
	<div class="container">
		<?php if ( is_admin() || ! empty( $content ) ) : ?><?php echo '<InnerBlocks allowedBlocks="' . esc_attr( wp_json_encode( $allowed_blocks ) ) . '" />'; ?><?php endif; ?>

		<?php if ( $posts ) : ?>
			<?php if ( $categories && ! is_wp_error( $categories ) ) : ?>
				<div class="posts__filters">
					<h2 class="posts__filters-title" id="posts-filters-title"><?php esc_html_e( 'Explore by category', 'origin' ); ?></h2>
					<div class="posts__filter-list" role="group" aria-labelledby="posts-filters-title">
						<button class="posts__filter" type="button" data-filter="all" data-slug="" aria-pressed="true"><?php esc_html_e( 'All', 'origin' ); ?></button>
						<?php foreach ( $categories as $category ) : ?>
							<button class="posts__filter" type="button" data-filter=".category-<?php echo esc_attr( $category->slug ); ?>" data-slug="<?php echo esc_attr( $category->slug ); ?>" aria-pressed="false"><?php echo esc_html( $category->name ); ?></button>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>

			<div class="posts__grid" aria-live="polite">
				<?php foreach ( $posts as $post_item ) :
					$classes = array( 'mix' );
					foreach ( get_the_category( $post_item->ID ) as $term ) {
						$classes[] = 'category-' . $term->slug;
					}
					get_template_part( 'partials/post-card', null, array( 'post' => $post_item, 'class' => implode( ' ', $classes ) ) );
				endforeach; ?>
			</div>

			<div class="posts__more">
				<button class="posts__load-more btn btn--secondary" type="button" hidden><?php esc_html_e( 'Load more articles', 'origin' ); ?></button>
			</div>
		<?php elseif ( is_admin() ) : ?>
			<p>No posts yet.</p>
		<?php endif; ?>
	</div>
</section>
