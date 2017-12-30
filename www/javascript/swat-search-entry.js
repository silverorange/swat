class SwatSearchEntry {
	constructor(id) {
		this.id = id;
		this.input = document.getElementById(this.id);
		this.input._search_entry = this;

		var labels = document.getElementsByTagName('label');
		var label = null;

		for (var i = labels.length - 1; i >= 0; i--) {
			if (labels[i].htmlFor == this.id) {
				label = labels[i];
				break;
			}
		}

		if (label !== null) {
			this.label_text = (label.innerText)
				? label.innerText
				: label.textContent;

			this.input_name = this.input.getAttribute('name');
			this.input_value = this.input.value;

			label.style.display = 'none';

			YAHOO.util.Event.addListener(
				this.input,
				'focus',
				this.handleFocus,
				this,
				true
			);
			YAHOO.util.Event.addListener(
				this.input,
				'blur',
				this.handleBlur,
				this,
				true
			);

			YAHOO.util.Event.onDOMReady(this.init, this, true);
		}
	}

	init() {
		if (this.input.value === '' && !this.input._focused) {
			this.showLabelText();
		} else {
			this.hideLabelText();
		}
	}

	handleKeyDown(e) {
		// prevent esc from undoing the clearing of label text in Firefox
		if (e.keyCode === 27) {
			this.input.value = '';
		}

		YAHOO.util.Event.removeListener(this.input, 'keypress', this.handleKeyDown);
	}

	handleFocus(e) {
		this.hideLabelText();
		this.input.focus(); // IE hack to focus

		// hack to enable initialization when focused
		this.input._focused = true;
	}

	handleBlur(e) {
		if (this.input.value === '') {
			this.showLabelText();
		}

		YAHOO.util.Event.removeListener(
			this.input,
			'keypress',
			this.handleKeyDown
		);

		// hack to enable initialization when focused
		this.input._focused = false;
	}

	showLabelText() {
		if (this.isLabelTextShown()) {
			return;
		}

		YAHOO.util.Dom.addClass(this.input, 'swat-search-entry-empty');

		if (this.input.hasAttribute) {
			this.input.removeAttribute('name');
		} else {
			// IE can't set name attribute at runtime and doesn't have
			// hasAttribute method. Unbelievable but it's true.
			if (this.input.name) {
				// remove name attribute
				var outer_html = this.input.outerHTML.replace(
					'name=' + this.input_name,
					''
				);

				var old_input = this.input;
				this.input = document.createElement(outer_html);

				// replace old input with new one
				old_input.parentNode.insertBefore(this.input, old_input);

				// prevent IE memory leaks
				YAHOO.util.Event.purgeElement(old_input);
				old_input.parentNode.removeChild(old_input);

				// add event handlers back
				YAHOO.util.Event.addListener(
					this.input,
					'focus',
					this.handleFocus,
					this,
					true
				);
				YAHOO.util.Event.addListener(
					this.input,
					'blur',
					this.handleBlur,
					this,
					true
				);
			}
		}

		this.input_value = this.input.value;
		this.input.value = this.label_text;
	}

	isLabelTextShown() {
		var shown;

		if (this.input.hasAttribute) {
			shown = (!this.input.hasAttribute('name'));
		} else {
			shown = (!this.input.getAttribute('name'));
		}

		return shown;
	}

	hideLabelText() {
		if (!this.isLabelTextShown()) {
			return;
		}

		var hide = false;

		if (this.input.hasAttribute) {
			if (!this.input.hasAttribute('name')) {
				this.input.setAttribute('name', this.input_name);
				hide = true;
			}
		} else {
			// IE hack - seriously, unbelievable.
			if (!this.input.getAttribute('name')) {

				// we want the same input with a name attribute
				var outer_html = this.input.outerHTML.replace(
					'id=' + this.id,
					'id=' + this.id + ' name=' + this.input_name
				);

				var old_input = this.input;
				this.input = document.createElement(outer_html);

				// add event handlers back
				YAHOO.util.Event.addListener(
					this.input,
					'focus',
					this.handleFocus,
					this,
					true
				);
				YAHOO.util.Event.addListener(
					this.input,
					'blur',
					this.handleBlur,
					this,
					true
				);

				// replace old input with new one
				old_input.parentNode.insertBefore(this.input, old_input);

				// prevent IE memory leaks
				YAHOO.util.Event.purgeElement(old_input);
				old_input.parentNode.removeChild(old_input);

				hide = true;
			}
		}

		if (hide) {
			this.input.value = this.input_value;
			YAHOO.util.Dom.removeClass(this.input, 'swat-search-entry-empty');
			YAHOO.util.Event.addListener(
				this.input,
				'keypress',
				this.handleKeyDown,
				this,
				true
			);
		}
	}
}

module.exports = SwatSearchEntry;
