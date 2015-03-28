(function($) {

$.widget('swat.dateentry', {
	version: '2.2.3',
	options: {
		useCurrentDate: false
	},
	_create: function() {
		this._year = this.element.find('select.swat-date-entry-year');
		this._month = this.element.find('select.swat-date-entry-month');
		this._day = this.element.find('select.swat-date-entry-day');

		this._calendar = null;
		this._time = null;

		this._on(this._year, {
			change: function(event) { this._update('year'); }
		});
		this._on(this._month, {
			change: function(event) { this._update('month'); }
		});
		this._on(this._day, {
			change: function(event) { this._update('day'); }
		});

		this._buildIndex();
	},
	_buildIndex: function() {
		var indexTable = {
			'year': {},
			'month': {},
			'day': {}
		};

		var reverseIndexTable = {
			'year': {},
			'month': {},
			'day': {}
		};

		this._year.find('option').each(function(index) {
			var value = $(this).data('value');
			if (value !== undefined) {
				indexTable.year[value] = index;
				reverseIndexTable.year[index] = value;
			}
		});

		this._month.find('option').each(function(index) {
			var value = $(this).data('value');
			if (value !== undefined) {
				indexTable.month[value] = index;
				reverseIndexTable.month[index] = value;
			}
		});

		this._day.find('option').each(function(index) {
			var value = $(this).data('value');
			if (value !== undefined) {
				indexTable.day[value] = index;
				reverseIndexTable.day[index] = value;
			}
		});

		this._indexTable = indexTable;
		this._reverseIndexTable = reverseIndexTable;
	},
	_init: function() {
	},
	_setOption: function(key, value) {
		var ret = this._super(key, value);

		if (key === 'disabled') {
			if (value) {
				this._year
					.addClass('swat-insensitive')
					.prop('disabled', true);

				this._month
					.addClass('swat-insensitive')
					.prop('disabled', true);

				this._day
					.addClass('swat-insensitive')
					.prop('disabled', true);
			} else {
				this._year
					.removeClass('swat-insensitive')
					.prop('disabled', false);

				this._month
					.removeClass('swat-insensitive')
					.prop('disabled', false);

				this._day
					.removeClass('swat-insensitive')
					.prop('disabled', false);
			}

			if (this._calendar) {
				this._calendar._setOption('disabled', value);
			}

			if (this._time) {
				this._time._setOption('disabled', value);
			}
		}

		return ret;
	},
	_update: function(what) {
		// month is required for this, so stop if it doesn't exist
		if (this._month.length === 0) {
			return;
		}

		var index = null;
		switch (what) {
		case 'day':
			index = this._day.prop('selectedIndex');
			break;
		case 'month':
			index = this._month.prop('selectedIndex');
			break;
		case 'year':
			index = this._year.prop('selectedIndex');
			break;
		}

		// don't do anything if we select the blank option
		if (index !== 0) {
			var thisMonth = (new Date()).getMonth() + 1;

			if (this._getMonth() === thisMonth && this.options.useCurrentDate) {
				this._setNow(true);
			} else {
				this._setDefault(true);
			}
		}
	},
	_setDefault: function(alsoSetTime) {
		var now = new Date();

		if (this._year.prop('selectedIndex') === 0) {
			// Default to this year if it exists in the options. This behaviour
			// is different from the day and month, but makes common sense.
			var index = this._getIndex('year', now.getFullYear());
			if (index) {
				this._year.prop('selectedIndex', index);
			} else {
				this._year.prop('selectedIndex', 1);
			}
		}

		if (this._month.prop('selectedIndex') === 0) {
			this._month.prop('selectedIndex', 1);
		}

		if (this._day.prop('selectedIndex') === 0) {
			this._day.prop('selectedIndex', 1);
		}

		if (this._time && alsoSetTime) {
			this._time._setDefault(false);
		}
	},
	_setNow: function(alsoSetTime) {
		var now = new Date();

		if (this._year.prop('selectedIndex') === 0) {
			var index = this._getIndex('year', now.getFullYear());
			if (index) {
				this._year.prop('selectedIndex', index);
			} else {
				this._year.prop('selectedIndex', 1);
			}
		}

		if (this._month.prop('selectedIndex') === 0) {
			var index = this._getIndex('month', now.getMonth() + 1);
			if (index) {
				this._month.prop('selectedIndex', index);
			} else {
				this._month.prop('selectedIndex', 1);
			}
		}

		if (this._day.prop('selectedIndex') === 0) {
			var index = this._getIndex('day', now.getDate());
			if (index) {
				this._day.prop('selectedIndex', index);
			} else {
				this._day.prop('selectedIndex', 1);
			}
		}

		if (this._time && alsoSetTime) {
			this._time._setNow(false);
		}
	},
	_getIndex: function(what, value) {
		var index = this._indexTable[what][value];
		if (index === undefined) {
			index = null;
		}
		return index;
	},
	_getValue: function(what, index) {
		var value = this._reverseIndexTable[what][index];
		if (value === undefined) {
			value = null;
		}
		return value;
	},
	_reset: function(alsoResetTime) {
		this._year.prop('selectedIndex', 0);
		this._month.prop('selectedIndex', 0);
		this._day.prop('selectedIndex', 0);
		if (this._time && alsoResetTime) {
			this._time._reset(false);
		}
	},
	_getYear: function() {
		var year = null;

		if (this._year.size() > 0) {
			year = this._getValue('year', this._year.prop('selectedIndex'));
		}

		return year;
	},
	_getMonth: function() {
		var month = null;

		if (this._month.size() > 0) {
			month = this._getValue('month', this._month.prop('selectedIndex'));
		}

		return month;
	},
	_getDay: function() {
		var day = null;

		if (this._day.size() > 0) {
			day = this._getValue('day', this._day.prop('selectedIndex'));
		}

		return day;
	}

/*
SwatDateEntry.prototype.setCalendar = function(calendar)
{
	if (typeof SwatCalendar != 'undefined' &&
		calendar instanceof SwatCalendar) {
		this.calendar = calendar;
		calendar.date_entry = this;
	}
};

SwatDateEntry.prototype.setTimeEntry = function(time_entry)
{
	if (typeof SwatTimeEntry != 'undefined' &&
		time_entry instanceof SwatTimeEntry) {
		this.time_entry = time_entry;
		time_entry.date_entry = this;
	}
};
*/
});

})(jQuery);

$(function() {
	$('.swat-date-entry').each(function() {
		var $entry = $(this);
		var useCurrentDate = (
			$entry.data('use-current-date') === 'use-current-date'
		);
		$entry.dateentry({
			useCurrentDate: useCurrentDate
		});
	});
});
