$(function() {
	var RESIZE_ANIMATION_DURATION = 250; // milliseconds

	$('.swat-accordion').each(function() {
		var $accordion = $(this);
		var animate = ($accordion.data('no-animate') != 'no-animate');
		var always_open = (
			$accordion.data('not-always-open') != 'not-always-open'
		);

		// check for location hash open page
		var hash_open_page = null;
		$accordion.children('.swat-accordion-page').each(function() {
			if (location.hash === '#open_' + this.id) {
				hash_open_page = this;
			}
		});

		// check for selected page, convert it to an active page index value
		var i = 0;
		var active_index = 0;
		$accordion.children('.swat-accordion-page').each(function() {
			var $page = $(this);
			if (   !hash_open_page && $page.hasClass('selected')
				|| hash_open_page && $page.get(0) === hash_open_page
			) {
				active_index = i;
				$page
					.removeClass('swat-accordion-page-closed')
					.addClass('swat-accordion-page-opened');
			} else {
				$page
					.removeClass('swat-accordion-page-opened')
					.addClass('swat-accordion-page-closed');
			}

			// add href location hash to toggle link
			$page
				.children('.swat-accordion-page-toggle')
				.attr(
					'href',
					location.href.split('#')[0] + '#open_' + $page.attr('id')
				);

			i++;
		});

		$accordion.accordion({
			heightStyle: 'content',
			animate: animate ? RESIZE_ANIMATION_DURATION : false,
			collapsible: !always_open,
			header: '.swat-accordion-page-toggle',
			active: active_index,
			create: function(e, ui) {
				$(this).children('.swat-accordion-page').each(function() {
					var $page = $(this);
					if ($page.is(ui.header.parent())) {
						$page
							.removeClass('swat-accordion-page-closed')
							.addClass('swat-accordion-page-opened');
					} else {
						$page
							.removeClass('swat-accordion-page-opened')
							.addClass('swat-accordion-page-closed');
					}
				});
			},
			beforeActivate: function(e, ui) {
				if (ui.newHeader) {
					ui.newHeader
						.parent()
						.removeClass('swat-accordion-page-closed')
						.addClass('swat-accordion-page-opened');
				}
				if (ui.oldHeader) {
					ui.oldHeader
						.parent()
						.removeClass('swat-accordion-page-opened')
						.addClass('swat-accordion-page-closed');
				}
			}
		});
	});
});
