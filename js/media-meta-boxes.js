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
});
