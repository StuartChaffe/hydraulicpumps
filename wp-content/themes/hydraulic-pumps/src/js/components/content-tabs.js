// Content Tabs block: pill tabs that switch the panel below. Arrow keys,
// Home and End move between tabs. A URL hash matching a tab (e.g. #parker)
// opens that tab.
function initContentTabs(block) {
	const items = [...block.querySelectorAll('.content-tabs__item')];
	const tabs = [...block.querySelectorAll('.content-tabs__tab')];
	if (!items.length) return;

	// A single panel has no tab bar, so it isn't a tab panel
	if (!tabs.length) {
		items.forEach(item => {
			item.removeAttribute('role');
			item.removeAttribute('aria-labelledby');
		});
	}

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

	const openFromHash = () => {
		const index = items.findIndex(item => item.id === window.location.hash.slice(1));
		if (index === -1) return false;
		activate(index);
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

	if (!openFromHash()) activate(0);
	window.addEventListener('hashchange', openFromHash);
	block.classList.add('is-ready');
}

document.addEventListener('DOMContentLoaded', () => {
	document.querySelectorAll('.content-tabs--front').forEach(initContentTabs);
});
