<?php
// Each Tab child block outputs its own accordion toggle and panel. On the front
// end the tab bar is built here from the children's labels; the editor shows
// the tabs stacked instead.
$allowed_blocks = array( 'acf/tab' );
$template       = array( array( 'acf/tab' ), array( 'acf/tab' ) );
$tabs           = array();

if ( ! $is_preview && ! empty( $wp_block ) ) {
	foreach ( $wp_block->parsed_block['innerBlocks'] as $inner ) {
		if ( $inner['blockName'] !== 'acf/tab' ) {
			continue;
		}
		$label  = $inner['attrs']['data']['label'] ?? '';
		$label  = $label !== '' ? $label : 'Tab'; // Matches tab.php's fallback
		$tabs[] = array( 'label' => $label, 'id' => origin_tab_id( $label ) );
	}
}
?>
<section class="block tabs<?php echo $is_preview ? '' : ' tabs--front'; ?>">
	<div class="container">
		<div class="tabs__frame">
			<?php if ( $tabs ) : ?>
				<div class="tabs__list" role="tablist">
					<?php foreach ( $tabs as $i => $tab ) : ?>
						<button class="tabs__tab" type="button" role="tab" id="tab-<?php echo esc_attr( $tab['id'] ); ?>" aria-controls="<?php echo esc_attr( $tab['id'] ); ?>-panel" aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>"<?php echo $i === 0 ? '' : ' tabindex="-1"'; ?>><?php echo esc_html( $tab['label'] ); ?></button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
			<div class="tabs__items">
				<?php echo '<InnerBlocks allowedBlocks="' . esc_attr( wp_json_encode( $allowed_blocks ) ) . '" template="' . esc_attr( wp_json_encode( $template ) ) . '" />'; ?>
			</div>
		</div>
	</div>
</section>
