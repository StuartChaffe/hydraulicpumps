<?php
/** @var array $block Block settings, passed in by ACF. */
if ( origin_block_preview( $block ) ) return; // Inserter preview: show preview.jpg instead
$allowed_blocks = array( 'core/post-title', 'core/heading', 'core/paragraph', 'core/buttons', 'core/list', 'core/image', 'acf/block-list');
$size             = get_field('size') === 'small' ? 'small' : 'large';
// Images and video are only used by the large size (the fields are hidden for small)
$background_image = $size === 'large' ? get_field('background_image') : null;
$image            = $size === 'large' ? get_field('image') : null;
$video            = $size === 'large' ? page_header_vimeo( get_field('video_url') ) : null;
$video_text       = get_field('video_text') ?: __( 'Watch our video', 'origin' );
?>
<section class="block page-header page-header--<?php echo esc_attr( $size ); ?><?php echo $image ? ' page-header--has-media' : ''; ?>">
	<?php if ( $background_image ) : ?>
		<?php echo wp_get_attachment_image( $background_image, 'full', false, array( 'class' => 'page-header__background', 'alt' => '', 'sizes' => '100vw' ) ); ?>
	<?php endif; ?>
	<?php // Small is a centred container; large spans the header, content left and image right ?>
	<div class="page-header__inner<?php echo $size === 'small' ? ' container' : ''; ?><?php echo $background_image ? '' : ' f-center-align'; ?>">
		<div class="page-header__content">
			<?php if ( is_admin() || ! empty( $content ) ) : ?><?php echo '<InnerBlocks allowedBlocks="' . esc_attr( wp_json_encode( $allowed_blocks ) ) . '" />'; ?><?php endif; ?>
			<?php if ( $video ) : ?>
				<?php // Links to the video on Vimeo; scripts open it in a lightbox instead (see page-header.js) ?>
				<a class="page-header__video-link" href="<?php echo esc_url( 'https://vimeo.com/' . $video['id'] ); ?>" data-vimeo-id="<?php echo esc_attr( $video['id'] ); ?>" data-vimeo-title="<?php echo esc_attr( $video['title'] ); ?>">
					<span class="page-header__video-icon" aria-hidden="true"><svg viewBox="0 0 14 20" focusable="false"><path d="M0 0v20l14-10z" fill="currentColor"/></svg></span>
					<span class="page-header__video-text"><?php echo esc_html( $video_text ); ?></span>
				</a>
				<dialog class="video-lightbox" aria-label="<?php echo esc_attr( $video['title'] ); ?>">
					<button class="video-lightbox__close" type="button">
						<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/close.svg' ); ?>" width="42" height="42" alt="<?php esc_attr_e( 'Close video', 'origin' ); ?>">
					</button>
					<div class="video-lightbox__frame"></div>
				</dialog>
			<?php endif; ?>
		</div>
		<?php if ( $image ) : ?>
			<div class="page-header__media">
				<?php echo wp_get_attachment_image( $image, 'large', false, array( 'class' => 'page-header__image', 'sizes' => '(min-width: 50em) 50vw, 100vw' ) ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
