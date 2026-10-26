jQuery(function ($) {
	// 1. Vehicle Gallery Media Uploader
	$('#gw-gallery-btn').on('click', function (e) {
		e.preventDefault();
		if (typeof wp === 'undefined' || !wp.media) {
			return;
		}
		var frame = wp.media({ title: 'Select gallery images', multiple: true, library: { type: 'image' } });
		frame.on('select', function () {
			var ids = [];
			var $preview = $('#gw-gallery-preview').empty();
			frame.state().get('selection').each(function (att) {
				var j = att.toJSON();
				ids.push(j.id);
				var url = (j.sizes && j.sizes.thumbnail) ? j.sizes.thumbnail.url : j.url;
				$('<img>').attr('src', url).attr('alt', '').appendTo($preview);
			});
			$('#gw_gallery').val(ids.join(','));
		});
		frame.open();
	});

	// 2. Dashboard Tab Switching
	$(document).on('click', '.gw-tab-btn', function (e) {
		e.preventDefault();
		var targetId = $(this).attr('data-tab');
		if (!targetId) {
			return;
		}

		// Update active tab buttons
		$('.gw-tab-btn').removeClass('active');
		$('.gw-tab-btn[data-tab="' + targetId + '"]').addClass('active');

		// Switch visible tab pane
		$('.gw-tab-pane').removeClass('active');
		$('#' + targetId).addClass('active');

		// Sync URL hash
		var slug = targetId.replace('gw-tab-', '');
		if (window.history && window.history.replaceState) {
			var newUrl = window.location.href.split('#')[0] + '#' + slug;
			window.history.replaceState(null, null, newUrl);
		}
	});

	// Activate tab on page load if URL hash is present
	var initialHash = window.location.hash.replace('#', '');
	if (initialHash && $('#gw-tab-' + initialHash).length) {
		$('.gw-tab-btn[data-tab="gw-tab-' + initialHash + '"]').trigger('click');
	}

	// 3. 1-Click Copy Shortcode to Clipboard
	$(document).on('click', '.gw-copy-btn', function (e) {
		e.preventDefault();
		var $btn = $(this);
		var text = $btn.attr('data-copy');

		if (!text) {
			var $code = $btn.siblings('code');
			if (!$code.length) {
				$code = $btn.closest('.gw-code-box, .gw-output-bar').find('code');
			}
			text = $code.text();
		}

		text = $.trim(text);
		if (!text) {
			return;
		}

		function onCopied() {
			var origHtml = $btn.data('gw-orig-html');
			if (!origHtml) {
				origHtml = $btn.html();
				$btn.data('gw-orig-html', origHtml);
			}
			$btn.addClass('gw-copied').html('<span class="dashicons dashicons-yes"></span> Copied!');
			setTimeout(function () {
				$btn.removeClass('gw-copied').html(origHtml);
			}, 2000);
		}

		if (navigator.clipboard && navigator.clipboard.writeText) {
			navigator.clipboard.writeText(text).then(onCopied).catch(function () {
				fallbackCopy(text, onCopied);
			});
		} else {
			fallbackCopy(text, onCopied);
		}
	});

	function fallbackCopy(str, callback) {
		var $temp = $('<textarea>');
		$('body').append($temp);
		$temp.val(str).select();
		try {
			document.execCommand('copy');
			if (callback) callback();
		} catch (err) {
			console.warn('Copy failed', err);
		}
		$temp.remove();
	}

	// 4. Interactive Live Shortcode Builder
	function updateShortcodeGenerator() {
		var type = $('#gw-gen-type').val();
		var shortcode = '';

		if (type === 'cars_grid') {
			var cardStyle = $('#gw-gen-card-style').val();
			var cols = $('#gw-gen-cols').val();
			var limit = $('#gw-gen-limit').val() || 6;
			shortcode = '[gowheels_cars card_style="' + cardStyle + '" columns="' + cols + '" limit="' + limit + '"]';
		} else if (type === 'cars_tabs') {
			var tabCardStyle = $('#gw-gen-tab-card-style').val();
			var tabLimit = $('#gw-gen-tab-limit').val() || 9;
			shortcode = '[gowheels_cars layout="tabs" card_style="' + tabCardStyle + '" limit="' + tabLimit + '"]';
		} else if (type === 'single_car') {
			var tmpl = $('#gw-gen-template').val();
			var vid = $('#gw-gen-vehicle-id').val();
			if (vid) {
				shortcode = '[gowheels_car id="' + vid + '" template="' + tmpl + '"]';
			} else {
				shortcode = '[gowheels_car template="' + tmpl + '"]';
			}
		} else if (type === 'search') {
			shortcode = '[gowheels_search]';
		} else if (type === 'booking') {
			var bookVid = $('#gw-gen-vehicle-id').val();
			shortcode = bookVid ? '[gowheels_booking vehicle_id="' + bookVid + '"]' : '[gowheels_booking]';
		} else if (type === 'status') {
			shortcode = '[gowheels_booking_status]';
		}

		$('#gw-generated-code').text(shortcode);
		$('#gw-gen-copy-btn').attr('data-copy', shortcode);
	}

	$(document).on('change', '#gw-gen-type', function () {
		var type = $(this).val();
		$('.gw-gen-field').hide();
		if (type === 'cars_grid') {
			$('.gw-gen-grid-field').show();
		} else if (type === 'cars_tabs') {
			$('.gw-gen-tabs-field').show();
		} else if (type === 'single_car') {
			$('.gw-gen-single-field').show();
		} else if (type === 'booking') {
			$('.gw-gen-vehicle-field').show();
		}
		updateShortcodeGenerator();
	});

	$(document).on('input change', '#gw-generator-form select, #gw-generator-form input', function () {
		updateShortcodeGenerator();
	});

	if ($('#gw-gen-type').length) {
		updateShortcodeGenerator();
	}

	// 5. Design Studio Section Tabs Switcher
	$(document).on('click', '.gw-studio-tab-btn', function (e) {
		e.preventDefault();
		var section = $(this).data('section');
		if (!section) return;

		$('.gw-studio-tab-btn').removeClass('active');
		$(this).addClass('active');

		$('.gw-studio-section').hide();
		$('#gw-section-' + section).fadeIn(200);
	});

	// 6. Interactive Design Studio Selection (AJAX)
	$(document).on('click', '.gw-select-design-btn', function (e) {
		e.preventDefault();
		var $btn = $(this);
		var type = $btn.data('type'); // 'card' or 'single'
		var id   = $btn.data('id');   // '1', '2', '3'

		if (typeof gowheelsDesignData === 'undefined') {
			return;
		}

		$btn.prop('disabled', true).text('Saving...');

		$.post(gowheelsDesignData.ajaxurl, {
			action: 'gowheels_save_active_design',
			nonce: gowheelsDesignData.nonce,
			design_type: type,
			design_id: id
		}, function (res) {
			$btn.prop('disabled', false);
			if (res && res.success) {
				// Update all buttons in this section
				var $container = $btn.closest('.gw-studio-section');
				$container.find('.gw-select-design-btn[data-type="' + type + '"]')
					.removeClass('gw-btn-selected')
					.addClass('button-primary')
					.text(gowheelsDesignData.chooseText || 'Choose design');

				$btn.addClass('gw-btn-selected').removeClass('button-primary')
					.text(gowheelsDesignData.savedText || 'Selected ✓');

				// Update row border highlight
				$container.find('.gw-preset-row').removeClass('gw-preset-selected');
				$btn.closest('.gw-preset-row').addClass('gw-preset-selected');

				// Show toast
				showToast(res.data && res.data.message ? res.data.message : 'Design updated successfully!');
			} else {
				alert(res && res.data && res.data.message ? res.data.message : 'Error saving design.');
			}
		}).fail(function () {
			$btn.prop('disabled', false).text('Error');
		});
	});

	function showToast(msg) {
		var $toast = $('#gw-toast-notice');
		if (!$toast.length) {
			$toast = $('<div id="gw-toast-notice" class="gw-toast-notice"></div>').appendTo('body');
		}
		$toast.html('<span class="dashicons dashicons-yes"></span> ' + msg).fadeIn(200);
		setTimeout(function () {
			$toast.fadeOut(400);
		}, 3000);
	}
});

