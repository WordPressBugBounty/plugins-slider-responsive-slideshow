/**
 * Slider Responsive Slideshow - Admin Helper Scripts
 *
 * Contains extracted inline scripts for the slider admin settings UI.
 * Handles: copy-to-clipboard, tab navigation, range sliders, show/hide
 * toggles for link/navigation/autoplay settings, accordion icons, and
 * shortcode pulsation effect.
 *
 * @since 1.6.0
 */
(function($) {
	'use strict';

	/**
	 * Shortcode copy-to-clipboard (metabox sidebar)
	 */
	window.copyToClipboard = function(element) {
		var $temp = $('<input>');
		$('body').append($temp);
		$temp.val($(element).val()).select();
		document.execCommand('copy');
		$temp.remove();
		$('#SRScopyshortcode').select();
		$('#srs-copy-code').fadeIn();
	};

	/**
	 * Shortcode column copy handler (CPT list table)
	 * Uses delegated event binding instead of per-row inline scripts.
	 */
	$(document).on('click', '.sr-copy-shortcode-btn', function() {
		var postId = $(this).data('post-id');
		var copyText = document.getElementById('slider-responsive-shortcode-' + postId);
		if (copyText) {
			copyText.select();
			document.execCommand('copy');
			$('#copy-msg-' + postId).fadeIn(1000, 'linear').fadeOut(2500, 'swing');
		}
	});

	/**
	 * Settings page initialization
	 */
	$(document).ready(function() {

		// Hide copy confirmation on load
		$('#srs-copy-code').hide();

		// ---- Show/Hide Toggle: Link Settings ----
		var linkVal = $('input[name="show_link"]:checked').val();
		if (linkVal === 'true') { $('.link_show_hide').show(); }
		if (linkVal === 'false') { $('.link_show_hide').hide(); }

		$('input[name="show_link"]').change(function() {
			var v = $('input[name="show_link"]:checked').val();
			if (v === 'true') { $('.link_show_hide').show(); }
			if (v === 'false') { $('.link_show_hide').hide(); }
		});

		// ---- Show/Hide Toggle: Navigation Settings ----
		var navVal = $('input[name="navigation"]:checked').val();
		if (navVal === 'true') { $('.nav_show_hide').show(); }
		if (navVal === 'false') { $('.nav_show_hide').hide(); }

		$('input[name="navigation"]').change(function() {
			var v = $('input[name="navigation"]:checked').val();
			if (v === 'true') { $('.nav_show_hide').show(); }
			if (v === 'false') { $('.nav_show_hide').hide(); }
		});

		// ---- Show/Hide Toggle: Auto Play Settings ----
		var apVal = $('input[name="autoplay"]:checked').val();
		if (apVal === 'true') { $('.ap_show_hide').show(); }
		if (apVal === 'false') { $('.ap_show_hide').hide(); }

		$('input[name="autoplay"]').change(function() {
			var v = $('input[name="autoplay"]:checked').val();
			if (v === 'true') { $('.ap_show_hide').show(); }
			if (v === 'false') { $('.ap_show_hide').hide(); }
		});

		// ---- Accordion Icon Toggle ----
		(function() {
			function toggleSign(e) {
				$(e.target)
					.prev('.panel-heading')
					.find('i')
					.toggleClass('fa fa-chevron-down fa fa-chevron-up');
			}
			$('#accordion').on('hidden.bs.collapse', toggleSign);
			$('#accordion').on('shown.bs.collapse', toggleSign);
		})();

		// ---- Range Slider Value Display ----
		(function() {
			var slider = $('.range-slider'),
				range = $('.range-slider__range'),
				value = $('.range-slider__value');

			slider.each(function() {
				value.each(function() {
					var val = $(this).prev().attr('value');
					$(this).html(val);
				});
				range.on('input', function() {
					$(this).next(value).html(this.value);
				});
			});
		})();

		// ---- Shortcode Pulse Effect (bounded) ----
		(function() {
			var $target = $('#shortcode');
			if (!$target.length) { return; }

			var pulseInterval = setInterval(function() {
				// Auto-stop if target is no longer visible or present
				if (!$target.is(':visible') || !$.contains(document, $target[0])) {
					clearInterval(pulseInterval);
					return;
				}
				$target.fadeOut(600).fadeIn(600);
			}, 1500);
		})();

		// ---- Tab Navigation ----
		$('div.bhoechie-tab-menu>div.list-group>a').click(function(e) {
			e.preventDefault();
			$(this).siblings('a.active').removeClass('active');
			$(this).addClass('active');
			var index = $(this).index();
			$('div.bhoechie-tab>div.bhoechie-tab-content').removeClass('active');
			$('div.bhoechie-tab>div.bhoechie-tab-content').eq(index).addClass('active');
		});

	});

})(jQuery);
