import { Dom } from '../../../yui/www/dom/dom';
import { Event } from '../../../yui/www/event/event';

import '../styles/swat-time-entry.css';

export default class SwatTimeEntry {
	constructor(id, use_current_time) {
		this.id = id;
		this.use_current_time = use_current_time;

		this.hour = document.getElementById(id + '_hour');
		this.minute = document.getElementById(id + '_minute');
		this.second = document.getElementById(id + '_second');
		this.am_pm = document.getElementById(id + '_am_pm');

		this.twelve_hour = (this.hour !== null && this.am_pm !== null);

		this.date_entry = null;

		if (this.hour) {
			Event.addListener(
				this.hour,
				'change',
				this.handleHourChange,
				this,
				true
			);
		}

		if (this.minute) {
			Event.addListener(
				this.minute,
				'change',
				this.handleMinuteChange,
				this,
				true
			);
		}

		if (this.second) {
			Event.addListener(
				this.second,
				'change',
				this.handleSecondChange,
				this,
				true
			);
		}

		if (this.am_pm) {
			Event.addListener(
				this.am_pm,
				'change',
				this.handleAmPmChange,
				this,
				true
			);
		}

		this.lookup_table = {};
		this.reverse_lookup_table = {};
	}

	setSensitivity(sensitivity) {
		var elements = [];

		if (this.hour) {
			elements.push(this.hour);
		}

		if (this.minute) {
			elements.push(this.minute);
		}

		if (this.second) {
			elements.push(this.second);
		}

		if (this.am_pm) {
			elements.push(this.am_pm);
		}

		for (var i = 0; i < elements.length; i++) {
			if (sensitivity) {
				elements[i].disabled = false;
				Dom.removeClass(elements[i], 'swat-insensitive');
			} else {
				elements[i].disabled = true;
				Dom.addClass(elements[i], 'swat-insensitive');
			}
		}
	}

	handleHourChange() {
		this.update('hour');
	}

	handleMinuteChange() {
		this.update('minute');
	}

	handleSecondChange() {
		this.update('second');
	}

	handleAmPmChange() {
		this.update('am_pm');
	}

	addLookupTable(table_name, table) {
		this.lookup_table[table_name] = table;
		this.reverse_lookup_table[table_name] = {};
		for (var key in table) {
			this.reverse_lookup_table[table_name][table[key]] = key;
		}
	}

	lookup(table_name, key) {
		return this.lookup_table[table_name][key];
	}

	reverseLookup(table_name, key) {
		var value = this.reverse_lookup_table[table_name][key];
		if (value === undefined) {
			value = null;
		}
		return value;
	}

	setDateEntry(date_entry) {
		if (typeof SwatDateEntry !== 'undefined' &&
			date_entry instanceof SwatDateEntry
		) {
			this.date_entry = date_entry;
			date_entry.time_entry = this;
		}
	}

	/**
	 * @deprecated Use setDateEntry() instead.
	 */
	setSwatDate(swat_date) {
		this.setDateEntry(swat_date);
	}

	reset(reset_date) {
		if (this.hour) {
			this.hour.selectedIndex = 0;
		}

		if (this.minute) {
			this.minute.selectedIndex = 0;
		}

		if (this.second) {
			this.second.selectedIndex = 0;
		}

		if (this.am_pm) {
			this.am_pm.selectedIndex = 0;
		}

		if (this.date_entry && reset_date) {
			this.date_entry.reset(false);
		}
	}

	setNow(set_date) {
		var now = new Date();
		var hour = now.getHours();

		if (this.twelve_hour) {
			if (hour < 12) { // 0000-1100 is am
				var am_pm = 1;
			} else { // 1200-2300 is pm
				if (hour != 12) {
					hour -= 12;
				}

				var am_pm = 2;
			}
		}

		if (this.hour && this.hour.selectedIndex === 0) {
			this.hour.selectedIndex = this.lookup('hour', hour);
		}

		if (this.minute && this.minute.selectedIndex === 0) {
			this.minute.selectedIndex = this.lookup('minute', now.getMinutes());
		}

		if (this.second && this.second.selectedIndex === 0) {
			this.second.selectedIndex = this.lookup('second', now.getSeconds());
		}

		if (this.am_pm && this.am_pm.selectedIndex === 0) {
			this.am_pm.selectedIndex = am_pm;
		}

		if (this.date_entry && set_date) {
			this.date_entry.setNow(false);
		}
	}

	setDefault(set_date) {
		if (this.hour && this.hour.selectedIndex === 0) {
			if (this.am_pm) {
				this.hour.selectedIndex = 12;
			} else {
				this.hour.selectedIndex = 1;
			}
		}

		if (this.minute && this.minute.selectedIndex === 0) {
			this.minute.selectedIndex = 1;
		}

		if (this.second && this.second.selectedIndex === 0) {
			this.second.selectedIndex = 1;
		}

		if (this.am_pm && this.am_pm.selectedIndex === 0) {
			this.am_pm.selectedIndex = 1;
		}

		if (this.date_entry && set_date) {
			this.date_entry.setDefault(false);
		}
	}

	update(field) {
		// hour is required for this, so stop if it doesn't exist
		if (!this.hour) {
			return;
		}

		var index;

		switch (field) {
		case 'hour':
			index = this.hour.selectedIndex;
			break;
		case 'minute':
			index = this.minute.selectedIndex;
			break;
		case 'second':
			index = this.second.selectedIndex;
			break;
		case 'am_pm':
			index = this.am_pm.selectedIndex;
			break;
		}

		// don't do anything if we select the blank option
		if (index > 0) {
			var now = new Date();
			var this_hour = now.getHours();

			if (this.twelve_hour) {
				if (this_hour > 12) {
					this_hour -= 12;
				}

				if (this_hour === 0) {
					this_hour = 12;
				}
			}

			var lookup_hour = this.reverseLookup(
				'hour',
				this.hour.selectedIndex
			);

			if (lookup_hour == this_hour && this.use_current_time) {
				this.setNow(true);
			} else {
				this.setDefault(true);
			}
		}
	}
}
