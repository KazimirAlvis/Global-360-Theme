(() => {
	const nav = document.getElementById('site-navigation');
	if (!nav) {
		return;
	}

	const menuToggle = nav.querySelector('.menu-toggle');
	const menuClose = nav.querySelector('.menu-close');
	if (!menuToggle) {
		return;
	}

	const menuList = nav.getElementsByTagName('ul')[0];
	if (!menuList) {
		menuToggle.style.display = 'none';
		return;
	}

	if (!menuList.classList.contains('nav-menu')) {
		menuList.classList.add('nav-menu');
	}

	const parentItems = menuList.querySelectorAll(
		'.menu-item-has-children, .page_item_has_children'
	);
	const allLinks = menuList.getElementsByTagName('a');
	const submenuTriggers = menuList.querySelectorAll(
		'.menu-item-has-children > a, .page_item_has_children > a'
	);

	const desktopBreakpoint = window.matchMedia('(min-width: 1025px)');
	const mobileBreakpoint = window.matchMedia('(max-width: 1024px)');
	const isMobileView = () => mobileBreakpoint.matches;

	const hasExpandableChildren = (item) => {
		return (
			item &&
			item.classList &&
			(item.classList.contains('menu-item-has-children') ||
				item.classList.contains('page_item_has_children'))
		);
	};

	const isListItem = (node) => {
		return !!(node && node.tagName && node.tagName.toLowerCase() === 'li');
	};

	const getTrigger = (item) => {
		if (!item) {
			return null;
		}
		const firstChild = item.firstElementChild;
		if (!firstChild || firstChild.tagName.toLowerCase() !== 'a') {
			return null;
		}
		return firstChild;
	};

	const setExpandedState = (item, expanded) => {
		if (!hasExpandableChildren(item)) {
			return;
		}
		const trigger = getTrigger(item);
		if (!trigger) {
			return;
		}
		trigger.setAttribute('aria-haspopup', 'true');
		trigger.setAttribute('aria-expanded', expanded ? 'true' : 'false');
		item.classList.toggle('submenu-open', !!expanded);
	};

	for (const item of parentItems) {
		item.classList.add('has-submenu-toggle');
		const trigger = getTrigger(item);
		if (trigger) {
			trigger.classList.add('nav-submenu-trigger');
		}
	}

	const closeSubmenu = (item) => {
		if (!isListItem(item)) {
			return;
		}
		item.classList.remove('focus');
		setExpandedState(item, false);
	};

	const closeAllSubmenus = (exception = null) => {
		for (const parentItem of parentItems) {
			if (parentItem !== exception) {
				closeSubmenu(parentItem);
			}
		}
	};

	const toggleNavigation = (shouldOpen) => {
		nav.classList.toggle('toggled', shouldOpen);
		menuToggle.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
		menuToggle.classList.toggle('is-active', shouldOpen);
		document.body.classList.toggle('mobile-menu-open', shouldOpen);

		if (menuClose) {
			menuClose.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
			menuClose.setAttribute('aria-hidden', shouldOpen ? 'false' : 'true');
			menuClose.tabIndex = shouldOpen ? 0 : -1;
		}

		if (!shouldOpen) {
			closeAllSubmenus();
		}
	};

	toggleNavigation(false);

	menuToggle.addEventListener('click', () => {
		const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';
		toggleNavigation(!isExpanded);
	});

	if (menuClose) {
		menuClose.addEventListener('click', () => {
			toggleNavigation(false);
			menuToggle.focus();
		});
	}

	document.addEventListener('click', (event) => {
		if (nav.contains(event.target) || menuToggle.contains(event.target)) {
			return;
		}
		toggleNavigation(false);
	});

	document.addEventListener('keyup', (event) => {
		if (event.key === 'Escape') {
			toggleNavigation(false);
		}
	});

	const handleDesktopChange = (event) => {
		if (event.matches) {
			toggleNavigation(false);
		}
	};

	if (typeof desktopBreakpoint.addEventListener === 'function') {
		desktopBreakpoint.addEventListener('change', handleDesktopChange);
	} else if (typeof desktopBreakpoint.addListener === 'function') {
		desktopBreakpoint.addListener(handleDesktopChange);
	}

	const handleParentTriggerClick = (event) => {
		const item = event.currentTarget.parentNode;
		if (!item) {
			return;
		}

		event.preventDefault();
		const isExpanded = item.classList.contains('submenu-open');

		if (isMobileView()) {
			const nextState = !isExpanded;
			if (nextState) {
				closeAllSubmenus(item);
			} else {
				closeSubmenu(item);
			}
			setExpandedState(item, nextState);
			item.classList.toggle('focus', nextState);
			return;
		}

		closeAllSubmenus(item);
		setExpandedState(item, true);
		item.classList.add('focus');
	};

	for (const trigger of submenuTriggers) {
		trigger.setAttribute('aria-haspopup', 'true');
		trigger.setAttribute('aria-expanded', 'false');
		trigger.addEventListener('click', handleParentTriggerClick);
	}

	const handleLinkFocus = (event) => {
		if (isMobileView()) {
			return;
		}
		if (event.type !== 'focus' && event.type !== 'blur') {
			return;
		}
		let current = event.currentTarget;
		while (current && !current.classList.contains('nav-menu')) {
			if (isListItem(current)) {
				if (event.type === 'focus') {
					current.classList.add('focus');
				} else {
					current.classList.remove('focus');
				}
				setExpandedState(current, current.classList.contains('focus'));
			}
			current = current.parentNode;
		}
	};

	for (const link of allLinks) {
		link.addEventListener('focus', handleLinkFocus, true);
		link.addEventListener('blur', handleLinkFocus, true);
		link.addEventListener('click', (event) => {
			if (event.defaultPrevented) {
				return;
			}
			if (isMobileView()) {
				toggleNavigation(false);
			}
		});
	}
})();
