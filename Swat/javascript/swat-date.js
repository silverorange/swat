<?php
require_once('Swat/javascript/swat-find-index.js');
?>

function dateSet(id, activeFlydown) {
	var e = dateInit(id);

	//hour is required for this, so stop if it doesn't exist
	if (!e.month) return;
	
	if (activeFlydown.value == -1) {
		dateReset(id, true);
	} else {
		var vDate = new Date();	
		var this_month = vDate.getMonth();
		
		if (e.month.value == (this_month + 1))
			dateSetNow(id, true);
		else
			dateSetDefault(id, true);
	}
}

function dateInit(id) {
	this.year   = document.getElementById(id + '_year');
	this.month  = document.getElementById(id + '_month');
	this.day    = document.getElementById(id + '_day');
	this.time   = document.getElementById(id + '_hour');
	return this;
}

function dateReset(id, chktime) {
	var e = dateInit(id);
	
	if (e.year)   e.year.selectedIndex = 0;
	if (e.month)  e.month.selectedIndex = 0;
	if (e.day)    e.day.selectedIndex = 0;
	if (e.time && chktime)
		e.timeReset(id, false);
}

function dateSetNow(id, chktime) {
	var e = dateInit(id);
	var vDate = new Date();
	
	if (e.year && e.year.selectedIndex == 0) {
		this_year = find_index(e.year, vDate.getFullYear());
		if (this_year) e.year.selectedIndex = this_year;
		else e.year.selectedIndex = 1;
	}
	if (e.month && e.month.selectedIndex == 0) {
		this_month = find_index(e.month, (vDate.getMonth() + 1));
		if (this_month) e.month.selectedIndex = this_month;
		else e.month.selectedIndex = 1;
	}
	if (e.day && e.day.selectedIndex == 0) {
		this_day = find_index(e.day, vDate.getDate());
		if (this_day) e.day.selectedIndex = this_day;
		else e.day.selectedIndex = 1;
	}
	if (e.time && chktime)
		timeSetNow(id, false);
}

function dateSetDefault(id, chktime) {
	var e = dateInit(id);
	var vDate = new Date();
	
	if (e.year && e.year.selectedIndex == 0) {
		// default to this year if it exists in the options
			// this behaviour is somewhat different from the others, but just
			// makes common sense
		this_year = find_index(e.year, vDate.getFullYear());
		if (this_year) e.year.selectedIndex = this_year;
		else e.year.selectedIndex = 1;
	}
	if (e.month && e.month.selectedIndex == 0) 
		e.month.selectedIndex = 1;
	if (e.day && e.day.selectedIndex == 0)
		e.day.selectedIndex = 1;
	if (e.time && chktime)
		timeSetDefault(id, false);
}