// Tabs block: tabs on desktop, an accordion on mobile (matching the medium
// breakpoint in SCSS). Desktop has one active tab; on mobile each accordion
// item opens and closes on its own. A URL hash matching a tab (e.g. #rail)
// opens that tab.
const desktop = window.matchMedia('(min-width: 50em)');

function initTabs(block) {
	const items = [...block.querySelectorAll('.tabs__item')];
	if (!items.length) return;

	const tabs = [...block.querySelectorAll('.tabs__tab')];
	const toggles = items.map(item => item.querySelector('.tabs__toggle'));
	const panels = items.map(item => item.querySelector('.tabs__panel'));

	const activate = (index, focus = false) => {
		items.forEach((item, i) => {
			const on = i === index;
			item.classList.toggle('is-active', on);
			if (tabs[i]) {
				tabs[i].setAttribute('aria-selected', on ? 'true' : 'false');
				tabs[i].tabIndex = on ? 0 : -1;
			}
		});
		if (focus) tabs[index]?.focus();
	};

	const setOpen = (index, open) => {
		items[index].classList.toggle('is-open', open);
		toggles[index]?.setAttribute('aria-expanded', open ? 'true' : 'false');
	};

	// Panels are tab panels on desktop and plain accordion regions on mobile
	const setMode = () => {
		panels.forEach((panel, i) => {
			if (desktop.matches && tabs[i]) {
				panel.setAttribute('role', 'tabpanel');
				panel.setAttribute('aria-labelledby', tabs[i].id);
			} else {
				panel.removeAttribute('role');
				if (toggles[i]) panel.setAttribute('aria-labelledby', toggles[i].id);
			}
		});
	};

	const openFromHash = () => {
		const index = items.findIndex(item => item.id === window.location.hash.slice(1));
		if (index === -1) return false;
		activate(index);
		setOpen(index, true);
		return true;
	};

	tabs.forEach((tab, i) => {
		tab.addEventListener('click', () => activate(i));
		tab.addEventListener('keydown', e => {
			const last = tabs.length - 1;
			const next = { ArrowRight: i === last ? 0 : i + 1, ArrowLeft: i === 0 ? last : i - 1, Home: 0, End: last }[e.key];
			if (next === undefined) return;
			e.preventDefault();
			activate(next, true);
		});
	});

	toggles.forEach((toggle, i) => {
		toggle?.addEventListener('click', () => setOpen(i, !items[i].classList.contains('is-open')));
	});

	if (!openFromHash()) {
		activate(0);
		setOpen(0, true);
	}
	setMode();
	desktop.addEventListener('change', setMode);
	window.addEventListener('hashchange', openFromHash);
	block.classList.add('is-ready');
}

document.addEventListener('DOMContentLoaded', () => {
	document.querySelectorAll('.tabs--front').forEach(initTabs);
});
