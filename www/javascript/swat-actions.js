class SwatActions {
	constructor(id, values, selected) {
		this.id = id;
		this.flydown = document.getElementById(id + '_action_flydown');
		this.selected_element = (selected) ?
			document.getElementById(id + '_' + selected) : null;

		var button = document.getElementById(id + '_apply_button');

		this.values = values;
		this.message_shown = false;
		this.view = null;
		this.selector_id = null;

		// create message content area
		this.message_content = document.createElement('span');

		// create message dismiss link
		var message_dismiss = document.createElement('a');
		message_dismiss.href = '#';
		message_dismiss.title = SwatActions.dismiss_text;
		YAHOO.util.Dom.addClass(message_dismiss,
			'swat-actions-message-dismiss-link');

		message_dismiss.appendChild(
			document.createTextNode(SwatActions.dismiss_text)
		);

		YAHOO.util.Event.addListener(
			message_dismiss,
			'click',
			this.handleMessageClose,
			this,
			true
		);

		// create message span and add content area and dismiss link
		this.message_span = document.createElement('span');
		YAHOO.util.Dom.addClass(this.message_span, 'swat-actions-message');
		this.message_span.style.visibility = 'hidden';
		this.message_span.appendChild(this.message_content);
		this.message_span.appendChild(message_dismiss);

		// add message span to document
		button.parentNode.appendChild(this.message_span);

		YAHOO.util.Event.addListener(this.flydown, 'change',
			this.handleChange, this, true);

		YAHOO.util.Event.addListener(this.flydown, 'keyup',
			this.handleChange, this, true);

		YAHOO.util.Event.addListener(button, 'click',
			this.handleButtonClick, this, true);
	}

	setViewSelector(view, selector_id) {
		if (view.getSelectorItemCount) {
			this.view = view;
			this.selector_id = selector_id;
		}
	}

	handleChange() {
		if (this.selected_element) {
			YAHOO.util.Dom.addClass(this.selected_element, 'swat-hidden');
		}

		var id = this.id + '_' +
			this.values[this.flydown.selectedIndex];

		this.selected_element = document.getElementById(id);

		if (this.selected_element) {
			YAHOO.util.Dom.removeClass(this.selected_element, 'swat-hidden');
		}
	}

	handleButtonClick(e) {
		var is_blank;
		var value_exp = this.flydown.value.split('|', 2);
		if (value_exp.length === 1) {
			is_blank = (value_exp[0] === '');
		} else {
			is_blank = (value_exp[1] == 'N;');
		}

		if (this.view) {
			var items_selected =
				(this.view.getSelectorItemCount(this.selector_id) > 0);
		} else {
			var items_selected = true;
		}

		var message;
		if (is_blank && !items_selected) {
			message = SwatActions.select_an_item_and_an_action_text;
		} else if (is_blank) {
			message = SwatActions.select_an_action_text;
		} else if (!items_selected) {
			message = SwatActions.select_an_item_text;
		}

		if (message) {
			YAHOO.util.Event.preventDefault(e);
			this.showMessage(message);
		}
	}

	handleMessageClose(e) {
		YAHOO.util.Event.preventDefault(e);
		this.hideMessage();
	}

	showMessage(message_text) {
		if (this.message_content.firstChild) {
			this.message_content.removeChild(this.message_content.firstChild);
		}

		this.message_content.appendChild(
			document.createTextNode(message_text + ' ')
		);

		if (!this.message_shown) {
			this.message_span.style.opacity = 0;
			this.message_span.style.visibility = 'visible';

			var animation = new YAHOO.util.Anim(
				this.message_span,
				{ opacity: { from: 0, to: 1} },
				0.3,
				YAHOO.util.Easing.easeInStrong
			);

			animation.animate();

			this.message_shown = true;
		}
	}

	hideMessage() {
		if (this.message_shown) {
			var animation = new YAHOO.util.Anim(
				this.message_span,
				{ opacity: { from: 1, to: 0} },
				0.3,
				YAHOO.util.Easing.easeOutStrong
			);

			animation.onComplete.subscribe(
				function() {
					this.message_span.style.visibility = 'hidden';
					this.message_shown = false;
				},
				this,
				true
			);

			animation.animate();
		}
	}
}

SwatActions.dismiss_text = 'Dismiss message.';
SwatActions.select_an_action_text = 'Please select an action.';
SwatActions.select_an_item_text = 'Please select one or more items.';
SwatActions.select_an_item_and_an_action_text =
	'Please select an action, and one or more items.';

module.exports = SwatActions;
