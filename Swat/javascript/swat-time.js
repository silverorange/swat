function timeSet(id, activeFlydown) {
	var e = timeInit(id);

	//hour is required for this, so stop if it doesn't exist
	if (!hour) return;
	
	if (activeFlydown.value == -1) {
		//timeReset(id, true);
	} else {
		var vDate = new Date();	
		var this_hour = vDate.getHours();
		
		if (this_hour > 11)
			this_hour = (this_hour - 12);
		
		if (e.hour.value == this_hour)
			timeSetNow(id, true);
		else
			timeSetDefault(id, true);
	}
}

function timeInit(id) {
	this.hour   = document.getElementById(id + '_hour');
	this.minute = document.getElementById(id + '_minute');
	this.second = document.getElementById(id + '_second');
	this.ampm   = document.getElementById(id + '_ampm');
	this.date   = document.getElementById(id + '_month');
	return this;
}

function timeReset(id, chkdate) {
	var e = timeInit(id);
	
	if (e.hour)   e.hour.selectedIndex = 0;
	if (e.minute) e.minute.selectedIndex = 0;
	if (e.second) e.second.selectedIndex = 0;
	if (e.ampm)   e.ampm.selectedIndex = 0;
	if (e.date && chkdate)
		dateReset(id, false);
}

function timeSetNow(id, chkdate) {
	var e = timeInit(id);
	var vDate = new Date();	
	
	if (vDate.getHours() < 12) {
		hour_out = vDate.getHours();
		ampm_out = 1;
	} else {
		hour_out = (vDate.getHours() - 12);
		ampm_out = 2;
	}
	
	if (e.hour && e.hour.selectedIndex == 0)
		e.hour.selectedIndex = find_index(e.hour, hour_out);
	if (e.minute && e.minute.selectedIndex == 0)
		e.minute.selectedIndex = find_index(e.minute, vDate.getMinutes());
	if (e.second && e.second.selectedIndex == 0)
		e.second.selectedIndex = find_index(e.second,vDate.getSeconds());
	if (e.ampm && e.ampm.selectedIndex == 0) {
		e.ampm.selectedIndex = ampm_out;
	}
	if (e.date && chkdate)
		dateSetNow(id, false);
}

function timeSetDefault(id, chkdate) {
	var e = timeInit(id);
	
	if (e.hour && e.hour.selectedIndex == 0)
		e.hour.selectedIndex = 1;
	if (e.minute && e.minute.selectedIndex == 0) 
		e.minute.selectedIndex = 1;
	if (e.second && e.second.selectedIndex == 0)
		second.selectedIndex = 1;
	if (e.ampm && e.ampm.selectedIndex == 0) {
		e.ampm.selectedIndex = 1;
	}
	if (e.date && chkdate)
		dateSetDefault(id, false);
}
