<?php
// Columns and images are for the Tabs block's Plain style
$allowed_blocks = array( 'core/heading', 'core/paragraph', 'core/buttons', 'core/list', 'core/columns', 'core/image', 'acf/icon-list' );
$template       = array(
	array( 'core/heading', array( 'level' => 2, 'placeholder' => 'Heading' ) ),
	array( 'core/paragraph', array( 'placeholder' => 'Content' ) ),
);
$label      = get_field('label') ?: 'Tab';
$background = get_field('background_image');
$id         = origin_tab_id( $label );
?>
<div class="tabs__item" id="<?php echo esc_attr( $id ); ?>">
	<?php // Accordion toggle, used on mobile (and as the label in the editor) ?>
	<h3 class="tabs__heading">
		<button class="tabs__toggle" type="button" id="toggle-<?php echo esc_attr( $id ); ?>" aria-controls="<?php echo esc_attr( $id ); ?>-panel" aria-expanded="false"><?php echo esc_html( $label ); ?><span class="tabs__icon" aria-hidden="true"></span></button>
	</h3>
	<div class="tabs__panel" id="<?php echo esc_attr( $id ); ?>-panel">
		<?php if ( $background ) : ?>
			<?php echo wp_get_attachment_image( $background, 'full', false, array( 'class' => 'tabs__background', 'alt' => '', 'sizes' => '(min-width: 85em) 68rem, 80vw' ) ); ?>
		<?php endif; ?>
		<div class="tabs__content">
			<?php echo '<InnerBlocks allowedBlocks="' . esc_attr( wp_json_encode( $allowed_blocks ) ) . '" template="' . esc_attr( wp_json_encode( $template ) ) . '" />'; ?>
		</div>
	</div>
</div>
