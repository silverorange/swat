function SwatCheckboxEntryList(id)
{
	this.check_list = document.getElementsByName(id + '[]');
	this.entry_list = [];

	// a reference to a checkall widget
	// (if it exists - set by the SwatCheckAll widget)
	this.check_all = null;

	for (i = 0; i < this.check_list.length; i++) {
		this.entry_list[i] = document.getElementById(
			id + '_entry_' + this.check_list[i].value);

		this.check_list[i]._index = i;
		YAHOO.util.Event.addListener(this.check_list[i], 'click',
			SwatCheckboxEntryList.clickHandler, this);
	}

	this.init();
}

SwatCheckboxEntryList.clickHandler = function(event, object)
{
	object.checkAllInit();
	var target = YAHOO.util.Event.getTarget(event);
	object.toggleEntry(target._index);
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
			YAHOO.util.Dom.removeClass(this.entry_list[index],
				'swat-insensitive');
		} else {
			this.entry_list[index].disabled = true;
			YAHOO.util.Dom.addClass(this.entry_list[index], 'swat-insensitive');
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
