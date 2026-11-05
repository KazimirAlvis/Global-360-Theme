(function () {
	'use strict';

	document.querySelectorAll('[data-linktree-v2-carousel]').forEach(function (carousel) {
		var slides = Array.prototype.slice.call(carousel.querySelectorAll('[data-linktree-v2-slide]'));
		var dots = Array.prototype.slice.call(carousel.querySelectorAll('[data-linktree-v2-dot]'));
		var track = carousel.querySelector('.linktree-v2-carousel__slides');
		var previous = carousel.querySelector('[data-linktree-v2-previous]');
		var next = carousel.querySelector('[data-linktree-v2-next]');
		var status = carousel.querySelector('[data-linktree-v2-status]');
		var current = 0;
		var touchStartX = null;

		if (slides.length < 2) {
			return;
		}

		function showSlide(index, announce) {
			current = (index + slides.length) % slides.length;
			slides.forEach(function (slide, slideIndex) {
				var active = slideIndex === current;
				slide.hidden = false;
				slide.setAttribute('aria-hidden', active ? 'false' : 'true');
				slide.querySelectorAll('a, button, input, select, textarea, [tabindex]').forEach(function (control) {
					if (!active) {
						if (control.hasAttribute('tabindex')) {
							control.dataset.linktreeV2Tabindex = control.getAttribute('tabindex');
						}
						control.setAttribute('tabindex', '-1');
					} else if (control.dataset.linktreeV2Tabindex !== undefined) {
						control.setAttribute('tabindex', control.dataset.linktreeV2Tabindex);
						delete control.dataset.linktreeV2Tabindex;
					} else {
						control.removeAttribute('tabindex');
					}
				});
			});
			track.style.transform = 'translate3d(-' + (current * 100) + '%, 0, 0)';
			dots.forEach(function (dot, dotIndex) {
				dot.setAttribute('aria-current', dotIndex === current ? 'true' : 'false');
			});
			if (announce && status) {
				status.textContent = 'Slide ' + (current + 1) + ' of ' + slides.length;
			}
		}

		previous.addEventListener('click', function () {
			showSlide(current - 1, true);
		});
		next.addEventListener('click', function () {
			showSlide(current + 1, true);
		});
		dots.forEach(function (dot) {
			dot.addEventListener('click', function () {
				showSlide(parseInt(dot.dataset.linktreeV2Dot, 10), true);
			});
		});

		carousel.addEventListener('keydown', function (event) {
			if (event.key === 'ArrowLeft') {
				event.preventDefault();
				showSlide(current - 1, true);
			} else if (event.key === 'ArrowRight') {
				event.preventDefault();
				showSlide(current + 1, true);
			} else if (event.key === 'Home') {
				event.preventDefault();
				showSlide(0, true);
			} else if (event.key === 'End') {
				event.preventDefault();
				showSlide(slides.length - 1, true);
			}
		});

		carousel.addEventListener('touchstart', function (event) {
			touchStartX = event.changedTouches[0].clientX;
		}, { passive: true });
		carousel.addEventListener('touchend', function (event) {
			if (touchStartX === null) {
				return;
			}
			var delta = event.changedTouches[0].clientX - touchStartX;
			touchStartX = null;
			if (Math.abs(delta) < 45) {
				return;
			}
			showSlide(delta > 0 ? current - 1 : current + 1, true);
		}, { passive: true });

		showSlide(0, false);
		carousel.classList.add('is-ready');
	});
}());
