jQuery(function ($) {
	function setupMediaButton(opts) {
		var frame;
		$(opts.button).on('click', function (e) {
			e.preventDefault();
			if (frame) {
				frame.open();
				return;
			}
			frame = wp.media({
				title: opts.title,
				button: { text: opts.buttonText },
				library: { type: 'image' },
				multiple: false,
			});
			frame.on('select', function () {
				var data = frame.state().get('selection').first().toJSON();
				var url = data.sizes && data.sizes.medium ? data.sizes.medium.url : data.url;
				$(opts.field).val(data.id);
				$(opts.container + ' img').remove();
				$(opts.insertTarget).before(
					`<img src="${url}" style="max-width:200px; display:block; margin-bottom:10px;" />`
				);
				$(opts.remove).show();
				$(opts.button).text(opts.changeText);
			});
			frame.open();
		});

		$(opts.remove).on('click', function (e) {
			e.preventDefault();
			$(opts.field).val('');
			$(opts.container + ' img').remove();
			$(this).hide();
			$(opts.button).text(opts.selectText);
		});
	}

	var frame;
	$('#header_logo_button').on('click', function (e) {
		e.preventDefault();
		if (frame) {
			frame.open();
			return;
		}
		frame = wp.media({
			title: 'Select or Upload Header Logo',
			button: { text: 'Use this logo' },
			library: { type: 'image' },
			multiple: false,
		});
		frame.on('select', function () {
			var data = frame.state().get('selection').first().toJSON();
			var url = data.sizes && data.sizes.medium ? data.sizes.medium.url : data.url;
			$('#header_logo_id').val(data.id);
			$('#header-logo-settings-container img').remove();
			$('#header_logo_button').before(
				'<img src="' + url + '" style="max-width:200px; display:block; margin-bottom:10px;" />'
			);
			$('#header_logo_remove').show();
			$('#header_logo_button').text('Change Logo');
		});
		frame.open();
	});
	$('#header_logo_remove').on('click', function (e) {
		e.preventDefault();
		$('#header_logo_id').val('');
		$('#header-logo-settings-container img').remove();
		$(this).hide();
		$('#header_logo_button').text('Select Logo');
	});

	// Clinic Logo
	setupMediaButton({
		button: '#clinic_logo_button',
		remove: '#clinic_logo_remove',
		field: '#clinic_logo_field',
		container: '#clinic-logo-container',
		insertTarget: '#clinic_logo_button',
		title: 'Select or Upload Logo',
		buttonText: 'Use this logo',
		selectText: 'Select Logo',
		changeText: 'Change Logo',
	});

	// Clinic Thumbnail
	setupMediaButton({
		button: '#clinic_thumbnail_button',
		remove: '#clinic_thumbnail_remove',
		field: '#clinic_thumbnail_field',
		container: '#clinic-thumbnail-container',
		insertTarget: '#clinic_thumbnail_button',
		title: 'Select or Upload Thumbnail',
		buttonText: 'Use this thumbnail',
		selectText: 'Select Thumbnail',
		changeText: 'Change Thumbnail',
	});

	// Doctor Photo
	setupMediaButton({
		button: '#doctor_photo_button',
		remove: '#doctor_photo_remove',
		field: '#doctor_photo_field',
		container: '#doctor-details-metabox',
		insertTarget: '#doctor_photo_button',
		title: 'Select Doctor Photo',
		buttonText: 'Use this photo',
		selectText: 'Select Photo',
		changeText: 'Change Photo',
	});

	// Header Logo (for settings page)
	setupMediaButton({
		button: '#header_logo_button',
		remove: '#header_logo_remove',
		field: '#header_logo_id',
		container: '#header-logo-settings-container',
		insertTarget: '#header_logo_button',
		title: 'Select or Upload Header Logo',
		buttonText: 'Use this logo',
		selectText: 'Select Logo',
		changeText: 'Change Logo',
	});

	// Linktree Logo (for settings page)
	setupMediaButton({
		button: '#linktree_logo_button',
		remove: '#linktree_logo_remove',
		field: '#linktree_logo_id',
		container: '#linktree-logo-settings-container',
		insertTarget: '#linktree_logo_button',
		title: 'Select or Upload Linktree Logo',
		buttonText: 'Use this logo',
		selectText: 'Select Logo',
		changeText: 'Change Logo',
	});

	initializeFaviconManager();

	function initializeFaviconManager() {
		var manager = $('#cpt360-favicon-manager');
		if (!manager.length || typeof wp === 'undefined' || !wp.media) {
			return;
		}

		var bulkFrame;
		var singleFrames = {};
		var order = ['png_96_id', 'svg_id', 'ico_id', 'apple_touch_180_id', 'manifest_id'];

		var bulkButton = manager.find('#cpt360-favicon-bulk-upload');
		var clearAllButton = manager.find('.cpt360-favicon-clear-all');

		function getRow(key) {
			return manager.find('.cpt360-favicon-row[data-favicon-key="' + key + '"]');
		}

		function filenameFromAttachment(file) {
			if (!file) {
				return '';
			}
			if (file.filename) {
				return file.filename;
			}
			if (file.title) {
				return file.title;
			}
			if (file.url) {
				return file.url.split('/').pop();
			}
			return '';
		}

		function matchesSlot(key, file) {
			if (!file) {
				return false;
			}
			var filename = (filenameFromAttachment(file) || '').toLowerCase();
			var mime = (file.mime || '').toLowerCase();
			var subtype = (file.subtype || '').toLowerCase();
			var width = parseInt(file.width, 10);
			var height = parseInt(file.height, 10);

			switch (key) {
				case 'png_96_id':
					if (mime === 'image/png' || subtype === 'png') {
						if (width === 96 && height === 96) {
							return true;
						}
						return filename.indexOf('favicon') !== -1 && filename.indexOf('96x96') !== -1;
					}
					return false;
				case 'svg_id':
					return filename.endsWith('.svg');
				case 'ico_id':
					return filename.endsWith('.ico') || mime === 'image/x-icon' || subtype === 'ico';
				case 'apple_touch_180_id':
					if (mime === 'image/png' || subtype === 'png') {
						if (width === 180 && height === 180) {
							return true;
						}
						return filename.indexOf('apple') !== -1 && filename.indexOf('touch') !== -1;
					}
					return false;
				case 'manifest_id':
					return filename.endsWith('.webmanifest') || filename.endsWith('manifest.json');
			}
			return false;
		}

		function updateRow(key, file) {
			var row = getRow(key);
			if (!row.length) {
				return;
			}
			var field = row.find('.cpt360-favicon-field');
			var filename = filenameFromAttachment(file);
			var preview = row.find('.cpt360-favicon-preview');
			var link = row.find('.cpt360-favicon-link');
			var removeBtn = row.find('.cpt360-favicon-remove');
			var emptyLabel = row.find('.cpt360-favicon-filename').data('empty-label') || 'Not set';

			field.val(file && file.id ? file.id : '');
			row.find('.cpt360-favicon-filename').text(filename || emptyLabel);

			var viewLabel = row.data('viewLabel') || 'View file';
			if (file && file.url) {
				link.show().html('<a href="' + file.url + '" target="_blank" rel="noopener">' + viewLabel + '</a>');
			} else {
				link.hide().empty();
			}

			if (preview.length) {
				if ((key === 'png_96_id' || key === 'apple_touch_180_id') && file && file.url) {
					preview.empty().append($('<img>', { src: file.url, alt: '' }));
				} else if (key === 'svg_id') {
					preview.empty().append($('<span class="dashicons dashicons-media-code"></span>'));
				} else if (key === 'ico_id') {
					preview.empty().append($('<span class="dashicons dashicons-art"></span>'));
				} else if (key === 'manifest_id') {
					preview.empty().append($('<span class="dashicons dashicons-media-text"></span>'));
				} else {
					preview.empty();
				}
			}

			if (removeBtn.length) {
				if (file && file.id) {
					removeBtn.show();
				} else {
					removeBtn.hide();
				}
			}

			row.toggleClass('is-set', !!(file && file.id));
		}

		function clearRow(row) {
			var emptyLabel = row.find('.cpt360-favicon-filename').data('empty-label') || 'Not set';
			row.removeClass('is-set');
			row.find('.cpt360-favicon-field').val('');
			row.find('.cpt360-favicon-filename').text(emptyLabel);
			row.find('.cpt360-favicon-link').hide().empty();
			row.find('.cpt360-favicon-preview').empty();
			row.find('.cpt360-favicon-remove').hide();
		}

		function handleSingleSelection(key) {
			if (!singleFrames[key]) {
				singleFrames[key] = wp.media({
					title: 'Select favicon file',
					button: { text: 'Use this file' },
					multiple: false,
				});
				singleFrames[key].on('select', function () {
					var attachment = singleFrames[key].state().get('selection').first().toJSON();
					if (!matchesSlot(key, attachment)) {
						window.alert('The selected file does not match the expected format for this icon slot.');
						return;
					}
					updateRow(key, attachment);
				});
			}
			singleFrames[key].open();
		}

		manager.on('click', '.cpt360-favicon-select', function (e) {
			e.preventDefault();
			var key = $(this).closest('.cpt360-favicon-row').data('faviconKey');
			if (!key) {
				return;
			}
			handleSingleSelection(key);
		});

		manager.on('click', '.cpt360-favicon-remove', function (e) {
			e.preventDefault();
			var row = $(this).closest('.cpt360-favicon-row');
			clearRow(row);
		});

		clearAllButton.on('click', function (e) {
			e.preventDefault();
			manager.find('.cpt360-favicon-row').each(function () {
				clearRow($(this));
			});
		});

		bulkButton.on('click', function (e) {
			e.preventDefault();
			if (bulkFrame) {
				bulkFrame.open();
				return;
			}
			bulkFrame = wp.media({
				title: 'Upload favicon files',
				button: { text: 'Use selected files' },
				multiple: true,
			});
			bulkFrame.on('select', function () {
				var selection = bulkFrame.state().get('selection');
				selection.each(function (attachment) {
					var data = attachment.toJSON();
					for (var i = 0; i < order.length; i++) {
						var key = order[i];
						if (matchesSlot(key, data)) {
							updateRow(key, data);
							break;
						}
					}
				});
			});
			bulkFrame.open();
		});
	}
});
