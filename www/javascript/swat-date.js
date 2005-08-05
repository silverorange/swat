function SwatDate(id)
{
	this.id = id;

	this.year = document.getElementById(id + '_year');
	this.month = document.getElementById(id + '_month');
	this.day = document.getElementById(id + '_day');

	this.swat_time = null;
}

SwatDate.prototype.setSwatTime = function(swat_time)
{
	if (swat_time instanceof SwatTime) {
		this.swat_time = swat_time;
		swat_time.swat_date = this;
	}
}

SwatDate.prototype.reset = function(reset_time)
{
	if (this.year) this.year.selectedIndex = 0;
	if (this.month) this.month.selectedIndex = 0;
	if (this.day) this.day.selectedIndex = 0;

	if (this.swat_time && reset_time)
		this.swat_time.reset(false);
}

SwatDate.prototype.setNow = function(set_time)
{
	var now = new Date();
	
	if (this.year && this.year.selectedIndex == 0) {
		var this_year = find_index(this.year, now.getFullYear());
		
		if (this_year)
			this.year.selectedIndex = this_year;
		else
			this.year.selectedIndex = 1;
	}
	
	if (this.month && this.month.selectedIndex == 0) {
		var this_month = find_index(this.month, (now.getMonth() + 1));
		
		if (this_month)
			this.month.selectedIndex = this_month;
		else
			this.month.selectedIndex = 1;
	}
	
	if (this.day && this.day.selectedIndex == 0) {
		var this_day = find_index(this.day, now.getDate());
		if (this_day)
			this.day.selectedIndex = this_day;
		else
			this.day.selectedIndex = 1;
	}

	if (this.swat_time && set_time)
		this.swat_time.setNow(false);
}

SwatDate.prototype.setDefault = function(set_time)
{
	var now = new Date();
	
	if (this.year && this.year.selectedIndex == 0) {
		/*
		 * Default to this year if it exists in the options. This behaviour
		 * is somewhat different from the others, but just makes common sense.
		 */
		var this_year = find_index(this.year, now.getFullYear());
		
		if (this_year)
			this.year.selectedIndex = this_year;
		else
			this.year.selectedIndex = 1;
	}

	if (this.month && this.month.selectedIndex == 0) 
		this.month.selectedIndex = 1;

	if (this.day && this.day.selectedIndex == 0)
		this.day.selectedIndex = 1;

	if (this.swat_time && set_time)
		this.swat_time.setDefault(false);
}

SwatDate.prototype.set = function(active_flydown)
{
	// month is required for this, so stop if it doesn't exist
	if (!this.month)
		return;
	
	if (active_flydown.value == '') {
		//this.reset(true);
	} else {
		var now = new Date();	
		var this_month = now.getMonth() + 1;
		
		if (this.month.value == this_month)
			this.setNow(true);
		else
			this.setDefault(true);
	}
}
