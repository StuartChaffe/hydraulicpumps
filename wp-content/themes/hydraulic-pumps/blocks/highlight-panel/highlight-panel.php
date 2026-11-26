<?php
/** @var array $block Block settings, passed in by ACF. */
if ( origin_block_preview( $block ) ) return; // Inserter preview: show preview.jpg instead
// Highlight Panel: a navy top (heading and intro, with an optional image on the
// right, e.g. the cost comparison graphic) over a grey strip of icon benefits
// and a footnote. Either half can be left empty.
$allowed_blocks = array( 'core/heading', 'core/paragraph', 'core/buttons', 'core/list' );
$template       = array(
	array( 'core/heading', array( 'level' => 2, 'fontSize' => 'x-large', 'placeholder' => 'Heading' ) ),
	array( 'core/paragraph', array( 'placeholder' => 'Intro' ) ),
);
$field   = fn( $name ) => origin_block_field( $block, $name, 'field_highlight_panel_' . $name );
$aside   = $field('aside');
$image   = $aside === 'image' ? get_field('image') : null;
$items   = get_field('items') ?: array();
$note    = $field('footnote');
?>
<section class="block highlight-panel">
	<div class="container">
		<div class="highlight-panel__frame">
			<div class="highlight-panel__top<?php echo $image ? ' highlight-panel__top--has-aside' : ''; ?>">
				<div class="highlight-panel__content">
					<?php echo '<InnerBlocks allowedBlocks="' . esc_attr( wp_json_encode( $allowed_blocks ) ) . '" template="' . esc_attr( wp_json_encode( $template ) ) . '" />'; ?>
				</div>

				<?php if ( $image ) : ?>
					<div class="highlight-panel__media">
						<?php echo wp_get_attachment_image( $image, 'large', false, array( 'class' => 'highlight-panel__image', 'sizes' => '(min-width: 50em) 40rem, 100vw' ) ); ?>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( $items || $note ) : ?>
				<div class="highlight-panel__bottom">
					<?php if ( $items ) : ?>
						<ul class="highlight-panel__items">
							<?php foreach ( $items as $item ) : ?>
								<li class="highlight-panel__item">
									<?php echo origin_icon( $item['icon'] ?? 0, 'highlight-panel__icon' ); ?>
									<?php if ( ! empty( $item['title'] ) ) : ?>
										<strong class="highlight-panel__item-title"><?php echo esc_html( $item['title'] ); ?></strong>
									<?php endif; ?>
									<?php echo wp_kses_post( $item['text'] ?? '' ); ?>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php elseif ( is_admin() ) : ?>
						<p class="highlight-panel__empty"><?php esc_html_e( 'Add benefits in the block settings, or leave them out.', 'origin' ); ?></p>
					<?php endif; ?>
					<?php if ( $note ) : ?>
						<p class="highlight-panel__footnote"><?php echo esc_html( $note ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
