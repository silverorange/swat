import { Event } from '../../../yui/www/event/event';

export default class SwatCheckboxList {
	/**
	 * JavaScript SwatCheckboxList component
	 *
	 * @param id string Id of the matching {@link SwatCheckboxList} object.
	 */
	constructor(id) {
		this.id = id;
		this.check_list = [];
		this.check_all = null; // a reference to a check-all js object
		Event.onDOMReady(this.init, this, true);
	}

	init() {
		var id = this.id;
		var container = document.getElementById(this.id);
		var input_elements = container.getElementsByTagName('INPUT');
		for (var i = 0; i < input_elements.length; i++) {
			if (input_elements[i].type == 'checkbox' &&
				input_elements[i].id.substring(0, id.length) === id) {
				this.check_list.push(input_elements[i]);
			}
		}

		for (var i = 0; i < this.check_list.length; i++) {
			Event.on(
				this.check_list[i],
				'click',
				this.handleClick,
				this,
				true
			);
			Event.on(
				this.check_list[i],
				'dblclick',
				this.handleClick,
				this,
				true
			);
		}

		this.updateCheckAll();
	}

	handleClick(event) {
		this.updateCheckAll();
	}

	updateCheckAll() {
		if (this.check_all === null) {
			return;
		}

		var count = 0;
		for (var i = 0; i < this.check_list.length; i++) {
			if (this.check_list[i].checked || this.check_list[i].disabled) {
				count++;
			} else if (count > 0) {
				break; // can't possibly be all checked or none checked
			}
		}

		this.check_all.setState(count == this.check_list.length);
	}

	checkAll(checked) {
		for (var i = 0; i < this.check_list.length; i++) {
			if (!this.check_list[i].disabled) {
				this.check_list[i].checked = checked;
			}
		}
	}
}
