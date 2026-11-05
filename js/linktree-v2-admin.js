(function ($) {
	'use strict';

	var panel = $('#global360-linktree-v2-panel');
	var list = $('#global360-linktree-v2-slides');
	var slideIndex = list.children().length;
	var config = window.global360LinktreeV2Admin || {};
	var v2Template = config.v2Template || panel.data('template') || 'page-linktree-v2.php';
	var lastTemplate = null;

	if (!panel.length) {
		return;
	}

	function escapeHtml(value) {
		return $('<div>').text(value || '').html();
	}

	function refreshOrder() {
		list.children('.global360-linktree-slide').each(function (index) {
			var row = $(this);
			row.attr('data-index', index);
			row.find('[name]').each(function () {
				this.name = this.name.replace(/global360_linktree_v2_slides\[\d+\]/, 'global360_linktree_v2_slides[' + index + ']');
			});
			row.find('.global360-linktree-slide__up').prop('disabled', index === 0);
			row.find('.global360-linktree-slide__down').prop('disabled', index === list.children().length - 1);
		});
	}

	function slideMarkup(index) {
		return '<li class="global360-linktree-slide" data-index="' + index + '">' +
			'<div class="global360-linktree-slide__handle" aria-hidden="true"><span class="dashicons dashicons-move"></span></div>' +
			'<div class="global360-linktree-slide__preview"></div>' +
			'<div class="global360-linktree-slide__fields">' +
			'<input class="global360-linktree-slide__id" type="hidden" name="global360_linktree_v2_slides[' + index + '][attachment_id]" value="">' +
			'<p><button type="button" class="button global360-linktree-slide__select">Select image</button> ' +
			'<button type="button" class="button-link-delete global360-linktree-slide__remove-image" hidden>Remove image</button></p>' +
			'<label>Alt text override<input class="widefat global360-linktree-slide__alt" type="text" name="global360_linktree_v2_slides[' + index + '][alt]" value=""></label>' +
			'<p class="description">Leave blank to use the image’s Media Library alt text.</p>' +
			'<div class="global360-linktree-slide__actions"><button type="button" class="button global360-linktree-slide__up">Move up</button> ' +
			'<button type="button" class="button global360-linktree-slide__down">Move down</button> ' +
			'<button type="button" class="button-link-delete global360-linktree-slide__remove">Remove slide</button></div>' +
			'</div></li>';
	}

	function openImagePicker(row) {
		var frame = wp.media({
			title: 'Select carousel image',
			button: { text: 'Use this image' },
			library: { type: 'image' },
			multiple: false
		});
		frame.on('select', function () {
			var image = frame.state().get('selection').first().toJSON();
			var previewUrl = image.sizes && image.sizes.thumbnail ? image.sizes.thumbnail.url : image.url;
			row.find('.global360-linktree-slide__id').val(image.id);
			row.find('.global360-linktree-slide__preview').html('<img src="' + escapeHtml(previewUrl) + '" alt="">');
			row.find('.global360-linktree-slide__select').text('Replace image');
			row.find('.global360-linktree-slide__remove-image').prop('hidden', false);
			if (!row.find('.global360-linktree-slide__alt').val() && image.alt) {
				row.find('.global360-linktree-slide__alt').val(image.alt);
			}
		});
		frame.open();
	}

	$('#global360-linktree-v2-add-slide').on('click', function () {
		var row = $(slideMarkup(slideIndex++));
		list.append(row);
		refreshOrder();
		openImagePicker(row);
	});

	list.on('click', '.global360-linktree-slide__select', function () {
		openImagePicker($(this).closest('.global360-linktree-slide'));
	});
	list.on('click', '.global360-linktree-slide__remove-image', function () {
		var row = $(this).closest('.global360-linktree-slide');
		row.find('.global360-linktree-slide__id').val('');
		row.find('.global360-linktree-slide__preview').empty();
		row.find('.global360-linktree-slide__select').text('Select image');
		$(this).prop('hidden', true);
	});
	list.on('click', '.global360-linktree-slide__remove', function () {
		$(this).closest('.global360-linktree-slide').remove();
		refreshOrder();
	});
	list.on('click', '.global360-linktree-slide__up', function () {
		var row = $(this).closest('.global360-linktree-slide');
		row.prev().before(row);
		refreshOrder();
		row.find('.global360-linktree-slide__up').trigger('focus');
	});
	list.on('click', '.global360-linktree-slide__down', function () {
		var row = $(this).closest('.global360-linktree-slide');
		row.next().after(row);
		refreshOrder();
		row.find('.global360-linktree-slide__down').trigger('focus');
	});

	list.sortable({
		handle: '.global360-linktree-slide__handle',
		items: '> .global360-linktree-slide',
		update: refreshOrder
	});

	var guideFrame;
	$('#global360-linktree-v2-guide-select').on('click', function () {
		if (!guideFrame) {
			guideFrame = wp.media({ title: 'Select download guide', button: { text: 'Use this file' }, multiple: false });
			guideFrame.on('select', function () {
				var file = guideFrame.state().get('selection').first().toJSON();
				$('#global360-linktree-v2-guide-id').val(file.id);
				$('#global360-linktree-v2-guide-preview').html('<a href="' + escapeHtml(file.url) + '" target="_blank" rel="noopener">' + escapeHtml(file.filename || file.title) + '</a>');
				$('#global360-linktree-v2-guide-select').text('Replace guide');
				$('#global360-linktree-v2-guide-remove').prop('hidden', false);
			});
		}
		guideFrame.open();
	});
	$('#global360-linktree-v2-guide-remove').on('click', function () {
		$('#global360-linktree-v2-guide-id').val('');
		$('#global360-linktree-v2-guide-preview').text('No guide selected.');
		$('#global360-linktree-v2-guide-select').text('Select guide');
		$(this).prop('hidden', true);
	});

	function blockEditorTemplate() {
		if (!window.wp || !wp.data || typeof wp.data.select !== 'function') {
			return null;
		}
		try {
			var editor = wp.data.select('core/editor');
			if (!editor || typeof editor.getEditedPostAttribute !== 'function') {
				return null;
			}
			var template = editor.getEditedPostAttribute('template');
			return typeof template === 'string' ? (template || 'default') : null;
		} catch (error) {
			return null;
		}
	}

	function selectedTemplate() {
		var templateSelect = $('#page_template');
		if (templateSelect.length) {
			return templateSelect.val() || 'default';
		}
		return blockEditorTemplate() || config.savedTemplate || 'default';
	}

	function updatePanelVisibility() {
		var template = selectedTemplate();
		var selected = template === v2Template;
		var metaBox = $('#global360-linktree-v2-fields');
		metaBox
			.toggleClass('global360-linktree-v2-template-hidden', !selected)
			.toggle(selected)
			.attr('aria-hidden', selected ? 'false' : 'true');
		lastTemplate = template;
	}

	$(document).on('change', '#page_template', updatePanelVisibility);
	if (window.wp && wp.data && typeof wp.data.subscribe === 'function') {
		wp.data.subscribe(function () {
			var template = blockEditorTemplate();
			if (template !== null && template !== lastTemplate) {
				updatePanelVisibility();
			}
		});
	}
	refreshOrder();
	updatePanelVisibility();
}(jQuery));
