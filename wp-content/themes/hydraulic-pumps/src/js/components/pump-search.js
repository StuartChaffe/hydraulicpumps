// Pump search (partials/pump-search.php): suggests matching pump names as you
// type, from /wp-json/hp/v1/pump-suggestions. An ARIA combobox: up and down
// move through the suggestions, Enter or a click picks one and searches,
// Escape closes the list.
const MIN_LENGTH = 2;
const DELAY = 200; // ms after the last key press

function initPumpSearch(form) {
	const input = form.querySelector('.pump-search__input');
	const list = form.querySelector('.pump-search__suggestions');
	const status = form.querySelector('.pump-search__status');
	const endpoint = form.dataset.suggest;
	if (!input || !list || !endpoint) return;

	let options = [];
	let active = -1;
	let timer;
	let controller;
	const cache = new Map();

	const close = () => {
		list.hidden = true;
		input.setAttribute('aria-expanded', 'false');
		input.removeAttribute('aria-activedescendant');
		active = -1;
	};

	const highlight = index => {
		options.forEach((option, i) => option.setAttribute('aria-selected', i === index ? 'true' : 'false'));
		active = index;
		if (index === -1) {
			input.removeAttribute('aria-activedescendant');
		} else {
			input.setAttribute('aria-activedescendant', options[index].id);
			options[index].scrollIntoView({ block: 'nearest' });
		}
	};

	const choose = name => {
		input.value = name;
		close();
		form.requestSubmit(); // Runs the submit handler, so the in-page search below applies
	};

	const render = pumps => {
		list.replaceChildren();
		options = pumps.map((pump, i) => {
			const option = document.createElement('li');
			option.id = `${list.id}-${i}`;
			option.className = 'pump-search__suggestion';
			option.setAttribute('role', 'option');
			option.setAttribute('aria-selected', 'false');

			const name = document.createElement('span');
			name.className = 'pump-search__suggestion-name';
			name.textContent = pump.name;
			option.append(name);

			if (pump.brand) {
				const brand = document.createElement('span');
				brand.className = 'pump-search__suggestion-brand';
				brand.textContent = pump.brand;
				option.append(brand);
			}

			// mousedown, not click, so it fires before the input's blur closes the list
			option.addEventListener('mousedown', e => {
				e.preventDefault();
				choose(pump.name);
			});
			list.append(option);
			return option;
		});

		active = -1;
		input.removeAttribute('aria-activedescendant');
		const open = options.length > 0;
		list.hidden = !open;
		input.setAttribute('aria-expanded', open ? 'true' : 'false');
		status.textContent = open
			? `${options.length} matching ${options.length === 1 ? 'pump' : 'pumps'}. Use the up and down arrows to choose.`
			: '';
	};

	const suggest = async () => {
		const term = input.value.trim();
		if (term.length < MIN_LENGTH) {
			close();
			status.textContent = '';
			return;
		}

		const key = term.toUpperCase();
		if (cache.has(key)) {
			render(cache.get(key));
			return;
		}

		controller?.abort(); // Only the latest search counts
		controller = new AbortController();
		try {
			const url = new URL(endpoint, window.location.origin);
			url.searchParams.set('search', term);
			const response = await fetch(url, { signal: controller.signal });
			if (!response.ok) return;
			const pumps = await response.json();
			cache.set(key, pumps);
			if (input.value.trim() === term) render(pumps);
		} catch (error) {
			if (error.name !== 'AbortError') close(); // No suggestions: the search still works
		}
	};

	input.addEventListener('input', () => {
		clearTimeout(timer);
		timer = setTimeout(suggest, DELAY);
	});

	input.addEventListener('keydown', e => {
		const open = !list.hidden && options.length;

		if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
			if (!open) {
				suggest(); // Reopen (from the cache if it's the same search)
				return;
			}
			e.preventDefault();
			const last = options.length - 1;
			const next = e.key === 'ArrowDown'
				? (active >= last ? 0 : active + 1)
				: (active <= 0 ? last : active - 1);
			highlight(next);
		} else if (e.key === 'Enter' && open && active > -1) {
			e.preventDefault();
			choose(options[active].querySelector('.pump-search__suggestion-name').textContent);
		} else if (e.key === 'Escape' && open) {
			e.preventDefault();
			close();
		}
	});

	input.addEventListener('blur', close);
	form.addEventListener('submit', close);

	// On the pump finder page itself, search in place: fetch the results page
	// and swap in its answer, so the page doesn't reload and jump. The address
	// updates so the result can be shared, and back/forward still work.
	const results = form.closest('.pump-finder')?.querySelector('.pump-finder__results');
	if (!results) return;

	const show = async (term, push) => {
		const url = new URL(form.action);
		url.hash = '';
		url.searchParams.set(input.name, term);
		form.setAttribute('aria-busy', 'true');
		try {
			const response = await fetch(url);
			if (!response.ok) throw new Error(response.status);
			const page = new DOMParser().parseFromString(await response.text(), 'text/html');
			const answer = page.querySelector('.pump-finder__results');
			if (!answer) throw new Error('No results');
			results.replaceChildren(...answer.childNodes);
			if (push) history.pushState({ pump: term }, '', url);
		} catch {
			window.location.href = url; // Fall back to loading the page
		} finally {
			form.removeAttribute('aria-busy');
		}
	};

	form.addEventListener('submit', e => {
		const term = input.value.trim();
		if (!term) return;
		e.preventDefault();
		show(term, true);
	});

	window.addEventListener('popstate', () => {
		const term = new URL(window.location.href).searchParams.get(input.name) || '';
		input.value = term;
		if (term) {
			show(term, false);
		} else {
			results.replaceChildren();
		}
	});
}

document.querySelectorAll('.pump-search').forEach(initPumpSearch);
