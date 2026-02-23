(function () {
	'use strict';

	function domReady(callback) {
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', callback, { once: true });
			return;
		}
		callback();
	}

	function hasStylesheetHref(href) {
		var links = document.querySelectorAll('link[rel="stylesheet"]');
		for (var i = 0; i < links.length; i += 1) {
			if (links[i].getAttribute('href') === href) {
				return true;
			}
		}
		return false;
	}

	function hasScriptWithSrc(src) {
		var scripts = document.querySelectorAll('script[src]');
		for (var i = 0; i < scripts.length; i += 1) {
			if (scripts[i].getAttribute('src') === src) {
				return true;
			}
		}
		return false;
	}

	function appendAssetsFromHtml(assetsHtml) {
		if (!assetsHtml) {
			return Promise.resolve();
		}

		var template = document.createElement('template');
		template.innerHTML = assetsHtml;
		var nodes = Array.prototype.slice.call(template.content.childNodes);

		var scriptPromises = [];

		nodes.forEach(function (node) {
			if (!node || node.nodeType !== 1) {
				return;
			}

			var tag = node.tagName.toLowerCase();

			if (tag === 'link') {
				var rel = node.getAttribute('rel');
				var href = node.getAttribute('href');
				if (rel === 'stylesheet' && href && !hasStylesheetHref(href)) {
					var link = document.createElement('link');
					link.rel = 'stylesheet';
					link.href = href;
					var media = node.getAttribute('media');
					if (media) {
						link.media = media;
					}
					document.head.appendChild(link);
				}
				return;
			}

			if (tag === 'script') {
				var src = node.getAttribute('src');
				var type = node.getAttribute('type');
				var id = node.getAttribute('id');

				if (src) {
					if (hasScriptWithSrc(src)) {
						return;
					}

					scriptPromises.push(
						new Promise(function (resolve, reject) {
							var script = document.createElement('script');
							script.src = src;
							if (type) {
								script.type = type;
							}
							if (id) {
								script.id = id;
							}
							if (node.hasAttribute('defer')) {
								script.defer = true;
							}
							if (node.hasAttribute('async')) {
								script.async = true;
							}
							script.onload = function () {
								resolve();
							};
							script.onerror = function () {
								reject(new Error('Failed to load script: ' + src));
							};
							document.body.appendChild(script);
						})
					);
					return;
				}

				// Inline scripts (localized vars, etc.) must execute.
				var inline = node.textContent;
				if (inline && inline.trim()) {
					var inlineScript = document.createElement('script');
					if (type) {
						inlineScript.type = type;
					}
					if (id) {
						inlineScript.id = id;
					}
					inlineScript.text = inline;
					document.body.appendChild(inlineScript);
				}
			}
		});

		return Promise.all(scriptPromises).then(function () {
			return;
		});
	}

	function initWpcf7In(container) {
		if (!container) {
			return;
		}

		if (!window.wpcf7) {
			return;
		}

		if (typeof window.wpcf7.init === 'function') {
			container.querySelectorAll('form.wpcf7-form').forEach(function (form) {
				try {
					window.wpcf7.init(form);
				} catch (e) {
					// noop
				}
			});
			return;
		}

		if (typeof window.wpcf7.initAll === 'function') {
			try {
				window.wpcf7.initAll();
			} catch (e) {
				// noop
			}
		}
	}

	function fetchLazyPayload(formId) {
		var config = window.Global360LazyCF7;
		if (!config || !config.ajaxUrl || !config.nonce) {
			return Promise.reject(new Error('Lazy CF7 config missing'));
		}

		var body = new URLSearchParams();
		body.set('action', 'global_360_lazy_cf7');
		body.set('form_id', formId);
		body.set('nonce', config.nonce);

		return fetch(config.ajaxUrl, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
			},
			credentials: 'same-origin',
			body: body.toString(),
		}).then(function (res) {
			return res.json();
		});
	}

	function loadFormInto(container) {
		if (!container) {
			return Promise.resolve();
		}

		if (container.dataset.cf7Loaded === '1') {
			return Promise.resolve();
		}

		if (container.dataset.cf7Loaded === 'loading') {
			return container.__cf7LoadingPromise || Promise.resolve();
		}

		var formId = container.dataset.cf7FormId;
		if (!formId) {
			return Promise.resolve();
		}

		container.dataset.cf7Loaded = 'loading';

		var promise = fetchLazyPayload(formId)
			.then(function (payload) {
				if (!payload || !payload.success || !payload.data) {
					throw new Error('Lazy CF7 payload error');
				}

				return appendAssetsFromHtml(payload.data.assets).then(function () {
					container.innerHTML = payload.data.html;
					container.dataset.cf7Loaded = '1';
					initWpcf7In(container);
				});
			})
			.catch(function () {
				container.dataset.cf7Loaded = '0';
				container.innerHTML = '<p>Unable to load the form. Please try again.</p>';
			});

		container.__cf7LoadingPromise = promise;
		return promise;
	}

	function setupLazyContainers() {
		var containers = document.querySelectorAll('[data-cf7-form-id]');
		containers.forEach(function (container) {
			var triggerSelector = container.getAttribute('data-cf7-lazy-trigger');
			if (!triggerSelector) {
				return;
			}

			var triggers = document.querySelectorAll(triggerSelector);
			triggers.forEach(function (trigger) {
				trigger.addEventListener('click', function () {
					loadFormInto(container);
				});
			});
		});
	}

	domReady(setupLazyContainers);

	window.Global360LazyCF7 = window.Global360LazyCF7 || {};
	window.Global360LazyCF7.loadFormInto = loadFormInto;
})();
