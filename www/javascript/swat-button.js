$(document).ready(function() {
	var PROCESSING_ANIMATION_DURATION = 1000; // milliseconds
	var PROCESSING_ANIMATION_MAX_OPACITY = 0.5;

	$('.swat-button').each(function() {
		var $button = $(this);

		var confirmation_message = $button.data('confirmation-message');

		var processing_message = $button.data('processing-message');
		if (processing_message || processing_message === '') {
			if (processing_message === '') {
				processing_message = ' '; // UTF-8 non-breaking space
			}
			var $throbber = $(
				'<span class="swat-button-processing-throbber"></span>'
			);
			$throbber
				.text(processing_message)
				.appendTo($button.parent());
		}

		$button.click(function(e) {
			var confirmed = (confirmation_message)
				? confirm(confirmation_message)
				: true;

			if (confirmed) {
				if (processing_message) {
					// Disable button to prevent double submission.
					$button
						.attr('disabled', true)
						.addClass('swat-insensitive');

					// Add shim to form so button value still gets submitted.
					var $shim = $('<input type="hidden"/>');
					$shim
						.attr({
							name: $button.attr('name'),
							value: $button.attr('value')
						});

					// Show processing message
					$throbber.animate(
						{ opacity: PROCESSING_ANIMATION_MAX_OPACITY },
						PROCESSING_ANIMATION_DURATION
					);

					// Submit form
					var $form = $button.parents('form');
					if ($form.length) {
						$shim.appendTo($form);
						$form.submit(); // Needed for IE and Webkit
					}
				}
			} else {
				e.preventDefault();
			}
		});
	});
});
