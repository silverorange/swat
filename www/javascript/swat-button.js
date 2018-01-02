import { Dom } from '../../../yui/www/dom/dom';
import { Anim, Easing } from '../../../yui/www/animation/animation';

import '../styles/swat-button.css';

export default class SwatButton {
	constructor(id, options) {
		this.id = id;

		this.confirmation_message = options.confirmation_message || '';
		this.processing_message = options.processing_message || '';

		this.throbber_container = null;

		this.handleClick = this.handleClick.bind(this);

		document.addEventListener('DOMContentLoaded', () => {
			this.init();
			if (options.show_processing_throbber) {
				this.initThrobber();
			}
		});
	}

	init() {
		this.button = document.getElementById(this.id);
		this.button.addEventListener('click', this.handleClick);
	}

	initThrobber() {
		this.throbber_container = document.createElement('span');
		this.throbber_container.classList.add(
			'swat-button-processing-throbber'
		);

		if (this.processing_message.length > 0) {
			this.throbber_container.appendChild(
				document.createTextNode(this.processing_message)
			);
			this.throbber_container.classList.add(
				'swat-button-processing-throbber-text'
			);
		} else {
			// the following string is a UTF-8 encoded non breaking space
			this.throbber_container.appendChild(document.createTextNode(' '));
		}

		this.button.parentNode.appendChild(this.throbber_container);
	}

	handleClick(e) {
		var confirmed = (this.confirmation_message)
			? confirm(this.confirmation_message)
			: true;

		if (confirmed) {
			if (this.throbber_container !== null) {
				this.button.disabled = true;
				this.button.classList.add('swat-insensitive')

				// add button to form data manually since we disabled it above
				var div = document.createElement('div');
				var hidden_field = document.createElement('input');
				hidden_field.type = 'hidden';
				hidden_field.name = this.id;
				hidden_field.value = this.button.value;
				div.appendChild(hidden_field);
				this.button.form.appendChild(div);

				this.showThrobber();
				var form = Dom.getAncestorByTagName(
					this.button,
					'form'
				);
				if (form) {
					form.submit(); // needed for IE and WebKit
				}
			}
		} else {
			e.preventDefault();
		}
	}

	showThrobber() {
		var animation = new Anim(
			this.throbber_container,
			{ opacity: { to: 0.5 }},
			1,
			Easing.easingNone
		);

		animation.animate();
	}
}
