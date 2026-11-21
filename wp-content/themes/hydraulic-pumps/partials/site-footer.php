<?php
/**
 * Site footer: the footer menu (in two columns), contact details and address,
 * accreditation logos and the white logo, then the company number and copyright.
 * Everything is in Theme Options: the Footer tab, and the address in Contact Details.
 */
$contact        = get_field( 'footer_contact', 'option' );
$address        = get_field( 'address', 'option' );
$accreditations = get_field( 'accreditations', 'option' );
$company_name   = get_field( 'company_name', 'option' ) ?: get_bloginfo( 'name' );
$company_number = get_field( 'company_number', 'option' );
$links          = get_field( 'footer_links', 'option' );
$links_heading  = get_field( 'footer_links_heading', 'option' );
$contact_heading = get_field( 'footer_contact_heading', 'option' );
$accreditations_heading = get_field( 'footer_accreditations_heading', 'option' );
?>
<footer class="site-footer">
	<div class="site-footer__inner">

		<div class="site-footer__top">
			<?php if ( $links ) : ?>
				<nav class="site-footer__links"<?php echo $links_heading ? ' aria-labelledby="footer-links-heading"' : ' aria-label="' . esc_attr__( 'Footer', 'origin' ) . '"'; ?>>
					<?php if ( $links_heading ) : ?>
						<h2 class="site-footer__heading" id="footer-links-heading"><?php echo esc_html( $links_heading ); ?></h2>
					<?php endif; ?>
					<ul class="footer-nav__menu" role="list">
						<?php foreach ( $links as $row ) :
							$link = $row['link'] ?? null;
							if ( empty( $link['url'] ) ) continue;
							?>
							<li class="footer-nav__item"><a class="footer-nav__link" href="<?php echo esc_url( $link['url'] ); ?>"<?php echo ! empty( $link['target'] ) ? ' target="' . esc_attr( $link['target'] ) . '" rel="noopener"' : ''; ?>><?php echo esc_html( $link['title'] ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</nav>
			<?php endif; ?>

			<?php if ( $contact || $address ) : ?>
				<div class="site-footer__contact">
					<?php if ( $contact_heading ) : ?>
						<h2 class="site-footer__heading"><?php echo esc_html( $contact_heading ); ?></h2>
					<?php endif; ?>
					<div class="site-footer__contact-columns">
						<?php if ( $contact ) : ?>
							<div class="site-footer__text"><?php echo wp_kses_post( wpautop( $contact ) ); ?></div>
						<?php endif; ?>
						<?php if ( $address ) : ?>
							<address class="site-footer__address"><?php echo wp_kses_post( $address ); // The field adds the <br>s ?></address>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>

		<div class="site-footer__middle">
			<?php if ( $accreditations ) : ?>
				<div class="site-footer__accreditations">
					<?php if ( $accreditations_heading ) : ?>
						<h2 class="site-footer__heading"><?php echo esc_html( $accreditations_heading ); ?></h2>
					<?php endif; ?>
					<ul class="site-footer__logos" role="list">
						<?php foreach ( $accreditations as $logo_id ) : ?>
							<li><?php echo wp_get_attachment_image( $logo_id, 'medium', false, array( 'class' => 'site-footer__logo-image' ) ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<a class="site-footer__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/logo-white.png' ); ?>" width="469" height="196" alt="<?php echo esc_attr( get_bloginfo( 'name' ) . ' home' ); ?>">
			</a>
		</div>

		<div class="site-footer__bottom">
			<?php if ( $company_number ) : ?>
				<p><?php printf( esc_html__( 'Company Registration Number: %s', 'origin' ), esc_html( $company_number ) ); ?></p>
			<?php endif; ?>
			<p><?php printf( esc_html__( 'All content copyright © %1$s %2$s', 'origin' ), esc_html( $company_name ), esc_html( date( 'Y' ) ) ); ?></p>
		</div>

	</div>
</footer>
