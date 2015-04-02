(function($) {

$.widget('swat.swatbutton', {
	version: '2.2.3',
	options: {
		animationDuration: 1000, // milliseconds
		animationOpacity: 0.5,
		confirmationMessage: null,
		processingMessage: null
	},
	_create: function() {
		this._shim = $('<input type="hidden"/>');
		this._throbber = $(
			'<span class="swat-button-processing-throbber"></span>'
		);

		this._setProcessingMessage(this.options.processingMessage);

		this._on(this.element, {
			click: function(event) { this._handleClick(event); }
		});
	},
	_setOption: function(key, value) {
		var ret = this._super(key, value);

		if (key === 'processingMessage') {
			this._setProcessingMessage(value);
		}

		return ret;
	},
	_handleClick: function(event) {
		if (this._confirm()) {
			this._showProcessingMessage();
		} else {
			event.preventDefault();
		}
	},
	_confirm: function() {
		return (this.options.confirmationMessage)
			? confirm(this.options.confirmationMessage)
			: true;
	},
	_showProcessingMessage: function() {
		// Disable button to prevent double submission.
		this.element
			.prop('disabled', true)
			.addClass('swat-insensitive');

		// Add shim to form so button value still gets submitted.
		this._shim.prop({
			name: this.element.prop('name'),
			value: this.element.prop('value')
		});

		// Show processing message
		this._throbber.animate(
			{ opacity: this.options.animationOpacity },
			this.options.animationDuration
		);

		// Submit form
		var form = this.element.parents('form');
		if (form.length) {
			this._shim.appendTo(form);
			form.submit(); // Needed for IE and Webkit
		}
	},
	_setProcessingMessage: function(value) {
		if (value || value === '') {
			// If empty string, use non-breaking space instead so element
			// shows up.
			value = (value === '') ? ' ' : value;
			this._throbber
				.text(value)
				.appendTo(this.element.parent());
		} else {
			this._throbber.remove();
		}
	}
});

})(jQuery);

$(function() {
	$('.swat-button').each(function() {
		var $button = $(this);
		$button.swatbutton({
			processingMessage: $button.data('processing-message'),
			confirmationMessage: $button.data('confirmation-message')
		});
	});
});
