<?php
/** @var array $block Block settings, passed in by ACF. */
if ( origin_block_preview( $block ) ) return; // Inserter preview: show preview.jpg instead
$is_pump_finder = get_field('style') === 'pump_finder';
$background     = ! $is_pump_finder && get_field('background') === 'navy' ? 'navy' : 'mask';
$full           = get_field('width') === 'full' ? ' cta--full' : ''; // Standard (the default) changes nothing

if ( $is_pump_finder ) :
	// The content is set once for the site (Theme Settings > Pump finder). A page
	// can change it with the block's "Change the content on this page" switch;
	// any field it leaves empty still uses the site-wide content.
	$custom = (bool) get_field('pf_custom');
	$field  = function ( $name ) use ( $custom ) {
		$value = $custom ? get_field( $name ) : '';
		return $value ?: get_field( 'pump_finder_cta_' . substr( $name, 3 ), 'option' );
	};
	$title       = $field('pf_title');
	$intro       = $field('pf_intro');
	$placeholder = $field('pf_placeholder'); // Empty: the form's own default
	$strip_text  = $field('pf_strip_text');
	$wa_label    = $field('pf_whatsapp_label');
	$wa_number   = $field('pf_whatsapp_number');
	$qr_code     = $field('pf_qr_code');
	$search_id   = 'cta-search-' . ( $block['id'] ?? wp_unique_id() );

	// wa.me needs the international number: digits only, with 44 in place of the leading 0
	$wa_digits = preg_replace( '/\D/', '', (string) $wa_number );
	$wa_url    = $wa_digits ? 'https://wa.me/' . preg_replace( '/^0/', '44', $wa_digits ) : '';
	?>
<section class="block cta cta--mask cta--pump-finder<?php echo $full; ?>">
	<div class="container container--content">
		<div class="cta__panel">
			<div class="cta__main">
				<?php if ( $title ) : ?>
					<h2 class="cta__title"><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>
				<?php if ( $intro ) : ?>
					<p class="cta__intro"><?php echo esc_html( $intro ); ?></p>
				<?php endif; ?>

				<?php get_template_part( 'partials/pump-search', null, array( 'placeholder' => $placeholder, 'id' => $search_id ) ); ?>
			</div>

			<?php if ( $strip_text || $wa_url ) : ?>
				<div class="cta__strip">
					<?php if ( $strip_text ) : ?>
						<div class="cta__strip-text"><?php echo wp_kses_post( wpautop( $strip_text ) ); ?></div>
					<?php endif; ?>

					<?php if ( $wa_url ) : ?>
						<a class="cta__whatsapp" href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener">
							<img class="cta__whatsapp-icon" src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/whatsapp.svg' ); ?>" width="52" height="52" alt="">
							<span class="cta__whatsapp-text">
								<?php if ( $wa_label ) : ?><span><?php echo esc_html( $wa_label ); ?></span><?php endif; ?>
								<span><?php echo esc_html( $wa_number ); ?></span>
								<span class="sr-only">on WhatsApp (opens in a new tab)</span>
							</span>
							<?php if ( $qr_code ) : ?>
								<?php echo wp_get_attachment_image( $qr_code, 'medium', false, array( 'class' => 'cta__qr', 'alt' => '' ) ); ?>
							<?php else : ?>
								<img class="cta__qr" src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/whatsapp-qr.png' ); ?>" width="448" height="460" alt="">
							<?php endif; ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php
	return;
endif;

$allowed_blocks = array( 'core/heading', 'core/paragraph', 'core/buttons', 'core/list' );
$template       = array(
	array( 'core/heading', array( 'level' => 2, 'textAlign' => 'center', 'fontSize' => 'x-large', 'placeholder' => 'Heading' ) ),
	array( 'core/paragraph', array( 'align' => 'center', 'fontSize' => 'large', 'placeholder' => 'Content' ) ),
	array( 'core/buttons', array( 'layout' => array( 'type' => 'flex', 'justifyContent' => 'center' ) ), array(
		array( 'core/button' ),
	) ),
);
?>
<section class="block cta cta--<?php echo esc_attr( $background ); ?><?php echo $full; ?>">
	<div class="container container--content">
		<div class="cta__panel">
			<?php if ( is_admin() || ! empty( $content ) ) : ?><?php echo '<InnerBlocks allowedBlocks="' . esc_attr( wp_json_encode( $allowed_blocks ) ) . '" template="' . esc_attr( wp_json_encode( $template ) ) . '" />'; ?><?php endif; ?>
		</div>
	</div>
</section>
