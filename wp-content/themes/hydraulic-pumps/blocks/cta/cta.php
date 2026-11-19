<?php
$allowed_blocks = array( 'core/heading', 'core/paragraph', 'core/buttons', 'core/list' );
$template       = array(
	array( 'core/heading', array( 'level' => 2, 'textAlign' => 'center', 'fontSize' => 'x-large', 'placeholder' => 'Heading' ) ),
	array( 'core/paragraph', array( 'align' => 'center', 'fontSize' => 'large', 'placeholder' => 'Content' ) ),
	array( 'core/buttons', array( 'layout' => array( 'type' => 'flex', 'justifyContent' => 'center' ) ), array(
		array( 'core/button' ),
	) ),
);
$background     = get_field('background') === 'navy' ? 'navy' : 'mask';
?>
<section class="block cta cta--<?php echo esc_attr( $background ); ?>">
	<div class="container container--content">
		<div class="cta__panel">
			<?php if ( is_admin() || ! empty( $content ) ) : ?><?php echo '<InnerBlocks allowedBlocks="' . esc_attr( wp_json_encode( $allowed_blocks ) ) . '" template="' . esc_attr( wp_json_encode( $template ) ) . '" />'; ?><?php endif; ?>
		</div>
	</div>
</section>
