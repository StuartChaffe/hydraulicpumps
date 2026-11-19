<?php
// One panel of the Content Tabs block. The label is only output in the
// editor, where it stands in for the tab; the front end's tab bar is built by
// content-tabs.php.

/** @var bool $is_preview Passed in by ACF: true in the editor. */
$is_preview = ! empty( $is_preview );

$allowed_blocks = array( 'core/table', 'core/heading', 'core/paragraph', 'core/list', 'core/buttons' );
$template       = array( array( 'core/table', array( 'hasFixedLayout' => false ) ) );
$label          = get_field('label') ?: 'Tab';
$id             = origin_tab_id( $label );
?>
<div class="content-tabs__item" id="<?php echo esc_attr( $id ); ?>" role="tabpanel" aria-labelledby="ctab-<?php echo esc_attr( $id ); ?>">
	<?php if ( $is_preview ) : ?>
		<span class="content-tabs__label"><?php echo esc_html( $label ); ?></span>
	<?php endif; ?>
	<div class="content-tabs__panel">
		<?php echo '<InnerBlocks allowedBlocks="' . esc_attr( wp_json_encode( $allowed_blocks ) ) . '" template="' . esc_attr( wp_json_encode( $template ) ) . '" />'; ?>
	</div>
</div>
