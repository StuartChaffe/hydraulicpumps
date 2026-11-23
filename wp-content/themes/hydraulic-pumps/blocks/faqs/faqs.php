<?php
/** @var array $block Block settings, passed in by ACF. */
if ( origin_block_preview( $block ) ) return; // Inserter preview: show preview.jpg instead
// FAQs from the FAQ post type, in the accordion's style. Either every FAQ in
// the chosen categories, with a tab per category (filtered by MixItUp, see
// faqs.js), or a hand-picked list. The intro above is InnerBlocks.
$allowed_blocks = array( 'core/heading', 'core/paragraph' );
$source         = get_field('source') === 'select' ? 'select' : 'category';
$categories     = array();
$faqs           = array();

if ( $source === 'category' ) {
	$term_ids = array_filter( (array) get_field('categories') );

	// Keep the order the categories were picked in, skipping any that are empty
	foreach ( $term_ids as $term_id ) {
		$term = get_term( $term_id, 'faq_category' );
		if ( $term instanceof WP_Term && $term->count ) {
			$categories[] = $term;
		}
	}

	if ( $categories ) {
		$faqs = get_posts( array(
			'post_type'      => 'faq',
			'posts_per_page' => -1,
			'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
			'tax_query'      => array( array(
				'taxonomy' => 'faq_category',
				'terms'    => wp_list_pluck( $categories, 'term_id' ),
			) ),
		) );
	}
} else {
	$ids = array_filter( (array) get_field('faqs') );
	if ( $ids ) {
		$faqs = get_posts( array(
			'post_type'      => 'faq',
			'posts_per_page' => -1,
			'post__in'       => $ids,
			'orderby'        => 'post__in',
		) );
	}
}

// Tabs only when there's more than one category to switch between
$has_tabs = count( $categories ) > 1;
$uid      = 'faqs-' . substr( md5( $block['id'] ?? uniqid() ), 0, 6 );
?>
<section class="block faqs accordion<?php echo $has_tabs ? ' faqs--tabs' : ''; ?>" id="<?php echo esc_attr( $uid ); ?>">
	<div class="container container--content">
		<?php if ( is_admin() || ! empty( $content ) ) : ?><?php echo '<InnerBlocks allowedBlocks="' . esc_attr( wp_json_encode( $allowed_blocks ) ) . '" />'; ?><?php endif; ?>

		<?php if ( $faqs ) : ?>
			<?php if ( $has_tabs ) : ?>
				<div class="faqs__filters" role="group" aria-label="<?php esc_attr_e( 'FAQ categories', 'origin' ); ?>">
					<?php foreach ( $categories as $i => $category ) : ?>
						<button class="faqs__filter" type="button" data-filter=".faq-cat-<?php echo esc_attr( $category->slug ); ?>" data-slug="<?php echo esc_attr( $category->slug ); ?>" aria-pressed="<?php echo $i === 0 ? 'true' : 'false'; ?>"><?php echo esc_html( $category->name ); ?></button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if ( $categories ) : // The current category's name, updated by faqs.js ?>
				<h2 class="faqs__heading heading-3" aria-live="polite"><?php echo esc_html( $categories[0]->name ); ?></h2>
			<?php endif; ?>

			<div class="accordion__inner faqs__list">
				<?php foreach ( $faqs as $faq ) :
					$classes = array( 'accordion__item', 'faqs__item' );
					if ( $has_tabs ) {
						$classes[] = 'mix';
						foreach ( get_the_terms( $faq, 'faq_category' ) ?: array() as $term ) {
							$classes[] = 'faq-cat-' . $term->slug;
						}
					}
					// Before MixItUp runs, show the first tab's FAQs only
					$hidden = $has_tabs && ! has_term( $categories[0]->term_id, 'faq_category', $faq );
					?>
					<details class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" id="faq-<?php echo esc_attr( $faq->post_name ); ?>"<?php echo $hidden ? ' style="display: none"' : ''; ?>>
						<summary class="accordion__handle">
							<h3 class="accordion__title"><?php echo esc_html( get_the_title( $faq ) ); ?></h3>
							<span class="accordion__icon" aria-hidden="true"></span>
						</summary>
						<div class="accordion__body content">
							<?php echo wp_kses_post( get_field( 'answer', $faq->ID ) ); ?>
						</div>
					</details>
				<?php endforeach; ?>
			</div>
		<?php elseif ( is_admin() ) : ?>
			<p><?php echo $source === 'select' ? 'Choose some FAQs.' : 'Choose some FAQ categories (with FAQs in them).'; ?></p>
		<?php endif; ?>
	</div>
</section>

<?php if ( $faqs && ! is_admin() ) :
	$schema = array(
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => array_map( function( $faq ) {
			return array(
				'@type'          => 'Question',
				'name'           => get_the_title( $faq ),
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => wp_strip_all_tags( get_field( 'answer', $faq->ID ) ),
				),
			);
		}, $faqs ),
	);
	?>
<script type="application/ld+json"><?php echo wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); ?></script>
<?php endif; ?>
