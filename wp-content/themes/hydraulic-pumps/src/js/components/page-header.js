// Page Header block (blocks/page-header): the "Watch our video" link goes to
// the video on Vimeo. With scripts, it opens the Vimeo player in the block's
// lightbox instead (a native <dialog>, so focus, Escape and the backdrop come free).
document.querySelectorAll('.video-lightbox').forEach(dialog => {
	dialog.querySelector('.video-lightbox__close')?.addEventListener('click', () => dialog.close());
	// A click on the backdrop lands on the dialog itself
	dialog.addEventListener('click', e => { if (e.target === dialog) dialog.close(); });
	// Remove the player on close so the video stops
	dialog.addEventListener('close', () => dialog.querySelector('.video-lightbox__frame').replaceChildren());
});

document.addEventListener('click', e => {
	const link = e.target.closest('.page-header__video-link');
	const dialog = link?.parentElement.querySelector('.video-lightbox');
	if (!link?.dataset.vimeoId || typeof dialog?.showModal !== 'function') return;

	e.preventDefault();

	const iframe = document.createElement('iframe');
	iframe.src = `https://player.vimeo.com/video/${link.dataset.vimeoId}?autoplay=1&dnt=1&title=0&byline=0&portrait=0`;
	iframe.title = link.dataset.vimeoTitle || 'Video';
	iframe.allow = 'autoplay; fullscreen; picture-in-picture';
	iframe.allowFullscreen = true;

	dialog.querySelector('.video-lightbox__frame').replaceChildren(iframe);
	dialog.showModal();
});
