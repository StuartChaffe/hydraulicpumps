<?php
// Split Intro: the first heading sits on the left, everything after it on the
// right (see split-intro.scss). Plain InnerBlocks, so the heading keeps the
// usual formatting, e.g. a red word.
$allowed_blocks = array( 'core/heading', 'core/paragraph', 'core/list', 'core/buttons' );
$template       = array(
	array( 'core/heading', array( 'level' => 2, 'fontSize' => 'x-large', 'placeholder' => 'Heading' ) ),
	array( 'core/paragraph', array( 'placeholder' => 'Text' ) ),
);
?>
<section class="block split-intro">
	<div class="container split-intro__inner">
		<?php echo '<InnerBlocks allowedBlocks="' . esc_attr( wp_json_encode( $allowed_blocks ) ) . '" template="' . esc_attr( wp_json_encode( $template ) ) . '" />'; ?>
	</div>
</section>
