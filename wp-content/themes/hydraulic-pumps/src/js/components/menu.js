// Mobile menu: add a toggle button next to each parent item to open its sub-menu
jQuery(document).ready(function( $ ) {
	$( ".mobile-menu__nav .mobile-nav__item--parent > a" ).each(function(i) {
		const $link = $(this);
		const $submenu = $link.siblings('.mobile-nav__sub-menu');
		const submenuId = 'mobile-submenu-' + i;

		$submenu.attr('id', submenuId);

		const $toggle = $('<button>', {
			type: 'button',
			class: 'dropdown-button',
			'aria-expanded': 'false',
			'aria-controls': submenuId,
			'aria-label': 'Toggle ' + $link.text().trim() + ' submenu',
		}).append($('<span>', { 'aria-hidden': 'true', class: 'dropdown-button__icon' }));

		$link.after($toggle);
	});

	$('.dropdown-button').on('click', function() {
		const $button = $(this);
		const isActive = $button.hasClass('is-active');

		$button.toggleClass('is-active', !isActive);
		$button.attr('aria-expanded', String(!isActive));

		if (isActive) {
			$button.next('.mobile-nav__sub-menu').slideUp('normal');
		} else {
			$button.next('.mobile-nav__sub-menu').slideDown('normal');
		}
	});
});
