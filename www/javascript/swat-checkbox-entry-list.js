import SwatCheckboxList  from './swat-checkbox-list';
import '../styles/swat-checkbox-entry-list.css';

export default class SwatCheckboxEntryList extends SwatCheckboxList {
	constructor(id) {
		super(id);
		this.entry_list = [];
	}

	init() {
		super.init();

		for (var i = 0; i < this.check_list.length; i++) {
			var option = this.check_list[i];
			this.entry_list[i] = document.getElementById(
				this.id + '_entry_' + option.value
			);
			this.check_list[i]._index = i;
		}

		this.updateFields();
	}

	handleClick(e) {
		super.handleClick(e);
		var target = YAHOO.util.Event.getTarget(e);
		this.toggleEntry(target._index);
	}

	checkAll(checked) {
		super.checkAll(checked);
		for (var i = 0; i < this.check_list.length; i++) {
			this.setEntrySensitivity(i, checked);
		}
	}

	toggleEntry(index) {
		if (this.entry_list[index]) {
			this.setEntrySensitivity(index, this.entry_list[index].disabled);
		}
	};

	setEntrySensitivity(index, sensitivity) {
		if (this.entry_list[index]) {
			if (sensitivity) {
				this.entry_list[index].disabled = false;
				YAHOO.util.Dom.removeClass(
					this.entry_list[index],
					'swat-insensitive'
				);
			} else {
				this.entry_list[index].disabled = true;
				YAHOO.util.Dom.addClass(
					this.entry_list[index],
					'swat-insensitive'
				);
			}
		}
	}

	updateFields() {
		for (var i = 0; i < this.check_list.length; i++) {
			this.setEntrySensitivity(i, this.check_list[i].checked);
		}
	}
}
