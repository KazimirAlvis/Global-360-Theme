(() => {
	const e = document.getElementById('site-navigation');
	if (!e) return;
	const t = e.querySelector('.menu-toggle'),
		n = e.querySelector('.menu-close');
	if (!t) return;
	const o = e.getElementsByTagName('ul')[0];
	if (!o) {
		t.style.display = 'none';
		return;
	}
	o.classList.contains('nav-menu') || o.classList.add('nav-menu');
	const s = (a) => {
		e.classList.toggle('toggled', a),
			t.setAttribute('aria-expanded', a ? 'true' : 'false'),
			t.classList.toggle('is-active', a),
			document.body.classList.toggle('mobile-menu-open', a),
			n &&
				(n.setAttribute('aria-expanded', a ? 'true' : 'false'),
				n.setAttribute('aria-hidden', a ? 'false' : 'true'),
				(n.tabIndex = a ? 0 : -1));
	};
	s(!1),
		t.addEventListener('click', () => {
			s('true' !== t.getAttribute('aria-expanded'));
		}),
		n &&
			n.addEventListener('click', () => {
				s(!1), t.focus();
			}),
		document.addEventListener('click', (a) => {
			e.contains(a.target) || t.contains(a.target) || s(!1);
		}),
		document.addEventListener('keyup', (a) => {
			'Escape' === a.key && s(!1);
		});
	const c = window.matchMedia('(min-width: 1025px)');
	function i(a) {
		a.matches && s(!1);
	}
	'function' == typeof c.addEventListener
		? c.addEventListener('change', i)
		: 'function' == typeof c.addListener && c.addListener(i);
	const d = o.getElementsByTagName('a'),
		l = o.querySelectorAll('.menu-item-has-children > a, .page_item_has_children > a'),
		r = (a) => {
			if ('focus' === a.type || 'blur' === a.type) {
				let u = a.currentTarget;
				for (; u && !u.classList.contains('nav-menu'); )
					'li' === u.tagName.toLowerCase() && u.classList.toggle('focus'), (u = u.parentNode);
			}
			if ('touchstart' === a.type) {
				const u = a.currentTarget.parentNode;
				a.preventDefault();
				for (const f of u.parentNode.children) f !== u && f.classList.remove('focus');
				u.classList.toggle('focus');
			}
		};
	for (const a of d)
		a.addEventListener('focus', r, !0), a.addEventListener('blur', r, !0), a.addEventListener('click', () => s(!1));
	for (const a of l) a.addEventListener('touchstart', r, !1);
})();
