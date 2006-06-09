function SwatDateEntry(id)
{
	this.id = id;

	this.year = document.getElementById(id + '_year');
	this.month = document.getElementById(id + '_month');
	this.day = document.getElementById(id + '_day');

	this.swat_time = null;

	var is_ie = (document.addEventListener) ? false : true;
	var self = this;

	function handleChange(event)
	{
		var target;
		if (!event)
			var event = window.event;

		if (event.target)
			target = event.target;
		else if (event.srcElement)
			target = event.srcElement;

		// fix Safari bug
		if (target.nodeType == 3)
			target = target.parentNode;

		self.update(target);
	}

	if (is_ie) {
		if (this.year)
			this.year.attachEvent('onchange', handleChange);

		if (this.month)
			this.month.attachEvent('onchange', handleChange);

		if (this.day)
			this.day.attachEvent('onchange', handleChange);
	} else {
		if (this.year)
			this.year.addEventListener('change', handleChange, true);

		if (this.month)
			this.month.addEventListener('change', handleChange, true);

		if (this.day)
			this.day.addEventListener('change', handleChange, true);
	}

}

SwatDateEntry.prototype.setSwatTime = function(swat_time)
{
	if (typeof SwatTimeEntry != 'undefined' &&
		swat_time instanceof SwatTimeEntry) {
		this.swat_time = swat_time;
		swat_time.swat_date = this;
	}
}

SwatDateEntry.prototype.reset = function(reset_time)
{
	if (this.year)
		this.year.selectedIndex = 0;

	if (this.month)
		this.month.selectedIndex = 0;

	if (this.day)
		this.day.selectedIndex = 0;

	if (this.swat_time && reset_time)
		this.swat_time.reset(false);
}

SwatDateEntry.prototype.parseInt = function(serialized_integer)
{
	var value = parseInt(serialized_integer.replace(/[^\d]*/, ''));
	if (isNaN(value))
		return null;

	return value;
}

SwatDateEntry.prototype.getIntegerIndex = function(flydown, value)
{
	var value;
	for (i = 0; i < flydown.options.length; i++) {
		flydown_value = this.parseInt(flydown.options[i].value);
		if (flydown_value == value)
			return i;
	}
	return null;
}

SwatDateEntry.prototype.getYearIndex = function(year)
{
	return this.getIntegerIndex(this.year, year);
}

SwatDateEntry.prototype.getMonthIndex = function(month)
{
	return this.getIntegerIndex(this.month, month);
}

SwatDateEntry.prototype.getDayIndex = function(day)
{
	return this.getIntegerIndex(this.day, day);
}

SwatDateEntry.prototype.setNow = function(set_time)
{
	var now = new Date();

	if (this.year && this.year.selectedIndex == 0) {
		var this_year = this.getYearIndex(now.getFullYear());

		if (this_year)
			this.year.selectedIndex = this_year;
		else
			this.year.selectedIndex = 1;
	}

	if (this.month && this.month.selectedIndex == 0) {
		var this_month = this.getMonthIndex(now.getMonth() + 1);

		if (this_month)
			this.month.selectedIndex = this_month;
		else
			this.month.selectedIndex = 1;
	}

	if (this.day && this.day.selectedIndex == 0) {
		var this_day = this.getDayIndex(now.getDate());
		if (this_day)
			this.day.selectedIndex = this_day;
		else
			this.day.selectedIndex = 1;
	}

	if (this.swat_time && set_time)
		this.swat_time.setNow(false);
}

SwatDateEntry.prototype.setDefault = function(set_time)
{
	var now = new Date();

	if (this.year && this.year.selectedIndex == 0) {
		/*
		 * Default to this year if it exists in the options. This behaviour
		 * is somewhat different from the others, but just makes common sense.
		 */
		var this_year = this.getYearIndex(now.getFullYear());

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

SwatDateEntry.prototype.update = function(active_flydown)
{
	// month is required for this, so stop if it doesn't exist
	if (!this.month)
		return;

	// don't do anything if we select the blank option
	if (this.parseInt(active_flydown.value) != null) {
		var now = new Date();
		var this_month = now.getMonth() + 1;

		if (this.getMonth() == this_month)
			this.setNow(true);
		else
			this.setDefault(true);
	}
}

SwatDateEntry.prototype.getDay = function()
{
	var day = null;

	if (this.day)
		day = this.parseInt(this.day.value);

	return day;
}

SwatDateEntry.prototype.getMonth = function()
{
	var month = null;

	if (this.month)
		month = this.parseInt(this.month.value);

	return month;
}

SwatDateEntry.prototype.getYear = function()
{
	var year = null;

	if (this.year)
		year = this.parseInt(this.year.value);

	return year;
}

SwatDateEntry.prototype.setDay = function(day)
{
	if (this.day) {
		var this_day = this.getDayIndex(day);

		if (this_day)
			this.day.selectedIndex = this_day;
		else
			this.day.selectedIndex = 0;
	}
}

SwatDateEntry.prototype.setMonth = function(month)
{
	if (this.month) {
		var this_month = this.getMonthIndex(month);

		if (this_month)
			this.month.selectedIndex = this_month;
		else
			this.month.selectedIndex = 0;
	}
}

SwatDateEntry.prototype.setYear = function(year)
{
	if (this.year) {
		var this_year = this.getYearIndex(year);

		if (this_year)
			this.year.selectedIndex = this_year;
		else
			this.year.selectedIndex = 0;
	}
}
