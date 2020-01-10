import { Anim, Easing } from '../../../yui/www/animation/animation';

import '../styles/swat-actions.css';

class SwatActions {
	constructor(id, values, selected) {
		this.id = id;

		this.values = values;
		this.message_shown = false;
		this.view = null;
		this.selector_id = null;

		this.handleMessageClose = this.handleMessageClose.bind(this);
		this.handleChange = this.handleChange.bind(this);

		document.addEventListener('DOMContentLoaded', () => {
			this.init(selected);
		});
	}

	init(selected) {
		this.flydown = document.getElementById(this.id + '_action_flydown');
		this.selected_element = (selected)
			? document.getElementById(this.id + '_' + selected)
			: null;

		var button = document.getElementById(this.id + '_apply_button');

		// create message content area
		this.message_content = document.createElement('span');

		// create message dismiss link
		var message_dismiss = document.createElement('a');
		message_dismiss.href = '#';
		message_dismiss.title = SwatActions.dismiss_text;
		message_dismiss.classList.add('swat-actions-message-dismiss-link');
		message_dismiss.appendChild(
			document.createTextNode(SwatActions.dismiss_text)
		);

		message_dismiss.addEventListener('click', this.handleMessageClose);

		// create message span and add content area and dismiss link
		this.message_span = document.createElement('span');
		this.message_span.classList.add('swat-actions-message')
		this.message_span.style.visibility = 'hidden';
		this.message_span.appendChild(this.message_content);
		this.message_span.appendChild(message_dismiss);

		// add message span to document
		button.parentNode.appendChild(this.message_span);

		this.flydown.addEventListener('change', this.handleChange);
		this.flydown.addEventListener('keyup', this.handleChange);

		button.addEventListener('click', this.handleButtonClick);
	}

	setViewSelector(view, selector_id) {
		if (view.getSelectorItemCount) {
			this.view = view;
			this.selector_id = selector_id;
		}
	}

	handleChange() {
		if (this.selected_element) {
			this.selected_element.classList.add('swat-hidden');
		}

		var id = this.id + '_' +
			this.values[this.flydown.selectedIndex];

		this.selected_element = document.getElementById(id);

		if (this.selected_element) {
			this.selected_element.classList.remove('swat-hidden');
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
			e.preventDefault();
			this.showMessage(message);
		}
	}

	handleMessageClose(e) {
		e.preventDefault();
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

			var animation = new Anim(
				this.message_span,
				{ opacity: { from: 0, to: 1} },
				0.3,
				Easing.easeInStrong
			);

			animation.animate();

			this.message_shown = true;
		}
	}

	hideMessage() {
		if (this.message_shown) {
			var animation = new Anim(
				this.message_span,
				{ opacity: { from: 1, to: 0} },
				0.3,
				Easing.easeOutStrong
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

export default SwatActions;
