// Before & After block (blocks/before-after): compare two photos by dragging
// the handle (a range input over the photo, so it works with the keyboard and
// touch too). Before and After slide it all the way across. More than one pump
// is a Swiper carousel with dots; the photo itself doesn't swipe the carousel.
import Swiper from 'swiper';
import { Pagination, A11y } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/pagination';

const describe = value => {
	if (value >= 100) return 'All before';
	if (value <= 0) return 'All after';
	return `${value}% before`;
};

function initCompare(slide) {
	const compare = slide.querySelector('.before-after__compare');
	const range = slide.querySelector('.before-after__range');
	if (!compare || !range) return;

	const set = value => {
		range.value = value;
		compare.style.setProperty('--position', `${value}%`);
		range.setAttribute('aria-valuetext', describe(Number(value)));
	};

	set(range.value); // In step with the input, in case the browser restored its value

	range.addEventListener('input', () => {
		compare.classList.remove('is-animating'); // Dragging follows the pointer straight away
		set(range.value);
	});

	// Before / After: animate the handle all the way across
	slide.querySelectorAll('.before-after__show').forEach(button => {
		button.addEventListener('click', () => {
			compare.classList.add('is-animating');
			set(button.dataset.show === 'before' ? 100 : 0);
		});
	});
	compare.addEventListener('transitionend', () => compare.classList.remove('is-animating'));

	// (i): the panel about the repair, over the photo. Close or Escape returns to the photo.
	const info = slide.querySelector('.before-after__info');
	const story = info && document.getElementById(info.getAttribute('aria-controls'));
	if (info && story) {
		const toggle = open => {
			story.hidden = !open;
			info.setAttribute('aria-expanded', String(open));
			(open ? story : info).focus();
		};
		info.addEventListener('click', () => toggle(true));
		story.querySelector('.before-after__close')?.addEventListener('click', () => toggle(false));
		story.addEventListener('keydown', e => {
			if (e.key === 'Escape') toggle(false);
		});
	}
}

document.querySelectorAll('.before-after').forEach(block => {
	block.querySelectorAll('.before-after__slide').forEach(initCompare);

	const carousel = block.querySelector('.before-after__carousel.swiper');
	if (!carousel) return;

	new Swiper(carousel, {
		modules: [Pagination, A11y],
		slidesPerView: 1,
		spaceBetween: 30,
		autoHeight: true,
		wrapperClass: 'before-after__track',
		slideClass: 'before-after__slide',
		noSwipingClass: 'swiper-no-swiping', // The compare slider, so dragging it doesn't change pump
		pagination: {
			el: carousel.querySelector('.before-after__dots'),
			clickable: true,
			bulletClass: 'before-after__dot',
			bulletActiveClass: 'is-active',
		},
		a11y: {
			paginationBulletMessage: 'Show pump {{index}}',
		},
	});
});
