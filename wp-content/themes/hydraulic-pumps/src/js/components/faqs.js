import mixitup from 'mixitup';

// FAQs block: the category tabs filter the FAQs with MixItUp. Each block gets
// its own mixer, driven through the API so tabs only control their own block.
// The URL hash can pick a tab (#category-slug) or open an FAQ (#faq-slug,
// switching to its first category tab).
document.addEventListener('DOMContentLoaded', () => {
	const hash = window.location.hash.slice(1);

	document.querySelectorAll('.faqs--tabs').forEach(block => {
		const list = block.querySelector('.faqs__list');
		const buttons = [...block.querySelectorAll('.faqs__filter')];
		const heading = block.querySelector('.faqs__heading');
		if (!list || !buttons.length) return;

		const setActive = button => {
			buttons.forEach(b => b.setAttribute('aria-pressed', b === button ? 'true' : 'false'));
			if (heading) heading.textContent = button.textContent;
		};

		// Starting tab: from the hash if it names one of this block's
		// categories or FAQs, otherwise the first
		let start = buttons[0];
		const target = hash ? document.getElementById(hash) : null;
		if (target && list.contains(target)) {
			start = buttons.find(b => target.matches(b.dataset.filter)) || start;
			target.open = true;
		} else if (hash) {
			start = buttons.find(b => b.dataset.slug === hash) || start;
		}

		const mixer = mixitup(list, {
			selectors: { target: '.mix' },
			controls: { enable: false }, // Tabs call mixer.filter() themselves
			animation: { duration: 200, effects: 'fade', reverseOut: false },
			load: { filter: start.dataset.filter },
		});

		setActive(start);

		buttons.forEach(button => {
			button.addEventListener('click', () => {
				setActive(button);
				mixer.filter(button.dataset.filter);
			});
		});

		if (target && list.contains(target)) {
			target.scrollIntoView({ block: 'start' });
		}
	});
});
