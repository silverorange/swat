/**
* Calendar Widget Version 1.0
* Copyright (c) 2004, Tribador Mediaworks,
*
* Brian Munroe <bmunroe@tribador.net
*
* calendar.js - Calendar Widget JavaScript Library
*
* Permission to use, copy, modify, distribute, and sell this software and its
* documentation for any purpose is hereby granted without fee, provided that
* the above copyright notice appear in all copies and that both that
* copyright notice and this permission notice appear in supporting
* documentation.  No representations are made about the suitability of this
* software for any purpose.  It is provided "as is" without express or
* implied warranty.
*
* 
* Adapted with permission by silverorange. December 2004.
*/

function setDateValues(idtag, year, month, day) {
	y = document.getElementById(idtag + "_year");
	if (y) y.selectedIndex = find_index(y,year);
	
	m = document.getElementById(idtag + "_month");
	if (m) m.selectedIndex = find_index(m,month);
	
	d = document.getElementById(idtag + "_day");
	if (d) d.selectedIndex = find_index(d,day);
}

function _isLeapYear(year) {
	return (
		((year % 4 == 0) && (year % 100 != 0)) || (year % 400 == 0)
	) ? 1 : 0;
}

function setCalendar(idtag, yyyy, mm, dd) {
	//swat-removed: y = document.getElementById(idtag);
	//swat-removed: y.value = mm + "/" + dd + "/" + yyyy;
	setDateValues(idtag,yyyy,mm,dd);
	
	y = document.getElementById(idtag + "_div");
	y.style.display = "none";
}

function closeCal(idtag) {
	t = document.getElementById(idtag + "_div");
	t.style.display = "none";
}

function closeCalNoDate(idtag) {
	//swat-removed: y = document.getElementById(idtag);
	//swat-removed: y.value = "";
	setDateValues(idtag,'','','');
	
	t = document.getElementById(idtag + "_div");
	t.style.display = "none";
}

function closeCalSetToday(idtag) {
	var doDate = new Date();
	var mm = doDate.getMonth()+1;
	var dd = doDate.getDate();
	var yyyy = doDate.getYear();

	if (yyyy < 1000) {
		yyyy = yyyy + 1900;
	}

	setCalendar(idtag, yyyy, mm, dd);
	
	t = document.getElementById(idtag + "_div");
	t.style.display = "none";
}

function redrawCalendar(idtag, start_date, end_date) {

	var x = document.getElementById(idtag + "SelectMonth");
	for (i = 0; i < x.options.length;i++){
		if (x.options[i].selected) {
			var mm = x.options[i].value;
		}
	}

	var y = document.getElementById(idtag + "SelectYear");
	for (i = 0; i < y.options.length; i++) {
		if (y.options[i].selected) {
			var yyyy = y.options[i].value;
		}
	}

	// Who f-ing knows why you need this?
	// If you don't cast it to an int,
	// the browser goes into some kind of
	// infinite loop, atleast in IE6.0/Win32
	//
	mm = mm*1;
	yyyy = yyyy*1;

	if (yyyy == end_date.getFullYear() && mm > end_date.getMonth())
		yyyy = (end_date.getFullYear() - 1);
	if (yyyy == start_date.getFullYear() && mm < start_date.getMonth())
		yyyy = (start_date.getFullYear() + 1);
	
	drawCalendar(idtag, start_date, end_date, yyyy, mm);
}

function _buildCalendarControls() {

	var nw = new Date();

	(arguments[0] ? idtag = arguments[0] : idtag = "");
	(arguments[1] ? yyyy = arguments[1] : yyyy = nw.getYear());
	(arguments[2] ? mm = arguments[2] : mm = nw.getMonth());
	(arguments[3] ? dd = arguments[3] : dd = nw.getDay());

	// Mozilla hack,  I am sure there is a more elegent way, but I did it
	// on a Friday to get a release out the door...
	//
	if (yyyy < 1000) {
		yyyy = yyyy + 1900;
	}

	
	// First build the month selection box
	
	var monthArray = '<select class="swat-calendar-control" id="'
					 + idtag + 'SelectMonth" onChange="redrawCalendar(\''
					 + idtag + '\', start_date, end_date);">';
	
	if (start_date.getYear() == end_date.getYear()) {
		for (i = start_date.getMonth(); i <= end_date.getMonth(); i++) {
			if (i == mm-1)
				monthArray = monthArray + '<option value="' + eval(i + 1) + '" '
							 + 'selected="selected">' + months[i] + '</option>';
			else
				monthArray = monthArray + '<option value="' + eval(i + 1) + '">'
							 + months[i] + '</option>';
		}
	} else if ((end_date.getYear() - start_date.getYear()) == 1) {
		for (i = start_date.getMonth(); i <= 11; i++) {
			if (i == mm-1)
				monthArray = monthArray + '<option value="' + eval(i + 1) + '" '
							 + 'selected="selected">' + months[i] + '</option>';
			else
				monthArray = monthArray + '<option value="' + eval(i + 1) + '">'
							 + months[i] + '</option>';
		}
		
		for (i = 0; i <= end_date.getMonth(); i++) {
			if (i == mm-1)
				monthArray = monthArray + '<option value="' + eval(i + 1) + '" '
							 + 'selected="selected">' + months[i] + '</option>';
			else
				monthArray = monthArray + '<option value="' + eval(i + 1) + '">'
							 + months[i] + '</option>';
		}
	} else {
		for (i = 0; i < 12; i++) {
			if (i == mm-1)
				monthArray = monthArray + '<option value="' + eval(i + 1) + '" '
							 + 'selected="selected">' + months[i] + '</option>';
			else
				monthArray = monthArray + '<option value="' + eval(i + 1) + '">'
							 + months[i] + '</option>';
		}
	}
	
	monthArray = monthArray + "</select>";
	
	
	var yearArray = '<select class ="swat-calendar-control" id="'
					+ idtag + 'SelectYear" onChange="redrawCalendar(\''
					+ idtag + '\', start_date, end_date);">';
	
	for (i = start_date.getFullYear(); i<= end_date.getFullYear(); i++) {
		if (i == yyyy)
			yearArray = yearArray + '<option value="' + i + '" '
						+ 'selected="selected">' + i + '</option>';
		else
			yearArray = yearArray + '<option value="' + i + '">'
						+ i + '</option>';
	}
	
	yearArray = yearArray + "</select>";
	
	return(monthArray + " " + yearArray);
}

function clickWidgetIcon() {
	(arguments[0] ? idtag = arguments[0] : idtag = "");
	(arguments[1] ? start_date = arguments[1] : start_date = "");
	(arguments[2] ? end_date = arguments[2] : end_date = "");

	t = document.getElementById(idtag + "_div");
	
	if (t.style.display == "block") {
		closeCal(idtag);
	} else {
		//note: logic switched, because with styles moved to a class instead
		//of inline, t.style.display returns either '' or 'none' when hidden
		
		var date = new Date();
		if (start_date.length == 10)
			start_date = makeDate(start_date);
		else {
			var year = (date.getFullYear() - 5);
			start_date = new Date(year, 0, 1);
		}
		
		if (end_date.length == 10)
			end_date = makeDate(end_date);
		else {
			var year = (date.getFullYear() + 5);
			end_date = new Date(year, 0, 1);
		}
	
		drawCalendar(idtag, start_date, end_date);
	}
}

function drawCalendar() {

	(arguments[0] ? idtag = arguments[0] : idtag = "");
	(arguments[1] ? start_date = arguments[1] : start_date = "");
	(arguments[2] ? end_date = arguments[2] : end_date = "");
	
	(arguments[3] ? yyyy = arguments[3] : yyyy = void(0));
	(arguments[4] ? mm = arguments[4] : mm = void(0));
	(arguments[5] ? dd = arguments[5] : dd = void(0));

	var today = new Date();
		
	var start_ts = start_date.getTime();
	var end_ts   = end_date.getTime();
	var today_ts = today.getTime();
	
	if (!yyyy && !mm) {
	
		var d = document.getElementById(idtag + '_day');
		var m = document.getElementById(idtag + '_month');
		var y = document.getElementById(idtag + '_year');
		
		var day   = (d.value == '' ? today.getDate()     : parseInt(d.value));
		var month = (m.value == '' ? today.getMonth()+1  : parseInt(m.value));
		var year   = (y.value == '' ? today.getYear()    : parseInt(y.value));
			
		//TODO: figure out if the last two conditions are ever run
		if (day != 0 && month != 0 && year != 0) {
			var mm = month;
			var dd = day;
			var yyyy = year;
		} else if (today_ts >= start_ts && today_ts <= end_ts) {
			var mm = today.getMonth()+1;
			var dd = today.getDate();
			var yyyy = today.getYear();
		} else {
			var mm = start_date.getMonth()+1;
			var dd = start_date.getDate();
			var yyyy = start_date.getYear();
		}
	}

	// Mozilla hack,  I am sure there is a more elegent way, but I did it
	// on a Friday to get a release out the door...
	//
	if (yyyy < 1000) {
		yyyy = yyyy + 1900;
	}

	var newDate = new Date(yyyy,mm-1,1);
	var startDay = newDate.getDay();
	var dom = [31,28,31,30,31,30,31,31,30,31,30,31];
	
	var this_month = (newDate.getMonth() + 1);
	var this_year = newDate.getFullYear();
	
	var next_month = this_month + 1;
	var prev_month = this_month - 1;
	var prev_year  = this_year;
	var next_year  = this_year;
	if (this_month == 12) {
		next_month = 1;
		next_year  = (next_year + 1);
	} else if (this_month == 1) {
		prev_month = 12;
		prev_year  = (prev_year - 1);
	}
	
	var calendar_end = false;
	var calendar_start = false;
	
	var end_year	 = end_date.getFullYear();
	var start_year   = start_date.getFullYear();
	var end_month	= end_date.getMonth();
	var start_month  = start_date.getMonth();
	
	if (this_year == start_year && this_month == (start_month + 1))
		calendar_start = true;
	else if (this_year == end_year && this_month == (end_month + 1))
		calendar_end = true;
	
	
	if (calendar_start) {
		var prev_link = "return false;";
		var prev_img  = 'b_arrowl_off.gif';
		var prev_class = 'swat-calendar-arrows-off';
	} else {
		var prev_link = "drawCalendar('" + idtag + "', start_date, end_date,"
						+ prev_year + "," + prev_month + ",1);";
		var prev_img  = 'b_arrowl.gif';
		var prev_class = 'swat-calendar-arrows';
	}
	
	if (calendar_end) {
		var next_link = "return false;";
		var next_img  = 'b_arrowr_off.gif';
		var next_class = 'swat-calendar-arrows-off';
	} else {
		var next_link = "drawCalendar('" + idtag + "', start_date, end_date,"
						+ next_year + "," + next_month + ",1);";
		var next_img  = 'b_arrowr.gif';
		var next_class = 'swat-calendar-arrows';
	}
	
	var dateControls =
		'<tr>'
		+ '<td class="swat-calendar-control-frame" colspan="7">'
		+ '<table cellpadding="0" cellspacing="0" border="0"><tr><td>'
		+ '<img class="' + prev_class + '" onclick="' + prev_link + '" '
		+ 'src="swat/images/' + prev_img + '" width="23" height="22" />'
		+ '</td><td nowrap width="100%">'
		+ _buildCalendarControls(idtag,yyyy,mm,dd)
		+ '</td><td>'
		+ '<img class="' + next_class + '" onclick="' + next_link + '" '
		+ 'src="swat/images/' + next_img + '" width="23" height="22" />'
		+ '</td></tr></table>'
		+ '</td></tr>';
	
	var beginTable = '<table class="swat-calendar-frame">';
	var calHeader = '<tr>';
	for (i = 0; i < weeks.length; i++)
		calHeader = calHeader + '<td class="swat-calendar-header">'
							  + weeks[i] + "</td>";  
	
	calHeader = calHeader + "</tr>";
	
	var closeControls = '<tr>'
		+ '<td id="swat-calendar-close-controls" colspan="7">';
	
	closeControls = closeControls
		+ '<' + 'span' + '>'
		+ '<a class="swat-calendar-cancel" onclick="'
		+ 'closeCalNoDate(\'' + idtag + '\');">' + txt_nodate + '</a>'
		+ '<' + '/span' + '>';
	
	if (today_ts >= start_ts && today_ts <= end_ts)
		closeControls = closeControls
		+ '<' + 'span' + '>'
		+ '<a class="swat-calendar-today" onclick="'
		+ 'closeCalSetToday(\'' + idtag + '\');">' + txt_today +'</a>'
		+ '<' + '/span' + '>';
		
	closeControls = closeControls
		+ ''
		+ '<' + 'span' + '>'
		+ '<a class="swat-calendar-close" onclick="'
		+ 'closeCal(\'' + idtag + '\');">' + txt_close + '</a> '
		+ '<' + '/span' + '>';
	
	closeControls = closeControls + '</td></tr></table>';
			
	var curHTML = "";
	var curDay = 1;
	var endDay = 0;
	var rowElement = 0;
	var startFlag = 1;
	var endFlag = 0;
	var elementClick = "";
	var cellData = "";

	((_isLeapYear(yyyy) && mm == 2) ? endDay = 29 : endDay = dom[mm-1]);

	// calculate the lead gap
	if (startDay != 0) {
		curHTML = "<tr>";
		for (i = 0; i < startDay; i++) {
			curHTML = curHTML + '<td class="swat-calendar-empty-cell">&nbsp;</td>';
			rowElement++;
		}
	}
		
	for (i=1; i <= endDay; i++) {
		(dd == i ? cellData = "swat-calendar-current-cell" : cellData = "swat-calendar-cell");
		
		var onclickaction = 'setCalendar(\'' + idtag + '\','+ yyyy +',' + mm + ',' + i +');';
		if (calendar_start && i < start_date.getDate()) {
			var onclickaction = 'return false;';
			cellData = "swat-calendar-invalid-cell";
		} else if (calendar_end && i > end_date.getDate()) {
			var onclickaction = 'return false;';
			cellData = "swat-calendar-invalid-cell";
		}

		if (rowElement == 0) {
			curHTML = curHTML + '<tr>' + '<td class="' + cellData + '" onclick="' + onclickaction + '">' + i + '</td>';
			rowElement++;
			continue;
		}

		if (rowElement > 0 && rowElement < 6) {
			curHTML = curHTML + '<td class="' + cellData + '" onclick="' + onclickaction + '">' + i + '</td>';
			rowElement++;
			continue;
		}

		if (rowElement == 6) {
			curHTML = curHTML + '<td class="' + cellData + '" onclick="' + onclickaction + '">' + i + '</td></tr>';
			rowElement = 0;
			continue;
		}
	}

	// calculate the end gap
	if (rowElement != 0) {
		for (i = rowElement; i <= 6; i++){
			curHTML = curHTML + '<td class="swat-calendar-empty-cell">&nbsp;</td>';
		}
	}

	curHTML = curHTML + "</tr>";
	t = document.getElementById(idtag + "_div");
	dateField = document.getElementById(idtag + '_calendar');
	t.innerHTML = beginTable + dateControls + calHeader + curHTML + closeControls;

	// need to write some better browser detection/positioning code here
	// Also, there is a perceived stability issue where the calendar goes offscreen
	// when the widget is right justified..Need some edge detection
	//
	
	
	var kitName = "applewebkit/";
	var tempStr = navigator.userAgent.toLowerCase();
	var pos = tempStr.indexOf(kitName);
	var isAppleWebkit = (pos != -1);
	
	if (isAppleWebkit || document.all) {
		ieOffset = 10;
	} else {
		ieOffset = 0;
	}

	t.style.left = ieOffset + dateField.offsetLeft + "px";
	t.style.display = "block";
}

function createCalendarWidget() {
	(arguments[0] ? idtag = arguments[0] : idtag = "");
	(arguments[1] ? months = arguments[1] : months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"]);
	(arguments[2] ? weeks = arguments[2] : weeks = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"]);
	(arguments[3] ? txt_close = arguments[3] : txt_close = "Close");
	(arguments[4] ? txt_nodate = arguments[4] : txt_nodate = "No Date");
	(arguments[5] ? txt_today = arguments[5] : txt_today = "Today");
}

function makeDate(datestring) {
	var dateparts = datestring.split("/");
	var mm = dateparts[0]*1;
	var dd = dateparts[1]*1;
	var yyyy = dateparts[2]*1;
	return new Date(yyyy,mm-1,dd);
}

//preload images
if (document.images) {
	image1 = new Image();
	image1.src = "swat/images/b_arrowl.gif";
	image2 = new Image();	  
	image2.src = "swat/images/b_arrowr.gif";
	image3 = new Image();	  
	image3.src = "swat/images/b_arrowl_off.gif";
	image4 = new Image();	  
	image4.src = "swat/images/b_arrowr_off.gif";
}
