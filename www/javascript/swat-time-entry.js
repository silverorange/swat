function SwatTimeEntry(id)
{
	this.id = id;

	this.hour = document.getElementById(id + '_hour');
	this.minute = document.getElementById(id + '_minute');
	this.second = document.getElementById(id + '_second');
	this.ampm = document.getElementById(id + '_ampm');

	this.swat_date = null;

	if (this.hour)
		YAHOO.util.Event.addListener(this.hour, 'change',
			this.handleHourChange, this, true);

	if (this.minute)
		YAHOO.util.Event.addListener(this.minute, 'change',
			this.handleMinuteChange, this, true);

	if (this.second)
		YAHOO.util.Event.addListener(this.second, 'change',
			this.handleSecondChange, this, true);

	if (this.ampm)
		YAHOO.util.Event.addListener(this.ampm, 'change',
			this.handleAmPmChange, this, true);

	this.lookup_table = {};
	this.reverse_lookup_table = {};
}

SwatTimeEntry.prototype.handleHourChange = function()
{
	this.update('hour');
}

SwatTimeEntry.prototype.handleMinuteChange = function()
{
	this.update('minute');
}

SwatTimeEntry.prototype.handleSecondChange = function()
{
	this.update('second');
}

SwatTimeEntry.prototype.handleAmPmChange = function()
{
	this.update('ampm');
}

SwatTimeEntry.prototype.addLookupTable = function(table_name, table)
{
	this.lookup_table[table_name] = table;
	this.reverse_lookup_table[table_name] = {};
	for (key in table) {
		this.reverse_lookup_table[table_name][table[key]] = key;
	}
}

SwatTimeEntry.prototype.lookup = function(table_name, key)
{
	return this.lookup_table[table_name][key];
}

SwatTimeEntry.prototype.reverseLookup = function(table_name, key)
{
	return this.reverse_lookup_table[table_name][key];
}

SwatTimeEntry.prototype.setSwatDate = function(swat_date)
{
	if (typeof SwatDateEntry != 'undefined' &&
		swat_date instanceof SwatDateEntry) {
		this.swat_date = swat_date;
		swat_date.swat_time = this;
	}
}

SwatTimeEntry.prototype.reset = function(reset_date)
{
	if (this.hour)
		this.hour.selectedIndex = 0;

	if (this.minute)
		this.minute.selectedIndex = 0;

	if (this.second)
		this.second.selectedIndex = 0;

	if (this.ampm)
		this.ampm.selectedIndex = 0;

	if (this.swat_date && reset_date)
		this.swat_date.reset(false);
}

SwatTimeEntry.prototype.setNow = function(set_date)
{
	var now = new Date();	
	
	if (now.getHours() < 12) {
		var hour = now.getHours();
		var ampm = 1;
	} else {
		var hour = (now.getHours() - 12);
		var ampm = 2;
	}
	
	if (this.hour && this.hour.selectedIndex == 0)
		this.hour.selectedIndex = this.lookup('hour', hour);
		
	if (this.minute && this.minute.selectedIndex == 0)
		this.minute.selectedIndex = this.lookup('minute', now.getMinutes());
		
	if (this.second && this.second.selectedIndex == 0)
		this.second.selectedIndex = this.lookup('second', now.getSeconds());
		
	if (this.ampm && this.ampm.selectedIndex == 0)
		this.ampm.selectedIndex = ampm;
	
	if (this.swat_date && set_date)
		this.swat_date.setNow(false);
}

SwatTimeEntry.prototype.setDefault = function(set_date)
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

SwatTimeEntry.prototype.update = function(field)
{
	// hour is required for this, so stop if it doesn't exist
	if (!this.hour)
		return;

	var index = null;
	switch (field) {
		case 'hour':
			index = this.hour.selectedIndex;
			break;
		case 'minute':
			index = this.minute.selectedIndex;
			break;
		case 'second':
			index = this.second.selectedIndex;
			break;
		case 'ampm':
			index = this.ampm.selectedIndex;
			break;
	}

	// don't do anything if we select the blank option
	if (index != 0) {
		var now = new Date();	
		var this_hour = now.getHours();
		
		if (this_hour > 11)
			this_hour = this_hour - 12;
		
		if (this.reverseLookup('hour', this.hour.selectedIndex) == this_hour)
			this.setNow(true);
		else
			this.setDefault(true);
	}
}
