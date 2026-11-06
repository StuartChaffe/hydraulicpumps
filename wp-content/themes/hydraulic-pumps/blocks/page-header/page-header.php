<?php
$allowed_blocks = array( 'core/heading', 'core/paragraph', 'core/buttons', 'core/list', 'core/image', 'acf/block-list');
$size             = get_field('size') === 'small' ? 'small' : 'large';
// Images are only used by the large size (the fields are hidden for small)
$background_image = $size === 'large' ? get_field('background_image') : null;
$image            = $size === 'large' ? get_field('image') : null;
?>
<section class="block page-header page-header--<?php echo esc_attr( $size ); ?><?php echo $image ? ' page-header--has-media' : ''; ?>">
	<?php if ( $background_image ) : ?>
		<?php echo wp_get_attachment_image( $background_image, 'full', false, array( 'class' => 'page-header__background', 'alt' => '', 'sizes' => '100vw' ) ); ?>
	<?php endif; ?>
	<?php // Small is a centred container; large spans the header, content left and image right ?>
	<div class="page-header__inner<?php echo $size === 'small' ? ' container' : ''; ?><?php echo $background_image ? '' : ' f-center-align'; ?>">
		<div class="page-header__content">
			<?php if ( is_admin() || ! empty( $content ) ) : ?><?php echo '<InnerBlocks allowedBlocks="' . esc_attr( wp_json_encode( $allowed_blocks ) ) . '" />'; ?><?php endif; ?>
		</div>
		<?php if ( $image ) : ?>
			<div class="page-header__media">
				<?php echo wp_get_attachment_image( $image, 'large', false, array( 'class' => 'page-header__image', 'sizes' => '(min-width: 50em) 50vw, 100vw' ) ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
