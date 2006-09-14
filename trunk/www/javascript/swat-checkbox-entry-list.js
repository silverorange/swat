function SwatCheckboxEntryList(id)
{
	var self = this;
	this.check_list = document.getElementsByName(id + '[]');
	this.entry_list = [];

	// a reference to a checkall widget
	// (if it exists - set by the SwatCheckAll widget)
	this.check_all = null;

	var is_ie = (document.addEventListener) ? false : true;

	for (i = 0; i < this.check_list.length; i++) {
		this.entry_list[i] = document.getElementById(
			id + '_entry_' + this.check_list[i].value);

		this.check_list[i]._index = i;

		if (is_ie)
			this.check_list[i].attachEvent('onclick', eventHandler);
		else 
			this.check_list[i].addEventListener('change', eventHandler, false);
	}

	function eventHandler(event)
	{
		self.checkAllInit();

		if (typeof event == 'undefined')
			var event = window.event;

		var source;
		if (typeof event.target != 'undefined')
			source = event.target;
		else if (typeof event.srcElement != 'undefined')
			source = event.srcElement;
		else
			return true;

		self.toggleEntry(source._index);
	}

	this.init();
}

// extend regular checkbox list
SwatCheckboxEntryList.prototype = new SwatCheckboxList;

SwatCheckboxEntryList.prototype.toggleEntry = function(index)
{
	if (this.entry_list[index])
		this.setEntrySensitivity(index, this.entry_list[index].disabled);
}

SwatCheckboxEntryList.prototype.setEntrySensitivity =
	function(index, sensitivity)
{
	if (this.entry_list[index]) {
		if (sensitivity) {
			this.entry_list[index].disabled = false;
			this.entry_list[index].className = 
				this.entry_list[index].className.replace(/swat-insensitive/, '');
		} else {
			this.entry_list[index].disabled = true;
			this.entry_list[index].className += ' swat-insensitive';
		}
	}
}

SwatCheckboxEntryList.prototype.init = function()
{
	for (i = 0; i < this.check_list.length; i++)
		this.setEntrySensitivity(i, this.check_list[i].checked);
}

SwatCheckboxEntryList.prototype.checkAll = function(checked)
{
	for (i = 0; i < this.check_list.length; i++) {
		this.check_list[i].checked = checked;
		this.setEntrySensitivity(i, checked);
	}
}
