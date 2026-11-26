<?php
/**
 * Tabs: each Tab child block outputs its own accordion toggle and panel. On the
 * front end the tab bar is built here from the children's labels; in the editor
 * the children's toggles are laid out as the tabs instead (see tabs.scss).
 *
 * Passed in by ACF when it renders the block:
 *
 * @var array         $block      The block settings.
 * @var bool          $is_preview True in the editor.
 * @var WP_Block|null $wp_block   The block instance, for reading the Tab children.
 */
if ( origin_block_preview( $block ) ) return; // Inserter preview: show preview.jpg instead

$is_preview = ! empty( $is_preview );
$wp_block   = $wp_block ?? null;

$allowed_blocks = array( 'acf/tab' );
$template       = array( array( 'acf/tab' ), array( 'acf/tab' ) );
$tabs           = array();
$plain          = get_field('style') === 'plain'; // No images, stripes or fade (see tabs.scss)

if ( ! $is_preview && $wp_block instanceof WP_Block ) {
	foreach ( $wp_block->parsed_block['innerBlocks'] ?? array() as $inner ) {
		if ( $inner['blockName'] !== 'acf/tab' ) {
			continue;
		}
		$label  = $inner['attrs']['data']['label'] ?? '';
		$label  = $label !== '' ? $label : 'Tab'; // Matches tab.php's fallback
		$tabs[] = array( 'label' => $label, 'id' => origin_tab_id( $label ) );
	}
}
?>
<section class="block tabs<?php echo $plain ? ' tabs--plain' : ''; ?><?php echo $is_preview ? '' : ' tabs--front'; ?>">
	<div class="container<?php echo $plain ? ' container--content' : ''; ?>"><?php // Plain sits with the page content; the image style is wider ?>
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
