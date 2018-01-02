import { Dom } from '../../../yui/www/dom/dom';
import { Event } from '../../../yui/www/event/event';

import '../styles/swat-date-entry.css';

export default class SwatDateEntry {
	constructor(id, use_current_date) {
		this.id = id;
		this.use_current_date = use_current_date;

		this.year = document.getElementById(id + '_year');
		this.month = document.getElementById(id + '_month');
		this.day = document.getElementById(id + '_day');

		this.calendar = null;
		this.time_entry = null;

		if (this.year) {
			Event.addListener(
				this.year,
				'change',
				this.handleYearChange,
				this,
				true
			);
		}

		if (this.month) {
			Event.addListener(
				this.month,
				'change',
				this.handleMonthChange,
				this,
				true
			);
		}

		if (this.day) {
			Event.addListener(
				this.day,
				'change',
				this.handleDayChange,
				this,
				true
			);
		}

		this.lookup_table = {};
		this.reverse_lookup_table = {};
	}

	setSensitivity(sensitivity) {
		var elements = [];

		if (this.year) {
			elements.push(this.year);
		}

		if (this.month) {
			elements.push(this.month);
		}

		if (this.day) {
			elements.push(this.day);
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

		if (this.calendar) {
			this.calendar.setSensitivity(sensitivity);
		}

		if (this.time_entry) {
			this.time_entry.setSensitivity(sensitivity);
		}
	}

	handleYearChange() {
		this.update('year');
	}

	handleMonthChange() {
		this.update('month');
	}

	handleDayChange() {
		this.update('day');
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

	setCalendar(calendar) {
		if (typeof SwatCalendar !== 'undefined' &&
			calendar instanceof SwatCalendar
		) {
			this.calendar = calendar;
			calendar.date_entry = this;
		}
	}

	setTimeEntry(time_entry) {
		if (typeof SwatTimeEntry !== 'undefined' &&
			time_entry instanceof SwatTimeEntry
		) {
			this.time_entry = time_entry;
			time_entry.date_entry = this;
		}
	}

	/**
	 * @deprecated Use setTimeEntry() instead.
	 */
	setSwatTime(swat_time) {
		this.setTimeEntry(swat_time);
	}

	reset(reset_time) {
		if (this.year) {
			this.year.selectedIndex = 0;
		}

		if (this.month) {
			this.month.selectedIndex = 0;
		}

		if (this.day) {
			this.day.selectedIndex = 0;
		}

		if (this.time_entry && reset_time) {
			this.time_entry.reset(false);
		}
	}

	setNow(set_time) {
		var now = new Date();

		if (this.year && this.year.selectedIndex === 0) {
			var this_year = this.lookup('year', now.getFullYear());

			if (this_year) {
				this.year.selectedIndex = this_year;
			} else {
				this.year.selectedIndex = 1;
			}
		}

		if (this.month && this.month.selectedIndex === 0) {
			var this_month = this.lookup('month', now.getMonth() + 1);

			if (this_month) {
				this.month.selectedIndex = this_month;
			} else {
				this.month.selectedIndex = 1;
			}
		}

		if (this.day && this.day.selectedIndex === 0) {
			var this_day = this.lookup('day', now.getDate());
			if (this_day) {
				this.day.selectedIndex = this_day;
			} else {
				this.day.selectedIndex = 1;
			}
		}

		if (this.time_entry && set_time) {
			this.time_entry.setNow(false);
		}
	}

	setDefault(set_time) {
		var now = new Date();

		if (this.year && this.year.selectedIndex === 0) {
			/*
			 * Default to this year if it exists in the options. This behaviour
			 * is somewhat different from the others, but just makes common sense.
			 */
			var this_year = this.lookup('year', now.getFullYear());

			if (this_year) {
				this.year.selectedIndex = this_year;
			} else {
				this.year.selectedIndex = 1;
			}
		}

		if (this.month && this.month.selectedIndex === 0) {
			this.month.selectedIndex = 1;
		}

		if (this.day && this.day.selectedIndex === 0) {
			this.day.selectedIndex = 1;
		}

		if (this.time_entry && set_time) {
			this.time_entry.setDefault(false);
		}
	}

	update(field) {
		// month is required for this, so stop if it doesn't exist
		if (!this.month) {
			return;
		}

		var index = null;
		switch (field) {
			case 'day':
				index = this.day.selectedIndex;
				break;
			case 'month':
				index = this.month.selectedIndex;
				break;
			case 'year':
				index = this.year.selectedIndex;
				break;
		}

		// don't do anything if we select the blank option
		if (index !== 0) {
			var now = new Date();
			var this_month = now.getMonth() + 1;

			if (this.getMonth() == this_month && this.use_current_date) {
				this.setNow(true);
			} else {
				this.setDefault(true);
			}
		}
	}

	getDay() {
		var day = null;

		if (this.day) {
			day = this.reverseLookup('day', this.day.selectedIndex);
		}

		return day;
	}

	getMonth() {
		var month = null;

		if (this.month) {
			month = this.reverseLookup('month', this.month.selectedIndex);
		}

		return month;
	}

	getYear() {
		var year = null;

		if (this.year) {
			year = this.reverseLookup('year', this.year.selectedIndex);
		}

		return year;
	}

	setDay(day) {
		if (this.day) {
			var this_day = this.lookup('day', day);

			if (this_day) {
				this.day.selectedIndex = this_day;
			} else {
				this.day.selectedIndex = 0;
			}
		}
	}

	setMonth(month) {
		if (this.month) {
			var this_month = this.lookup('month', month);

			if (this_month) {
				this.month.selectedIndex = this_month;
			} else {
				this.month.selectedIndex = 0;
			}
		}
	}

	setYear(year) {
		if (this.year) {
			var this_year = this.lookup('year', year);

			if (this_year) {
				this.year.selectedIndex = this_year;
			} else {
				this.year.selectedIndex = 0;
			}
		}
	}
}
