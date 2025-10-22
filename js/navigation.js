/**
 * File navigation.js.
 *
 * Handles toggling the navigation menu for small screens and enables TAB key
 * navigation support for dropdown menus.
 */
( function() {
	const siteNavigation = document.getElementById( 'site-navigation' );

	// Return early if the navigation doesn't exist.
	if ( ! siteNavigation ) {
		return;
	}

	const button = siteNavigation.getElementsByTagName( 'button' )[ 0 ];

	// Return early if the button doesn't exist.
	if ( 'undefined' === typeof button ) {
		return;
	}

	const menu = siteNavigation.getElementsByTagName( 'ul' )[ 0 ];

	// Hide menu toggle button if menu is empty and return early.
	if ( 'undefined' === typeof menu ) {
		button.style.display = 'none';
		return;
	}

	if ( ! menu.classList.contains( 'nav-menu' ) ) {
		menu.classList.add( 'nav-menu' );
	}

	const setMenuState = ( isOpen ) => {
		siteNavigation.classList.toggle( 'toggled', isOpen );
		button.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
		button.classList.toggle( 'is-active', isOpen );
		document.body.classList.toggle( 'mobile-menu-open', isOpen );
	};

	// Toggle the .toggled class and the aria-expanded value each time the button is clicked.
	button.addEventListener( 'click', function() {
		const isExpanded = button.getAttribute( 'aria-expanded' ) === 'true';
		setMenuState( ! isExpanded );
	} );

	// Remove the .toggled class and set aria-expanded to false when the user clicks outside the navigation.
	document.addEventListener( 'click', function( event ) {
		const isClickInside = siteNavigation.contains( event.target ) || button.contains( event.target );

		if ( ! isClickInside ) {
			setMenuState( false );
		}
	} );

	// Close on ESC key.
	document.addEventListener( 'keyup', function( event ) {
		if ( event.key === 'Escape' ) {
			setMenuState( false );
		}
	} );

	// Ensure menu closes on viewport expansion to desktop.
	const viewportBreakpoint = window.matchMedia( '(min-width: 1025px)' );
	const handleViewportChange = ( mq ) => {
		if ( mq.matches ) {
			setMenuState( false );
		}
	};
	if ( typeof viewportBreakpoint.addEventListener === 'function' ) {
		viewportBreakpoint.addEventListener( 'change', handleViewportChange );
	} else if ( typeof viewportBreakpoint.addListener === 'function' ) {
		viewportBreakpoint.addListener( handleViewportChange );
	}

	// Get all the link elements within the menu.
	const links = menu.getElementsByTagName( 'a' );

	// Get all the link elements with children within the menu.
	const linksWithChildren = menu.querySelectorAll( '.menu-item-has-children > a, .page_item_has_children > a' );

	// Toggle focus each time a menu link is focused or blurred.
	for ( const link of links ) {
		link.addEventListener( 'focus', toggleFocus, true );
		link.addEventListener( 'blur', toggleFocus, true );
		link.addEventListener( 'click', function() {
			setMenuState( false );
		} );
	}

	// Toggle focus each time a menu link with children receive a touch event.
	for ( const link of linksWithChildren ) {
		link.addEventListener( 'touchstart', toggleFocus, false );
	}

	/**
	 * Sets or removes .focus class on an element.
	 */
	function toggleFocus() {
		if ( event.type === 'focus' || event.type === 'blur' ) {
			let self = this;
			// Move up through the ancestors of the current link until we hit .nav-menu.
			while ( ! self.classList.contains( 'nav-menu' ) ) {
				// On li elements toggle the class .focus.
				if ( 'li' === self.tagName.toLowerCase() ) {
					self.classList.toggle( 'focus' );
				}
				self = self.parentNode;
			}
		}

		if ( event.type === 'touchstart' ) {
			const menuItem = this.parentNode;
			event.preventDefault();
			for ( const link of menuItem.parentNode.children ) {
				if ( menuItem !== link ) {
					link.classList.remove( 'focus' );
				}
			}
			menuItem.classList.toggle( 'focus' );
		}
	}
}() );
