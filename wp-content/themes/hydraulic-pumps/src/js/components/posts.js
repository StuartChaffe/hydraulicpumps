import mixitup from 'mixitup';

// Posts block: category filter buttons and "Load more", animated by MixItUp.
// MixItUp's own pagination is a paid add-on, so paging is done here: the
// first N posts matching the filter get .is-shown, and MixItUp filters to
// those. "Load more" adds another N. A URL hash matching a category slug
// (e.g. #rail) starts on that category.
function initPosts(block) {
	const grid = block.querySelector('.posts__grid');
	if (!grid) return;

	const cards = [...grid.querySelectorAll('.mix')];
	const buttons = [...block.querySelectorAll('.posts__filter')];
	const more = block.querySelector('.posts__load-more');
	const perPage = parseInt(block.dataset.perPage, 10) || 6;

	let filter = '.mix';
	let limit = perPage;

	const hash = window.location.hash.slice(1);
	const start = hash && buttons.find(b => b.dataset.slug === hash);
	if (start) filter = start.dataset.filter;

	// Mark the posts to show, return how many match the filter in total
	const mark = () => {
		const matching = cards.filter(card => card.matches(filter));
		cards.forEach(card => card.classList.remove('is-shown'));
		matching.slice(0, limit).forEach(card => card.classList.add('is-shown'));
		if (more) more.hidden = matching.length <= limit;
		return matching;
	};

	const setPressed = button => {
		buttons.forEach(b => b.setAttribute('aria-pressed', b === button ? 'true' : 'false'));
	};

	mark();
	if (start) setPressed(start);

	const mixer = mixitup(grid, {
		selectors: { target: '.mix' },
		controls: { enable: false }, // The buttons call mixer.filter() themselves
		animation: { duration: 250, effects: 'fade translateY(10px)', reverseOut: false },
		load: { filter: '.is-shown' },
	});

	buttons.forEach(button => {
		button.addEventListener('click', () => {
			filter = button.dataset.filter === 'all' ? '.mix' : button.dataset.filter;
			limit = perPage;
			setPressed(button);
			mark();
			mixer.filter('.is-shown');
		});
	});

	more?.addEventListener('click', () => {
		const firstNew = mark()[limit];
		limit += perPage;
		mark();
		// Move focus to the first new post, so keyboard users carry on from there
		mixer.filter('.is-shown').then(() => firstNew?.querySelector('a')?.focus());
	});

	block.classList.add('is-ready');
}

document.addEventListener('DOMContentLoaded', () => {
	document.querySelectorAll('.posts').forEach(initPosts);
});
