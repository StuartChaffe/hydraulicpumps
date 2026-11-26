// Tables (the core Table block): on small screens a table is wider than the
// screen and scrolls sideways (see _tables.scss). Mark which ends have more
// table to scroll to, so the CSS can fade those edges as a hint.
const update = frame => {
	const max = frame.scrollWidth - frame.clientWidth;
	frame.classList.toggle('has-more-start', frame.scrollLeft > 1);
	frame.classList.toggle('has-more-end', max - frame.scrollLeft > 1);
};

const frames = [...document.querySelectorAll('.wp-block-table')];

frames.forEach(frame => {
	update(frame);
	frame.addEventListener('scroll', () => update(frame), { passive: true });
});

// Again when the screen size changes (a table may start or stop scrolling)
const observer = new ResizeObserver(entries => entries.forEach(entry => update(entry.target)));
frames.forEach(frame => observer.observe(frame));

// And once fonts and images have loaded, which can change a table's width
window.addEventListener('load', () => frames.forEach(update));
