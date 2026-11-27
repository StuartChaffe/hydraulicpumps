<?php
/** @var array $block Block settings, passed in by ACF. */
if ( origin_block_preview( $block ) ) return; // Inserter preview: show preview.jpg instead
$allowed_blocks = array( 'core/heading', 'core/paragraph', 'core/buttons', 'core/list' );
$template       = array(
	array( 'core/heading', array( 'level' => 2, 'placeholder' => 'Heading' ) ),
	array( 'core/paragraph', array( 'placeholder' => 'Content' ) ),
);
$position = get_field('image_position') === 'left' ? 'left' : 'right';
$aside    = get_field('aside') === 'quote' ? 'quote' : 'image'; // Blocks saved before the quote option have no value: image

// Image(s)
$images = $aside === 'image' ? array_filter( array( get_field('image'), get_field('image_2') ) ) : array();

// Quote: the curly quote marks are drawn by the theme, so any typed round the text are dropped
$quote = $aside === 'quote' ? trim( (string) get_field('quote_text'), " \t\n\r\"“”" ) : '';
if ( $quote ) {
	$quote_name    = trim( (string) get_field('quote_name') );
	$quote_company = trim( (string) get_field('quote_company') );
	$quote_photo   = get_field('quote_photo');
	$quote_phone   = trim( (string) get_field('quote_phone') );
	$quote_email   = sanitize_email( (string) get_field('quote_email') );
}
$has_aside = $images || $quote;
?>
<section class="block feature-panel feature-panel--image-<?php echo esc_attr( $position ); ?><?php echo $has_aside ? ' feature-panel--has-image' : ''; ?>">
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
		<?php elseif ( $quote ) : ?>
			<?php // Quote card: the quote between big red quote marks, who said it, their phone and email, and their photo in the bottom left corner ?>
			<div class="feature-panel__media feature-panel__media--quote">
				<div class="feature-panel__quote">
					<blockquote class="feature-panel__quote-text">
						<p><?php echo esc_html( $quote ); ?></p>
					</blockquote>
					<?php if ( $quote_name || $quote_company ) : ?>
						<p class="feature-panel__quote-name">
							<?php if ( $quote_name ) : ?><strong><?php echo esc_html( $quote_name ); ?></strong><?php endif; ?>
							<?php if ( $quote_company ) : ?><?php echo $quote_name ? 'at ' : ''; ?><?php echo esc_html( $quote_company ); ?><?php endif; ?>
						</p>
					<?php endif; ?>
					<?php if ( $quote_phone || $quote_email ) : ?>
						<ul class="feature-panel__quote-contact">
							<?php if ( $quote_phone ) : ?>
								<li class="feature-panel__quote-phone"><a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $quote_phone ) ); ?>"><span class="sr-only">Phone: </span><?php echo esc_html( $quote_phone ); ?></a></li>
							<?php endif; ?>
							<?php if ( $quote_email ) : ?>
								<li class="feature-panel__quote-email"><a href="mailto:<?php echo esc_attr( $quote_email ); ?>"><span class="sr-only">Email: </span><?php echo str_replace( '@', '@<wbr>', esc_html( $quote_email ) ); // Wrap after the @ on narrow screens ?></a></li>
							<?php endif; ?>
						</ul>
					<?php endif; ?>
					<?php if ( $quote_photo ) : ?>
						<?php echo wp_get_attachment_image( $quote_photo, 'medium_large', false, array( 'class' => 'feature-panel__quote-photo', 'alt' => '', 'sizes' => '(min-width: 50em) 12rem, 9rem' ) ); ?>
					<?php endif; ?>
				</div>
			</div>
		<?php elseif ( $aside === 'quote' && is_admin() ) : ?>
			<div class="feature-panel__media feature-panel__media--quote"><p>Add the quote in the block settings.</p></div>
		<?php endif; ?>
	</div>
</section>
