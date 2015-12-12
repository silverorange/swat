$(document).ready(function() {
	$('.swat-form').each(function() {
		var $form = $(this);

		// Handle setting auto-focus
		if ($form.data('autofocus')) {
			var $el = $('#' + $form.data('autofocus'));
			if (!$el.attr('disabled') == !isNaN($el.attr('tabindex'))) {
				$el.focus();
			}
		}

		// Add submit handler to explicitly close persisitent HTTP connections
		if (   $form.data('connection-close-url')
			&& $form.attr('enctype') == 'multipart/form-data'
			&& /^.*mac os x.*safari.*$/i.test(navigator.userAgent)
		) {
			$form.submit(function(e) {
				$.get({ url: $form.data('connection-close-url') });
			});
		}
	});
});
