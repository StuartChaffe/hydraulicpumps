<?php
$email   = get_field('email_address', 'options');
$phone   = get_field('phone_number', 'options');
$address = get_field('address', 'options');
?>
<footer class="site-footer">
	<div class="site-footer__inner">

		<div class="site-footer__top">

			<?php if ($email || $phone || $address) : ?>
			<address class="site-footer__contact">
				<?php if ($email) : ?>
				<a class="site-footer__email" href="mailto:<?php echo esc_attr($email); ?>"><span>Email:</span> <?php echo esc_html($email); ?></a>
				<?php endif; ?>
				<?php if ($phone) : ?>
				<a class="site-footer__phone" href="tel:<?php echo esc_attr(preg_replace('/\s+/', '', $phone)); ?>"><span>Tel:</span> <?php echo esc_html($phone); ?></a>
				<?php endif; ?>
				<?php if ($address) : ?>
				<p class="site-footer__address"><?php echo wp_kses_post($address); ?></p>
				<?php endif; ?>
			</address>
			<?php endif; ?>

			<?php wp_nav_menu([
				'theme_location' => 'footer',
				'container'      => 'nav',
				'container_class'=> 'footer-nav',
				'items_wrap'     => '<ul class="footer-nav__menu">%3$s</ul>',
				'walker'         => new BEM_Nav_Walker('footer-nav'),
				'fallback_cb'    => false,
			]); ?>

		</div>

		<div class="site-footer__bottom">
			<p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>.</p>
			<?php get_template_part('partials/social-links', null, ['modifier_classes' => 'social-links--footer']); ?>
		</div>

	</div>
</footer>
