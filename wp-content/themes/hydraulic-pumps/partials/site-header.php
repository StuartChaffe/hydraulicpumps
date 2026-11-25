<header class="site-header">
	<div class="site-header__inner">
		<a class="site-header__logo" href="<?php echo home_url(); ?>" aria-label="Home">
			<img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.png" alt="">
		</a>
		<div class="site-header__strap"><p class="heading-2"><strong>Keeping the heart of your plant moving</strong></p></div>
		<div class="site-header__actions">
			<nav class="site-header__secondary-nav">
				<?php wp_nav_menu( array(
					'theme_location' => 'secondary',
					'container'      => false,
					'menu_class'     => 'secondary-nav',
					'items_wrap'     => '<ul class="%2$s">%3$s</ul>',
					'walker'         => new BEM_Nav_Walker( 'secondary-nav' ),
				) ); ?>
			</nav>
			<div class="btn-group">
				<?php // The text is hidden on mobile, leaving just the icons (see _header.scss) ?>
				<a href="mailto:sales@hydraulicpumps.co.uk" class="btn btn--email"><span class="btn__text">sales@hydraulicpumps.co.uk</span></a>
				<a href="tel:+441709360370" class="btn btn--phone"><span class="btn__text">01709 360370</span></a>
			</div>
		</div>
		<button class="site-header__menu-btn hamburger" data-toggle="#mobile-menu" data-toggle-body-class="nav-open" aria-label="Toggle mobile menu" aria-expanded="false" aria-controls="mobile-menu">
			<span class="hamburger-box">
				<span class="hamburger-inner"></span>
			</span>
		</button>
	</div>
	<nav class="site-header__nav">
		<?php wp_nav_menu( array(
			'theme_location' => 'primary',
			'container'      => false,
			'menu_class'     => 'primary-nav',
			'items_wrap'     => '<ul class="%2$s">%3$s</ul>',
			'walker'         => new BEM_Nav_Walker( 'primary-nav' ),
		) ); ?>
	</nav>
</header>

<div class="mobile-menu" id="mobile-menu">
	<nav class="mobile-menu__nav">
		<?php wp_nav_menu( array(
			'theme_location' => 'primary',
			'container'      => false,
			'menu_class'     => 'mobile-nav',
			'items_wrap'     => '<ul class="%2$s">%3$s</ul>',
			'walker'         => new BEM_Nav_Walker( 'mobile-nav' ),
		) ); ?>
		<div class="mobile-menu__extras">
			<?php wp_nav_menu( array(
				'theme_location' => 'secondary',
				'container'      => false,
				'menu_class'     => 'secondary-nav',
				'items_wrap'     => '<ul class="%2$s">%3$s</ul>',
				'walker'         => new BEM_Nav_Walker( 'secondary-nav' ),
			) ); ?>
			<?php // Email and phone are in the header itself on mobile, as icons ?>
		</div>
	</nav>
</div>
