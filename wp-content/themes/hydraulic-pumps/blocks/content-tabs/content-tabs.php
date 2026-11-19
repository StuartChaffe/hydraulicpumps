<?php
// Content Tabs: pill tabs that switch the panel below. Each Content Tab child
// block is a panel (a table, text etc.). On the front end the tab bar is built
// here from the children's labels; in the editor each child shows its own
// label as a tab instead (see content-tabs.scss).

/**
 * Passed in by ACF when it renders the block.
 *
 * @var bool          $is_preview True in the editor.
 * @var WP_Block|null $wp_block   The block instance, for reading the children.
 */
$is_preview = ! empty( $is_preview );
$wp_block   = $wp_block ?? null;

$allowed_blocks = array( 'acf/content-tab' );
$template       = array( array( 'acf/content-tab' ), array( 'acf/content-tab' ) );
$tabs           = array();

if ( ! $is_preview && $wp_block instanceof WP_Block ) {
	foreach ( $wp_block->parsed_block['innerBlocks'] ?? array() as $inner ) {
		if ( $inner['blockName'] !== 'acf/content-tab' ) {
			continue;
		}
		$label  = $inner['attrs']['data']['label'] ?? '';
		$label  = $label !== '' ? $label : 'Tab'; // Matches content-tab.php's fallback
		$tabs[] = array( 'label' => $label, 'id' => origin_tab_id( $label ) );
	}
}
?>
<section class="block content-tabs<?php echo $is_preview ? '' : ' content-tabs--front'; ?>">
	<div class="container container--content">
		<?php if ( count( $tabs ) > 1 ) : ?>
			<div class="content-tabs__list" role="tablist">
				<?php foreach ( $tabs as $i => $tab ) : ?>
					<button class="content-tabs__tab" type="button" role="tab" id="ctab-<?php echo esc_attr( $tab['id'] ); ?>" aria-controls="<?php echo esc_attr( $tab['id'] ); ?>" aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>"<?php echo $i === 0 ? '' : ' tabindex="-1"'; ?>><?php echo esc_html( $tab['label'] ); ?></button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<div class="content-tabs__items">
			<?php echo '<InnerBlocks allowedBlocks="' . esc_attr( wp_json_encode( $allowed_blocks ) ) . '" template="' . esc_attr( wp_json_encode( $template ) ) . '" />'; ?>
		</div>
	</div>
</section>
