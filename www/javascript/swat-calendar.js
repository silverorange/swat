/**
 * Calendar Widget Version 1.0
 *
 * calendar.js - Calendar Widget JavaScript Library
 *
 * Permission to use, copy, modify, distribute, and sell this software and its
 * documentation for any purpose is hereby granted without fee, provided that
 * the above copyright notice appear in all copies and that both that
 * copyright notice and this permission notice appear in supporting
 * documentation. No representations are made about the suitability of this
 * software for any purpose. It is provided "as is" without express or
 * implied warranty.
 * 
 * Adapted with permission by silverorange. December 2004.
 *
 * @copyright 2004 Tribador Mediaworks, 2005 silverorange Inc.
 * @author Brian Munroe <bmunroe@tribador.net>
 * @author Michael Gauthier <mike@silverorange.com>
 */

/**
 * Creates a SwatCalendar javascript object
 *
 * @param string id
 * @param string start_date
 * @param string end_date
 * @param SwatDateEntry swat_date_entry
 */
function SwatCalendar(id, start_date, end_date, swat_date_entry)
{
	this.id = id;

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

	if (typeof(SwatDateEntry) != 'undefined' &&
		swat_date_entry instanceof SwatDateEntry) {
		this.date = swat_date_entry;
	} else {
		this.date = null;
	}

	this.open = false;
}

/**
 * Decides if a given year is a leap year
 *
 * @param number year
 *
 * @return number
 */
SwatCalendar.isLeapYear = function(year)
{
	return (
		((year % 4 == 0) && (year % 100 != 0)) || (year % 400 == 0)
	) ? 1 : 0;
}

/**
 * Parses a date string into a Date object
 *
 * @param string date_string
 *
 * @return Date
 */
SwatCalendar.stringToDate = function(date_string)
{
	var date_parts = date_string.split('/');

	var mm = date_parts[0] * 1;
	var dd = date_parts[1] * 1;
	var yyyy = date_parts[2] * 1;

	return new Date(yyyy, mm - 1, dd);
}

/**
 * String data
 */
SwatCalendar.week_names = [
	'Sun', 'Mon', 'Tue',
	'Wed', 'Thu', 'Fri',
	'Sat'];

SwatCalendar.month_names = [
	'Jan', 'Feb', 'Mar',
	'Apr', 'May', 'Jun',
	'Jul', 'Aug', 'Sep',
	'Oct', 'Nov', 'Dec'];

SwatCalendar.prev_alt_text = 'Previous Month';
SwatCalendar.next_alt_text = 'Next Month';
SwatCalendar.close_text = 'Close';
SwatCalendar.nodate_text = 'No Date';
SwatCalendar.today_text = 'Today';

/**
 * Sets the values of an associated SwatDateEntry widget to the values
 * of this SwatCalendar
 *
 * @param number year
 * @param number month
 * @param number day
 */
SwatCalendar.prototype.setDateValues = function(year, month, day)
{
	if (this.date !== null) {
		this.date.setYear(year);
		this.date.setMonth(month);
		this.date.setDay(day);
	}
}

/**
 * Closes this calendar
 */
SwatCalendar.prototype.close = function()
{
	calendar_div = document.getElementById(this.id + '_div');
	calendar_div.style.display = 'none';
	this.open = false;
}

/**
 * Closes this calendar and sets the associated date widget to the
 * specified date
 */
SwatCalendar.prototype.closeAndSetDate = function(yyyy, mm, dd)
{
	this.setDateValues(yyyy, mm, dd);
	this.close();
}

/**
 * Closes this calendar and sets the associated date widget as blank
 */
SwatCalendar.prototype.closeAndSetBlank = function()
{
	this.setDateValues('', '', '');
	this.close();
}

/**
 * Closes this calendar and sets the associated date widget to today's
 * date
 */
SwatCalendar.prototype.closeAndSetToday = function()
{
	if (this.date) {
		var today = new Date();
		var mm = today.getMonth() + 1;
		var dd = today.getDate();
		var yyyy = today.getYear();

		if (yyyy < 1000) {
			yyyy = yyyy + 1900;
		}

		this.setDateValues(yyyy, mm, dd);
	}
	this.close();	
}

/**
 * Redraws this calendar without hiding it first
 */
SwatCalendar.prototype.redraw = function()
{
	var start_date = this.start_date;
	var end_date = this.end_date;

	var month_flydown = document.getElementById(this.id + '_month_flydown');
	for (i = 0; i < month_flydown.options.length;i++){
		if (month_flydown.options[i].selected) {
			var mm = month_flydown.options[i].value;
			break;
		}
	}

	var year_flydown = document.getElementById(this.id + '_year_flydown');
	for (i = 0; i < year_flydown.options.length; i++) {
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

	if (yyyy == end_date.getFullYear() && mm > end_date.getMonth())
		yyyy = (end_date.getFullYear() - 1);

	if (yyyy == start_date.getFullYear() && mm < start_date.getMonth())
		yyyy = (start_date.getFullYear() + 1);

	this.draw(yyyy, mm);
}

SwatCalendar.prototype.buildControls = function()
{
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
		this.id + '_month_flydown" onchange="' + this.id + '_obj.redraw();">';

	if (start_date.getYear() == end_date.getYear()) {
		for (i = start_date.getMonth(); i <= end_date.getMonth(); i++) {
			if (i == mm - 1)
				month_array = month_array + '<option value="' + eval(i + 1) + '" ' +
					'selected="selected">' + SwatCalendar.month_names[i] + '</option>';
			else
				month_array = month_array + '<option value="' + eval(i + 1) + '">' +
					SwatCalender.month_names[i] + '</option>';
		}
	} else if ((end_date.getYear() - start_date.getYear()) == 1) {
		for (i = start_date.getMonth(); i <= 11; i++) {
			if (i == mm - 1)
				month_array = month_array + '<option value="' + eval(i + 1) + '" ' +
					'selected="selected">' + SwatCalendar.month_names[i] + '</option>';
			else
				month_array = month_array + '<option value="' + eval(i + 1) + '">' +
					SwatCalender.month_names[i] + '</option>';
		}

		for (i = 0; i <= end_date.getMonth(); i++) {
			if (i == mm - 1)
				month_array = month_array + '<option value="' + eval(i + 1) + '" ' +
					'selected="selected">' + SwatCalendar.month_names[i] + '</option>';
			else
				month_array = month_array + '<option value="' + eval(i + 1) + '">' +
					SwtCalendar.month_names[i] + '</option>';
		}
	} else {
		for (i = 0; i < 12; i++) {
			if (i == mm - 1)
				month_array = month_array + '<option value="' + eval(i + 1) + '" ' +
					'selected="selected">' + SwatCalendar.month_names[i] + '</option>';
			else
				month_array = month_array + '<option value="' + eval(i + 1) + '">' +
					SwatCalendar.month_names[i] + '</option>';
		}
	}

	month_array = month_array + '</select>';

	var year_array = '<select class="swat-calendar-control" id="' +
		this.id + '_year_flydown" onchange="' + this.id + '_obj.redraw();">';

	for (i = start_date.getFullYear(); i <= end_date.getFullYear(); i++) {
		if (i == yyyy)
			year_array = year_array + '<option value="' + i + '" ' +
				'selected="selected">' + i + '</option>';
		else
			year_array = year_array + '<option value="' + i + '">' +
				i + '</option>';
	}

	year_array = year_array + '</select>';

	return (month_array + ' ' + year_array);
}

SwatCalendar.prototype.toggle = function()
{
	if (this.open) {
		this.close();
		calendar_div = document.getElementById(this.id + '_div');
		SwatZIndexManager.lowerElement(calendar_div);
	} else {
		this.draw();
		calendar_div = document.getElementById(this.id + '_div');
		SwatZIndexManager.raiseElement(calendar_div);
	}
}

SwatCalendar.prototype.draw = function()
{
	var start_date = this.start_date;
	var end_date   = this.end_date;

	var yyyy = (arguments[0]) ? arguments[0] : void(0);
	var mm   = (arguments[1]) ? arguments[1] : void(0);
	var dd   = (arguments[2]) ? arguments[2] : void(0);

	var today = new Date();
		
	var start_ts = start_date.getTime();
	var end_ts   = end_date.getTime();
	var today_ts = today.getTime();

	if (!yyyy && !mm) {
		if (this.date) {
			var d = this.date.getDay();
			var m = this.date.getMonth();
			var y = this.date.getYear();

			var day   = (d == null) ? today.getDate()      : parseInt(d);
			var month = (m == null) ? today.getMonth() + 1 : parseInt(m);
			var year  = (y == null) ? today.getYear()      : parseInt(y);

			//TODO: figure out if the last two conditions are ever run
			if (day != 0 && month != 0 && year != 0) {
				var mm = month;
				var dd = day;
				var yyyy = year;
			} else if (today_ts >= start_ts && today_ts <= end_ts) {
				var mm = today.getMonth() + 1;
				var dd = today.getDate();
				var yyyy = today.getYear();
			} else {
				var mm = start_date.getMonth() + 1;
				var dd = start_date.getDate();
				var yyyy = start_date.getYear();
			}
		} else {
			var mm = start_date.getMonth() + 1;
			var dd = start_date.getDate();
			var yyyy = start_date.getYear();
		}
	}

	/*
	 * Mozilla hack,  I am sure there is a more elegent way, but I did it
	 * on a Friday to get a release out the door...
	 */
	if (yyyy < 1000) {
		yyyy = yyyy + 1900;
	}

	var new_date = new Date(yyyy, mm - 1, 1);
	var start_day = new_date.getDay();

	var dom = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

	var this_month = new_date.getMonth() + 1;
	var this_year = new_date.getFullYear();

	var next_month = this_month + 1;
	var prev_month = this_month - 1;
	var prev_year  = this_year;
	var next_year  = this_year;
	if (this_month == 12) {
		next_month = 1;
		next_year  = next_year + 1;
	} else if (this_month == 1) {
		prev_month = 12;
		prev_year  = prev_year - 1;
	}

	var calendar_end = false;
	var calendar_start = false;

	var end_year     = end_date.getFullYear();
	var start_year   = start_date.getFullYear();
	var end_month    = end_date.getMonth();
	var start_month  = start_date.getMonth();

	if (this_year == start_year && this_month == (start_month + 1))
		calendar_start = true;
	else if (this_year == end_year && this_month == (end_month + 1))
		calendar_end = true;

	if (calendar_start) {
		var prev_link = 'return false;';
		var prev_img  = 'arrow-left-off.png';
		var prev_class = 'swat-calendar-arrows-off';
	} else {
		var prev_link = this.id + '_obj.draw(' +
			prev_year + ',' + prev_month + ', 1);';

		var prev_img  = 'arrow-left.png';
		var prev_class = 'swat-calendar-arrows';
	}

	if (calendar_end) {
		var next_link = 'return false;';
		var next_img  = 'arrow-right-off.png';
		var next_class = 'swat-calendar-arrows-off';
	} else {
		var next_link = this.id + '_obj.draw(' +
			next_year + ',' + next_month + ', 1);';

		var next_img  = 'arrow-right.png';
		var next_class = 'swat-calendar-arrows';
	}

	var prev_alt = SwatCalendar.prev_alt_text;
	var next_alt = SwatCalendar.next_alt_text;

	var date_controls =
		'<tr>' +
		'<td class="swat-calendar-control-frame" colspan="7">' +
		'<table cellpadding="0" cellspacing="0" border="0"><tr><td>' +
		'<img class="' + prev_class + '" onclick="' + prev_link + '" ' +
		'src="packages/swat/images/' + prev_img + '" width="23" height="22" ' +
		'alt="' + prev_alt + '" />' +
		'</td><td nowrap width="100%">' +
		this.buildControls(yyyy, mm, dd) +
		'</td><td>' +
		'<img class="' + next_class + '" onclick="' + next_link + '" ' +
		'src="packages/swat/images/' + next_img + '" width="23" height="22" ' +
		'alt="' + next_alt + '" />' +
		'</td></tr></table>' +
		'</td></tr>';

	var begin_table = '<table class="swat-calendar-frame">';

	var week_header = '<tr>';
	for (i = 0; i < SwatCalendar.week_names.length; i++)
		week_header = week_header + '<th class="swat-calendar-header">' +
		SwatCalendar.week_names[i] + '</th>';

	week_header = week_header + '</tr>';

	var close_controls = '<tr>' +
		'<td id="swat-calendar-close-controls" colspan="7">' +
		'<span>' +
		'<a class="swat-calendar-cancel" onclick="' +
		this.id + '_obj.closeAndSetBlank();">' + SwatCalendar.nodate_text + '</a>' +
		'</span>';

	if (today_ts >= start_ts && today_ts <= end_ts)
		close_controls = close_controls +
		'<span>' +
		'<a class="swat-calendar-today" onclick="' +
		this.id + '_obj.closeAndSetToday();">' + SwatCalendar.today_text + '</a>' +
		'</span>';

	close_controls = close_controls +
		'<span>' +
		'<a class="swat-calendar-close" onclick="' +
		this.id + '_obj.close();">' + SwatCalendar.close_text + '</a> ' +
		'</span>' +
		'</td></tr></table>';

	var cur_html = '';
	var end_day = (SwatCalendar.isLeapYear(yyyy) && mm == 2) ? 29 : dom[mm - 1];
	var row_element = 0;
	var cell_data = '';
	var onclick_action = '';

	// calculate the lead gap
	if (start_day != 0) {
		cur_html = '<tr>';
		for (i = 0; i < start_day; i++) {
			cur_html = cur_html + '<td class="swat-calendar-empty-cell">&nbsp;</td>';
			row_element++;
		}
	}
		
	for (i = 1; i <= end_day; i++) {

		cell_data = (dd == i) ? 'swat-calendar-current-cell' : 'swat-calendar-cell';
		onclick_action = this.id + '_obj.closeAndSetDate('+ yyyy + ',' + mm + ',' + i + ');';
		if (calendar_start && i < start_date.getDate()) {
			cell_data = 'swat-calendar-invalid-cell';
			onclick_action = 'return false;';
		} else if (calendar_end && i > end_date.getDate()) {
			cell_data = 'swat-calendar-invalid-cell';
			onclick_action = 'return false;';
		}

		if (row_element == 0) {
			cur_html = cur_html + '<tr><td class="' + cell_data + '" onclick="' + onclick_action + '">' + i + '</td>';
			row_element++;
			continue;
		}

		if (row_element > 0 && row_element < 6) {
			cur_html = cur_html + '<td class="' + cell_data + '" onclick="' + onclick_action + '">' + i + '</td>';
			row_element++;
			continue;
		}

		if (row_element == 6) {
			cur_html = cur_html + '<td class="' + cell_data + '" onclick="' + onclick_action + '">' + i + '</td></tr>';
			row_element = 0;
			continue;
		}
	}

	// calculate the end gap
	if (row_element != 0) {
		for (i = row_element; i <= 6; i++){
			cur_html = cur_html + '<td class="swat-calendar-empty-cell">&nbsp;</td>';
		}
	}

	cur_html = cur_html + '</tr>';

	calendar_div = document.getElementById(this.id + '_div');
	var body = document.getElementsByTagName('body')[0];
	body.appendChild(calendar_div);

	calendar_toggle = document.getElementById(this.id + '_toggle');
	calendar_div.innerHTML = begin_table + date_controls + week_header + cur_html + close_controls;

	var toggle_button = document.getElementById(this.id + '_toggle');

	// this block is required for correct offset calculation in IE6
	// multiple relative nodes results in incorrect offsetLeft calculation
	var x_offset = 0;
	var y_offset = 0;
	var node = toggle_button;
	var last_node_relative = false;
	var position;
	while (node) {
		position = (window.getComputedStyle) ?
			window.getComputedStyle(node, '').position :
			(node.currentStyle ?
				node.currentStyle.position :
				false);

		if (!(position == 'relative' && last_node_relative))
			x_offset += node.offsetLeft;

		y_offset += node.offsetTop;
		last_node_relative = (position == 'relative');
		node = node.offsetParent;
	}
	y_offset += calendar_toggle.offsetHeight;

	calendar_div.style.left = x_offset + 'px';
	calendar_div.style.top = y_offset + 'px';
	calendar_div.style.display = 'block';

	this.open = true;
}

//preload images
if (document.images) {
	image1 = new Image();
	image1.src = 'packages/swat/images/arrow-left.png';
	image2 = new Image();
	image2.src = 'packages/swat/images/arrow-right.png';
	image3 = new Image();
	image3.src = 'packages/swat/images/arrow-left-off.png';
	image4 = new Image();
	image4.src = 'packages/swat/images/arrow-right-off.png';
}
