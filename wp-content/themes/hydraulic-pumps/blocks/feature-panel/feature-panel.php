<?php
/** @var array $block Block settings, passed in by ACF. */
if ( origin_block_preview( $block ) ) return; // Inserter preview: show preview.jpg instead
$allowed_blocks = array( 'core/heading', 'core/paragraph', 'core/buttons', 'core/list' );
$template       = array(
	array( 'core/heading', array( 'level' => 2, 'placeholder' => 'Heading' ) ),
	array( 'core/paragraph', array( 'placeholder' => 'Content' ) ),
);
$images         = array_filter( array( get_field('image'), get_field('image_2') ) );
$position       = get_field('image_position') === 'left' ? 'left' : 'right';
?>
<section class="block feature-panel feature-panel--image-<?php echo esc_attr( $position ); ?><?php echo $images ? ' feature-panel--has-image' : ''; ?>">
	<div class="feature-panel__inner container">
		<div class="feature-panel__content">
			<?php if ( is_admin() || ! empty( $content ) ) : ?><?php echo '<InnerBlocks allowedBlocks="' . esc_attr( wp_json_encode( $allowed_blocks ) ) . '" template="' . esc_attr( wp_json_encode( $template ) ) . '" />'; ?><?php endif; ?>
		</div>
		<?php if ( $images ) : ?>
			<?php // One image, or two stacked (e.g. before and after). Each shows its Media Library caption, if it has one, like an Image block's (see _gutenberg.scss) ?>
			<div class="feature-panel__media">
				<?php foreach ( $images as $id ) : ?>
					<figure class="feature-panel__figure wp-block-image">
						<?php echo wp_get_attachment_image( $id, 'large', false, array( 'class' => 'feature-panel__image', 'sizes' => '(min-width: 50em) 40rem, 100vw' ) ); ?>
						<?php if ( $caption = wp_get_attachment_caption( $id ) ) : ?>
							<figcaption class="wp-element-caption"><?php echo wp_kses_post( $caption ); ?></figcaption>
						<?php endif; ?>
					</figure>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
