$(function() {
	var RESIZE_ANIMATION_DURATION = 250; // milliseconds

	$('.swat-accordion').each(function() {
		var $accordion = $(this);
		var current_page = null;
		var semaphore = false;
		var animate = ($accordion.data('no-animate') != 'no-animate');
		var always_open = (
			$accordion.data('not-always-open') != 'not-always-open'
		);

		function setPage(new_page) {
			if (current_page === new_page) {
				return;
			}

			for (var i = 0; i < pages.length; i++) {
				var page = pages[i];
				if (page == new_page) {
					page.$animation.css('display', 'block');
					page.$el
						.removeClass('swat-accordion-page-closed')
						.addClass('swat-accordion-page-opened');
				} else {
					page.$animation.css('display', 'none');
					page.$el
						.removeClass('swat-accordion-page-opened')
						.addClass('swat-accordion-page-closed');
				}
			}

			$accordion.trigger('page-change', [ new_page, current_page ]);

			current_page = new_page;
		}

		function setPageWithAnimation(new_page) {
			if (current_page === new_page || semaphore) {
				return;
			}

			semaphore = true;

			function animationComplete() {
				if (new_page !== null) {
					new_page.$animation.css('height', 'auto');
				}
				semaphore = false;
			}

			var old_page = current_page;

			// old_page === null means we're opening from a completely closed
			// state
			if (old_page !== null) {
				var old_from_height = old_page.$animation.outerHeight();
				var old_to_height = 0;
				old_page.$animation.css('overflow', 'hidden');
			}

			var new_to_height = 0;
			var new_from_height = 0;

			// new_page === null means we're closing to a completely closed
			// state
			if (new_page === null) {
				old_page.$animation.animate(
					{ height: 0 },
					RESIZE_ANIMATION_DURATION,
					'swing',
					animationComplete
				);
			} else {
				new_page.$animation.css('overflow', 'hidden');

				if (new_page.$animation.css('display') === 'none') {
					new_page.$animation.css('height', '0');
					new_from_height = 0;
				} else {
					new_from_height = new_page.$animation.outerHeight();
				}

				new_page.$animation.css('display', 'block');

				var new_to_height = new_page.$content.outerHeight();

				function animationProgress(animation, progress, remainingMs) {
					// Apply animation easing to progress
					progress = $.easing[animation.tweens[0].easing](progress);

					if (old_page) {
						var old_delta = old_from_height - old_to_height;
						var old_height = Math.ceil(
							old_from_height - old_delta * progress
						);

						old_page.$animation.css('height', old_height);
					}

					if (new_page) {
						var new_delta = new_from_height - new_to_height;
						var new_height = Math.floor(
							new_from_height - new_delta * progress
						);

						new_page.$animation.css('height', new_height);
					}
				}

				new_page.$animation.animate(
					// We manually set the element the heights in our progress
					// function. Just animate any value here.
					{ anyProperty: 1 },
					{
						duration: RESIZE_ANIMATION_DURATION,
						easing: 'swing',
						progress: animationProgress,
						complete: animationComplete
					}
				);
			}

			if (old_page) {
				old_page.$el
					.removeClass('swat-accordion-page-opened')
					.addClass('swat-accordion-page-closed');
			}

			if (new_page) {
				new_page.$el
					.removeClass('swat-accordion-page-closed')
					.addClass('swat-accordion-page-opened');
			}

			$accordion.trigger('page-change', [ new_page, old_page ]);

			current_page = new_page;
		}

		var hash_open_page = null;

		// Check for selected page based on location hash value
		$accordion.children('.swat-accordion-page').each(function() {
			if (location.hash === '#open_' + this.id) {
				hash_open_page = this;
			}
		});

		var pages = [];
		$accordion.children('.swat-accordion-page').each(function() {
			// Select page elements
			var page = { $el: $(this) };

			page.$animation = page.$el
				.children('.swat-accordion-page-animation')
				.first();

			page.$content = page.$animation
				.children('.swat-accordion-page-content')
				.first();

			var $toggle = page.$el
				.children('.swat-accordion-page-toggle')
				.first();

			// Add href hash tag to toggle link
			$toggle.attr(
				'href',
				location.href.split('#')[0] + '#open_' + page.$el.attr('id')
			);

			var $icon = $('<span class="swat-accordion-toggle-status"></span>');
			$icon.insertBefore($toggle);

			if (
				  !hash_open_page && page.$el.hasClass('selected')
				|| hash_open_page && page.$el.get(0) === hash_open_page
			) {
				current_page = page;
				page.$el
					.removeClass('selected')
					.addClass('swat-accordion-page-opened');
			} else {
				page.$animation.css('display', 'none');
				page.$el.addClass('swat-accordion-page-closed');
			}

			// Set up page toggle link click handler
			$toggle.click(function(e) {
				var new_page = (!always_open && page == current_page)
					? null
					: page;

				if (animate) {
					setPageWithAnimation(page);
				} else {
					setPage(page);
				}
				e.preventDefault();
			});

			// Add to list of pages
			pages.push(page);
		});

		$accordion.trigger('post-init');
	});
});
