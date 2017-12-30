/**
 * Calendar Widget Version 1.0
 *
 * Copyright (c) 2004 Tribador Mediaworks, 2004-2011 silverorange Inc.
 *
 * Portions of this code were adapted with permission from the
 * 'Calendar Widget' JavaScript library, which is distributed under the
 * following license:
 *
 *   Permission to use, copy, modify, distribute, and sell this software and
 *   its documentation for any purpose is hereby granted without fee, provided
 *   that the above copyright notice appear in all copies and that both that
 *   copyright notice and this permission notice appear in supporting
 *   documentation. No representations are made about the suitability of this
 *   software for any purpose. It is provided "as is" without express or
 *   implied warranty.
 *
 * @copyright 2004 Tribador Mediaworks, 2004-2012 silverorange Inc.
 * @author    Brian Munroe <bmunroe@tribador.net>
 * @author    Michael Gauthier <mike@silverorange.com>
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */

class SwatCalendar {
	/**
	 * Creates a SwatCalendar JavaScript object
	 *
	 * @param string id
	 * @param string start_date
	 * @param string end_date
	 */
	constructor(id, start_date, end_date) {
		SwatCalendar.preloadImages();

		this.id = id;
		this.is_webkit =
			(/AppleWebKit|Konqueror|KHTML/gi).test(navigator.userAgent);

		var date = new Date();
		if (start_date.length == 10) {
			this.start_date = SwatCalendar.stringToDate(start_date);
		} else {
			var year = (date.getFullYear() - 5);
			this.start_date = new Date(year, 0, 1);
		}

		if (end_date.length == 10) {
			this.end_date = SwatCalendar.stringToDate(end_date);
		} else {
			var year = (date.getFullYear() + 5);
			this.end_date = new Date(year, 0, 1);
		}

		this.date_entry = null;

		this.value = document.getElementById(this.id + '_value');

		// Draw the calendar on window load to prevent "Operation Aborted"
		// errors in MSIE 6 and 7.
		YAHOO.util.Event.on(window, 'load', this.createOverlay, this, true);

		this.open = false;
		this.positioned = false;
		this.drawn = false;
		this.sensitive = true;
	}

	/**
	 * Creates calendar toggle button and overlay widget
	 */
	createOverlay() {
		this.container = document.getElementById(this.id);

		this.drawButton();
		this.overlay = new YAHOO.widget.Overlay(
			this.id + '_div', {
				visible: false,
				constraintoviewport: true,
				effect: {
					effect: SwatCalendar.Effect,
					duration: 0.25
				}
			}
		);

		document.getElementById(this.id + '_div').style.display = 'block';

		this.overlay.render(document.body);
	}

	/**
	 * Associates this calendar control with an existing SwatDateEntry JavaScript
	 * object
	 *
	 * @param SwatDateEntry entry
	 */
	setDateEntry(date_entry) {
		if (typeof(SwatDateEntry) !== 'undefined' &&
			date_entry instanceof SwatDateEntry
		) {
			this.date_entry = date_entry;
			date_entry.calendar = this;
		}
	}

	/**
	 * @deprecated Use setDateEntry() instead.
	 */
	setSwatDateEntry(entry) {
		this.setDateEntry(entry);
	}

	setSensitivity(sensitivity) {
		if (!sensitivity && this.open) {
			this.close();
		}

		if (sensitivity) {
			YAHOO.util.Dom.removeClass(this.container, 'swat-insensitive');

			if (this.drawn) {
				if (this.toggle_button_insensitive.parentNode) {
					this.toggle_button_insensitive.parentNode.removeChild(
						this.toggle_button_insensitive
					);
				}

				this.container.insertBefore(
					this.toggle_button,
					this.container.firstChild
				);
			}

		} else {
			YAHOO.util.Dom.addClass(this.container, 'swat-insensitive');

			if (this.drawn) {
				if (this.toggle_button.parentNode) {
					this.toggle_button.parentNode.removeChild(
						this.toggle_button
					);
				}

				this.container.insertBefore(
					this.toggle_button_insensitive,
					this.container.firstChild
				);
			}
		}

		this.sensitive = sensitivity;
	}

	/**
	 * Displays the toggle button for this calendar control
	 */
	drawButton() {
		this.toggle_button_insensitive = document.createElement('span');
		YAHOO.util.Dom.addClass(
			this.toggle_button_insensitive,
			'swat-calendar-toggle-button'
		);

		this.toggle_button = document.createElement('a');
		this.toggle_button.id = this.id + '_toggle';
		this.toggle_button.href = '#';
		this.toggle_button.title = SwatCalendar.open_toggle_text;
		YAHOO.util.Dom.addClass(
			this.toggle_button,
			'swat-calendar-toggle-button'
		);
		YAHOO.util.Event.on(this.toggle_button, 'click',
			function(e) {
				YAHOO.util.Event.preventDefault(e);
				this.toggle();
			},
			this,
			true
		);

		if (this.is_webkit) {
			// Zero-width-space holds the link open in WebKit browsers. Only apply
			// to WebKit because IE can't display the character correctly.
			this.toggle_button.appendChild(document.createTextNode('\u200b'));
			this.toggle_button_insensitive.appendChild(
				document.createTextNode('\u200b')
			);
		}

		var calendar_div = document.createElement('div');
		calendar_div.id = this.id + '_div';
		calendar_div.style.display = 'none';
		YAHOO.util.Dom.addClass(calendar_div, 'swat-calendar-div');

		var overlay_header = document.createElement('div');
		YAHOO.util.Dom.addClass(overlay_header, 'hd');

		var overlay_body = document.createElement('div');
		YAHOO.util.Dom.addClass(overlay_body, 'bd');

		var overlay_footer = document.createElement('div');
		YAHOO.util.Dom.addClass(overlay_footer, 'ft');

		calendar_div.appendChild(overlay_header);
		calendar_div.appendChild(overlay_body);
		calendar_div.appendChild(overlay_footer);

		if (this.sensitive) {
			this.container.appendChild(this.toggle_button);
		} else {
			this.container.appendChild(this.toggle_button_insensitive);
		}
		this.container.appendChild(calendar_div);

		this.drawn = true;
	}

	/**
	 * Sets the values of an associated SwatDateEntry widget to the values
	 * of this SwatCalendar
	 *
	 * @param number year
	 * @param number month
	 * @param number day
	 */
	setDateValues(year, month, day) {
		// make sure the date is in the valid range
		var date = new Date(year, month - 1, day);
		if (date < this.start_date) {
			year = this.start_date.getFullYear();
			month = this.start_date.getMonth() + 1;
			day = this.start_date.getDate();
		} else if (date >= this.end_date) {
			year = this.end_date.getFullYear();
			month = this.end_date.getMonth() + 1;
			day = this.end_date.getDate();
		}

		if (this.date_entry === null) {
			// save internal date value in MM/DD/YYYY format
			month = (month < 10) ? '0' + month : month;
			this.value.value = month + '/' + day + '/' + year;
		} else {
			this.date_entry.setYear(year);
			this.date_entry.setMonth(month);
			this.date_entry.setDay(day);
		}

		this.redraw();
	}

	/**
	 * Sets the associated date widget to the specified date and updates the
	 * calendar display
	 */
	setDate(element, yyyy, mm, dd) {
		this.setDateValues(yyyy, mm, dd);
		if (this.selected_element) {
			this.selected_element.className = 'swat-calendar-cell';
		}

		element.className = 'swat-calendar-current-cell';

		this.selected_element = element;
	}

	/**
	 * Closes this calendar
	 */
	close() {
		this.overlay.hide();
		this.open = false;
		YAHOO.util.Event.removeListener(
			document,
			'click',
			this.handleDocumentClick
		);
	}

	/**
	 * Closes this calendar and sets the associated date widget to the
	 * specified date
	 */
	closeAndSetDate(yyyy, mm, dd) {
		this.setDateValues(yyyy, mm, dd);
		this.close();
	}

	/**
	 * Closes this calendar and sets the associated date widget as blank
	 */
	closeAndSetBlank() {
		this.setBlank();
		this.close();
	}

	/**
	 * Sets the associated date widget as blank
	 */
	setBlank() {
		if (this.date_entry !== null) {
			this.date_entry.setYear('');
			this.date_entry.setMonth('');
			this.date_entry.setDay('');
			if (this.date_entry.time_entry !== null) {
				this.date_entry.time_entry.reset();
			}
		}
	}

	/**
	 * Closes this calendar and sets the associated date widget to today's
	 * date
	 */
	closeAndSetToday() {
		this.setToday();
		this.close();
	}

	/**
	 * Sets the associated date widget to today's date
	 */
	setToday() {
		var today = new Date();
		var mm = today.getMonth() + 1;
		var dd = today.getDate();
		var yyyy = today.getYear();

		if (yyyy < 1000) {
			yyyy = yyyy + 1900;
		}

		this.setDateValues(yyyy, mm, dd);
	}

	/**
	 * Redraws this calendar without hiding it first
	 */
	redraw() {
		var start_date = this.start_date;
		var end_date = this.end_date;

		var month_flydown = document.getElementById(this.id + '_month_flydown');
		for (var i = 0; i < month_flydown.options.length;i++) {
			if (month_flydown.options[i].selected) {
				var mm = month_flydown.options[i].value;
				break;
			}
		}

		var year_flydown = document.getElementById(this.id + '_year_flydown');
		for (var i = 0; i < year_flydown.options.length; i++) {
			if (year_flydown.options[i].selected) {
				var yyyy = year_flydown.options[i].value;
				break;
			}
		}

		/*
		 * Who knows why you need this? If you don't cast it to a number,
		 * the browser goes into some kind of infinite loop -- at least in
		 * IE6.0/Win32
		 */
		mm = mm * 1;
		yyyy = yyyy * 1;

		if (yyyy == end_date.getFullYear() && mm > end_date.getMonth()) {
			yyyy = (end_date.getFullYear() - 1);
		}

		if (yyyy == start_date.getFullYear() && mm < start_date.getMonth()) {
			yyyy = (start_date.getFullYear() + 1);
		}

		this.draw(yyyy, mm);
	}

	buildControls() {
		var today = new Date();

		var start_date = this.start_date;
		var end_date = this.end_date;

		var yyyy = (arguments[0]) ? arguments[0] : today.getYear();
		var mm = (arguments[1]) ? arguments[1] : today.getMonth();
		var dd = (arguments[2]) ? arguments[2] : today.getDay();

		/*
		 * Mozilla hack,  I am sure there is a more elegent way, but I did it
		 * on a Friday to get a release out the door...
		 */
		if (yyyy < 1000) {
			yyyy = yyyy + 1900;
		}

		// First build the month selection box
		var month_array = '<select class="swat-calendar-control" id="' +
			this.id + '_month_flydown" onchange="' + this.id +
			'_obj.redraw();">';

		if (start_date.getYear() === end_date.getYear()) {
			for (var i = start_date.getMonth(); i <= end_date.getMonth(); i++) {
				if (i === mm - 1) {
					month_array = month_array + '<option value="' +
						eval(i + 1) + '" ' + 'selected="selected">' +
						SwatCalendar.month_names[i] + '</option>';
				} else {
					month_array = month_array + '<option value="' +
						eval(i + 1) + '">' + SwatCalendar.month_names[i] +
						'</option>';
				}
			}
		} else if ((end_date.getYear() - start_date.getYear()) === 1) {
			for (var i = start_date.getMonth(); i <= 11; i++) {
				if (i === mm - 1) {
					month_array = month_array + '<option value="' +
						eval(i + 1) + '" ' + 'selected="selected">' +
						SwatCalendar.month_names[i] + '</option>';
				} else {
					month_array = month_array + '<option value="' +
						eval(i + 1) + '">' + SwatCalendar.month_names[i] +
						'</option>';
				}
			}

			for (var i = 0; i <= end_date.getMonth(); i++) {
				if (i === mm - 1) {
					month_array = month_array + '<option value="' +
						eval(i + 1) + '" ' + 'selected="selected">' +
						SwatCalendar.month_names[i] + '</option>';
				} else {
					month_array = month_array + '<option value="' +
						eval(i + 1) + '">' + SwatCalendar.month_names[i] +
						'</option>';
				}
			}
		} else {
			for (var i = 0; i < 12; i++) {
				if (i === mm - 1) {
					month_array = month_array + '<option value="' +
						eval(i + 1) + '" ' + 'selected="selected">' +
						SwatCalendar.month_names[i] + '</option>';
				} else {
					month_array = month_array + '<option value="' +
						eval(i + 1) + '">' + SwatCalendar.month_names[i] +
						'</option>';
				}
			}
		}

		month_array = month_array + '</select>';

		var year_array = '<select class="swat-calendar-control" id="' +
			this.id + '_year_flydown" onchange="' + this.id +
			'_obj.redraw();">';

		for (var i = start_date.getFullYear(); i <= end_date.getFullYear(); i++) {
			if (i == yyyy) {
				year_array = year_array + '<option value="' + i + '" ' +
					'selected="selected">' + i + '</option>';
			} else {
				year_array = year_array + '<option value="' + i + '">' +
					i + '</option>';
			}
		}

		year_array = year_array + '</select>';

		return (month_array + '&nbsp;' + year_array);
	}

	toggle() {
		if (this.open) {
			this.close();
			this.toggle_button.title = SwatCalendar.open_toggle_text;
		} else {
			this.draw();
			this.toggle_button.title = SwatCalendar.close_toggle_text;
		}
	}

	draw() {
		var start_date = this.start_date;
		var end_date = this.end_date;

		var yyyy = (arguments[0]) ? arguments[0] : null;
		var mm = (arguments[1]) ? arguments[1] : null;
		var dd = (arguments[2]) ? arguments[2] : null;

		var today = new Date();

		var start_ts = start_date.getTime();
		var end_ts = end_date.getTime();
		var today_ts = today.getTime();

		if (yyyy === null && mm === null) {
			if (this.date_entry) {
				var d = this.date_entry.getDay();
				var m = this.date_entry.getMonth();
				var y = this.date_entry.getYear();

				var day   = (d === null) ? today.getDate() : parseInt(d, 10);
				var month = (m === null) ? today.getMonth() + 1 : parseInt(m, 10);
				var year  = (y === null) ? today.getYear() : parseInt(y, 10);

				//TODO: figure out if the last two conditions are ever run
				if (day !== 0 && month !== 0 && year !== 0) {
					mm = month;
					dd = day;
					yyyy = year;
				} else if (today_ts >= start_ts && today_ts <= end_ts) {
					mm = today.getMonth() + 1;
					dd = today.getDate();
					yyyy = today.getYear();
				} else {
					mm = start_date.getMonth() + 1;
					dd = start_date.getDate();
					yyyy = start_date.getYear();
				}
			} else if (this.value.value) {
				var date = SwatCalendar.stringToDate(this.value.value);
				dd   = date.getDate();
				mm   = date.getMonth() + 1;
				yyyy = date.getFullYear();
			} else {
				mm = start_date.getMonth() + 1;
				dd = start_date.getDate();
				yyyy = start_date.getYear();
			}
		} else if (dd === null) {
			if (this.date_entry) {
				var d = this.date_entry.getDay();
				var m = this.date_entry.getMonth();
				var y = this.date_entry.getYear();

				var day   = (d === null) ? today.getDate() : parseInt(d, 10);
				var month = (m === null) ? today.getMonth() + 1 : parseInt(m, 10);
				var year  = (y === null) ? today.getYear() : parseInt(y, 10);

				if (mm == month && yyyy == year) {
					dd = day;
				}
			} else if (this.value.value) {
				var date = SwatCalendar.stringToDate(this.value.value);
				var day   = date.getDate();
				var month = date.getMonth() + 1;
				var year  = date.getFullYear();
				if (mm == month && yyyy == year) {
					dd = day;
				}
			} else {
				mm = start_date.getMonth() + 1;
				dd = start_date.getDate();
				yyyy = start_date.getYear();
			}
		}

		/*
		 * Mozilla hack,  I am sure there is a more elegent way, but I did it
		 * on a Friday to get a release out the door...
		 */
		if (yyyy < 1000) {
			yyyy = yyyy + 1900;
		}

		// sanity check. make sure the date is in the valid range
		var display_date = new Date(yyyy, mm - 1, dd);
		if (display_date < this.start_date) {
			yyyy = this.start_date.getFullYear();
			mmm = this.start_date.getMonth() + 1;
			dd = this.start_date.getDate();
		} else if (display_date >= this.end_date) {
			yyyy = this.end_date.getFullYear();
			mm = this.end_date.getMonth() + 1;
			dd = this.end_date.getDate();
		}

		var new_date = new Date(yyyy, mm - 1, 1);
		var start_day = new_date.getDay();

		var dom = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

		var this_month = new_date.getMonth() + 1;
		var this_year = new_date.getFullYear();

		var next_month = this_month + 1;
		var prev_month = this_month - 1;
		var prev_year = this_year;
		var next_year = this_year;
		if (this_month == 12) {
			next_month = 1;
			next_year = next_year + 1;
		} else if (this_month == 1) {
			prev_month = 12;
			prev_year = prev_year - 1;
		}

		var end_year = end_date.getFullYear();
		var start_year = start_date.getFullYear();
		var end_month = end_date.getMonth();
		var start_month = start_date.getMonth();

		var calendar_start =
			(this_year === start_year && this_month === (start_month + 1));

		var calendar_end =
			(this_year === end_year && this_month === (end_month + 1));

		if (calendar_start) {
			var prev_link = 'return false;';
			var prev_img  = 'go-previous-insensitive.png';
			var prev_class = 'swat-calendar-arrows-off';
		} else {
			var prev_link = this.id + '_obj.draw(' +
				prev_year + ',' + prev_month + '); ' +
				'SwatCalendar.stopEventPropagation(event || window.event);"';

			var prev_img  = 'go-previous.png';
			var prev_class = 'swat-calendar-arrows';
		}

		if (calendar_end) {
			var next_link = 'return false;';
			var next_img  = 'go-next-insensitive.png';
			var next_class = 'swat-calendar-arrows-off';
		} else {
			var next_link = this.id + '_obj.draw(' +
				next_year + ',' + next_month + '); ' +
				'SwatCalendar.stopEventPropagation(event || window.event);"';

			var next_img  = 'go-next.png';
			var next_class = 'swat-calendar-arrows';
		}

		var prev_alt = SwatCalendar.prev_alt_text;
		var next_alt = SwatCalendar.next_alt_text;

		var date_controls =
			'<tr>' +
			'<td class="swat-calendar-control-frame" colspan="7">' +
			'<table cellpadding="0" cellspacing="0" border="0" width="100%">' +
			'<tr><td class="swat-calendar-control-arrow">' +
			'<img class="' + prev_class + '" onclick="' + prev_link + '" ' +
			'src="packages/swat/images/' + prev_img + '" width="16" ' +
			'height="16" alt="' + prev_alt + '" />' +
			'</td><td class="swat-calendar-control-flydowns">' +
			this.buildControls(yyyy, mm, dd) +
			'</td><td class="swat-calendar-control-arrow">' +
			'<img class="' + next_class + '" onclick="' + next_link + '" ' +
			'src="packages/swat/images/' + next_img + '" width="16" ' +
			'height="16" alt="' + next_alt + '" />' +
			'</td></tr></table>' +
			'</td></tr>';

		var begin_table = '<table class="swat-calendar-frame" cellspacing="0">';

		var week_header = '<tr>';
		for (var i = 0; i < SwatCalendar.week_names.length; i++) {
			if (i == SwatCalendar.week_names.length - 1) {
				week_header += '<th class="swat-calendar-last-header">' +
					SwatCalendar.week_names[i] + '</th>';
			} else {
				week_header += '<th class="swat-calendar-header">' +
					SwatCalendar.week_names[i] + '</th>';
			}
		}

		week_header += '</tr>';

		var close_controls = '<tr>' +
			'<td class="swat-calendar-close-controls" colspan="7">';

		if (this.date_entry !== null) {
			close_controls +=
				'<a class="swat-calendar-cancel" href="javascript:' +
				this.id + '_obj.setBlank();">' + SwatCalendar.nodate_text +
				'</a>&nbsp;';
		}

		if (today_ts >= start_ts && today_ts <= end_ts) {
			close_controls +=
				'<a class="swat-calendar-today" href="javascript:' +
				this.id + '_obj.setToday();">' + SwatCalendar.today_text +
				'</a>&nbsp;';
		}

		close_controls +=
			'<a class="swat-calendar-close" href="javascript:' +
			this.id + '_obj.close();">' + SwatCalendar.close_text + '</a> ' +
			'</td></tr></table>';

		var cur_html = '';
		var end_day = (SwatCalendar.isLeapYear(yyyy) && mm === 2)
			? 29
			: dom[mm - 1];

		var prev_end_day = (SwatCalendar.isLeapYear(yyyy) && prev_month === 2)
			? 29
			: dom[prev_month - 1];

		var cell_class = '';
		var cell_current = '';
		var onclick = '';

		var cell = 0;
		for (var row = 0; row < 6; row++) {
			cur_html += '<tr>';
			for (var col = 0; col < 7; col++) {
				cell++;
				// this month days
				if (cell > start_day && cell <= (start_day + end_day)) {
					day = cell - start_day;

					if (dd == day) {
						cell_class = 'swat-calendar-current-cell';
						cell_current = 'id="' + this.id + '_current_cell" ';
					} else {
						cell_class = 'swat-calendar-cell';
						cell_current = '';
					}

					onclick = ' onclick="' + this.id + '_obj.setDateValues(' +
						yyyy + ',' + mm + ',' + day + '); ' +
						'SwatCalendar.stopEventPropagation(event || window.event);"';

					if (calendar_start && day < start_date.getDate()) {
						cell_class = 'swat-calendar-invalid-cell';
						onclick = '';
					} else if (calendar_end && day > end_date.getDate()) {
						cell_class = 'swat-calendar-invalid-cell';
						onclick = '';
					}

					cur_html += '<td ' + cell_current + 'class="' + cell_class +
						'"' + onclick + '>' + day + '</td>';

				// previous month end days
				} else if (cell <= start_day) {
					cur_html += '<td class="swat-calendar-empty-cell">' +
						(prev_end_day - start_day + cell) + '</td>';

				// next month start days
				} else {
					cur_html += '<td class="swat-calendar-empty-cell">' +
						(cell - end_day - start_day) + '</td>';
				}
			}
			cur_html += '</tr>';
		}

		// draw calendar
		var calendar_div = document.getElementById(this.id + '_div');
		calendar_div.childNodes[1].innerHTML =
			begin_table + date_controls + week_header +
			cur_html + close_controls;

		if (!this.open) {

			// only set position once
			if (!this.positioned) {
				this.overlay.cfg.setProperty('context',
					[this.toggle_button, 'tl', 'bl']);

				this.positioned = true;
			}

			this.overlay.show();
			this.open = true;

			YAHOO.util.Event.on(
				document,
				'click',
				this.handleDocumentClick,
				this,
				true
			);
		}
	}

	handleDocumentClick(e) {
		var close = true;

		var target = YAHOO.util.Event.getTarget(e);

		if (target === this.toggle_button || target === this.overlay.element) {
			close = false;
		} else {
			while (target.parentNode) {
				target = target.parentNode;
				if (target === this.overlay.element ||
					YAHOO.util.Dom.hasClass(target, 'swat-calendar-frame')) {
					close = false;
					break;
				}
			}
		}

		if (close) {
			this.close();
		}
	}
}

/**
 * Custom effect for hiding and showing the overlay
 *
 * Shows instantly and hides with configurable fade duration.
 */
SwatCalendar.Effect = function(overlay, duration) {
	var effect = YAHOO.widget.ContainerEffect.FADE(overlay, duration);
	effect.attrIn = {
		attributes: { opacity: { from: 0, to: 1 } },
		duration: 0,
		method: YAHOO.util.Easing.easeIn
	};
	effect.init();
	return effect;
}

SwatCalendar.images_preloaded = false;

SwatCalendar.preloadImages = function() {
	if (SwatCalendar.images_preloaded) {
		return;
	}

	SwatCalendar.go_previous_insensitive_image = new Image();
	SwatCalendar.go_previous_insensitive_image.src =
		'packages/swat/images/go-previous-insensitive.png';

	SwatCalendar.go_next_insensitive_image = new Image();
	SwatCalendar.go_next_insensitive_image.src =
		'packages/swat/images/go-next-insensitive.png';

	SwatCalendar.go_previous_image = new Image();
	SwatCalendar.go_previous_image.src = 'packages/swat/images/go-previous.png';

	SwatCalendar.go_next_image = new Image();
	SwatCalendar.go_next_image.src = 'packages/swat/images/go-next.png';

	SwatCalendar.images_preloaded = true;
};

// string data
SwatCalendar.week_names = [
	'Sun', 'Mon', 'Tue',
	'Wed', 'Thu', 'Fri',
	'Sat'
];

SwatCalendar.month_names = [
	'Jan', 'Feb', 'Mar',
	'Apr', 'May', 'Jun',
	'Jul', 'Aug', 'Sep',
	'Oct', 'Nov', 'Dec'
];

SwatCalendar.prev_alt_text = 'Previous Month';
SwatCalendar.next_alt_text = 'Next Month';
SwatCalendar.close_text = 'Close';
SwatCalendar.nodate_text = 'No Date';
SwatCalendar.today_text = 'Today';

SwatCalendar.open_toggle_text = 'open calendar';
SwatCalendar.close_toggle_text = 'close calendar';

/**
 * Decides if a given year is a leap year
 *
 * @param number year
 *
 * @return number
 */
SwatCalendar.isLeapYear = function(year) {
	return (
		((year % 4 === 0) && (year % 100 !== 0)) || (year % 400 === 0)
	) ? 1 : 0;
};

/**
 * Parses a date string into a Date object
 *
 * @param string date_string
 *
 * @return Date
 */
SwatCalendar.stringToDate = function(date_string) {
	var date_parts = date_string.split('/');

	var mm = date_parts[0] * 1;
	var dd = date_parts[1] * 1;
	var yyyy = date_parts[2] * 1;

	return new Date(yyyy, mm - 1, dd);
};


SwatCalendar.stopEventPropagation = function(e) {
	if (e.stopPropagation) {
		e.stopPropagation();
	} else if (window.event) {
		window.event.cancelBubble = true;
	}

};

module.exports = SwatCalendar;
