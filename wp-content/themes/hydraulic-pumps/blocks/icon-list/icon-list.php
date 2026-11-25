<?php
/** @var array $block Block settings, passed in by ACF. */
if ( origin_block_preview( $block ) ) return; // Inserter preview: show preview.jpg instead
// Icon List: each item is an icon (an SVG from ./icons, inlined so it takes
// the text colour) beside a bold title and a short line of text.
$items = get_field('items') ?: array();
?>
<?php if ( $items ) : ?>
	<ul class="icon-list">
		<?php foreach ( $items as $item ) : ?>
			<li class="icon-list__item">
				<?php if ( $svg = origin_icon_list_svg( $item['icon'] ?? '' ) ) : ?>
					<span class="icon-list__icon" aria-hidden="true"><?php echo $svg; // Theme file, not user input ?></span>
				<?php endif; ?>
				<div class="icon-list__text">
					<?php if ( ! empty( $item['title'] ) ) : ?>
						<strong class="icon-list__title"><?php echo esc_html( $item['title'] ); ?></strong>
					<?php endif; ?>
					<?php if ( ! empty( $item['text'] ) ) : ?>
						<?php echo wp_kses_post( $item['text'] ); ?>
					<?php endif; ?>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>
<?php elseif ( is_admin() ) : ?>
	<p class="icon-list__empty"><?php esc_html_e( 'Add items to the Icon List in the block settings.', 'origin' ); ?></p>
<?php endif; ?>
