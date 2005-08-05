function SwatTime(id)
{
	this.id = id;

	this.hour = document.getElementById(id + '_hour');
	this.minute = document.getElementById(id + '_minute');
	this.second = document.getElementById(id + '_second');
	this.ampm = document.getElementById(id + '_ampm');

	this.swat_date = null;
}

SwatTime.prototype.setSwatDate = function(swat_date)
{
	if (swat_date instanceof SwatDate) {
		this.swat_date = swat_date;
		swat_date.swat_time = this;
	}
}

SwatTime.prototype.reset = function(reset_date)
{
	if (this.hour) this.hour.selectedIndex = 0;
	if (this.minute) this.minute.selectedIndex = 0;
	if (this.second) this.second.selectedIndex = 0;
	if (this.ampm) this.ampm.selectedIndex = 0;

	if (this.swat_date && reset_date)
		this.swat_date.reset(false);
}

SwatTime.prototype.setNow = function(set_date)
{
	var now = new Date();	
	
	if (now.getHours() < 12) {
		hour_out = now.getHours();
		ampm_out = 1;
	} else {
		hour_out = (now.getHours() - 12);
		ampm_out = 2;
	}
	
	if (this.hour && this.hour.selectedIndex == 0)
		this.hour.selectedIndex = find_index(this.hour, hour_out);
		
	if (this.minute && this.minute.selectedIndex == 0)
		this.minute.selectedIndex =
			find_index(this.minute, now.getMinutes());
		
	if (this.second && this.second.selectedIndex == 0)
		this.second.selectedIndex =
			find_index(this.second, now.getSeconds());
		
	if (this.ampm && this.ampm.selectedIndex == 0)
		this.ampm.selectedIndex = ampm_out;
	
	if (this.swat_date && set_date)
		this.swat_date.setNow(false);
}

SwatTime.prototype.setDefault = function(set_date)
{
	if (this.hour && this.hour.selectedIndex == 0)
		this.hour.selectedIndex = 1;

	if (this.minute && this.minute.selectedIndex == 0) 
		this.minute.selectedIndex = 1;

	if (this.second && this.second.selectedIndex == 0)
		this.second.selectedIndex = 1;

	if (this.ampm && this.ampm.selectedIndex == 0)
		this.ampm.selectedIndex = 1;

	if (this.swat_date && set_date)
		this.swat_date.setDefault(false);
}

SwatTime.prototype.set = function(active_flydown)
{
	// hour is required for this, so stop if it doesn't exist
	if (!this.hour) return;
	
	if (active_flydown.value == '') {
		//this.reset(true);
	} else {
		var now = new Date();	
		var this_hour = now.getHours();
		
		if (this_hour > 11)
			this_hour = this_hour - 12;
		
		if (this.hour.value == this_hour)
			this.setNow(true);
		else
			this.setDefault(true);
	}
}
