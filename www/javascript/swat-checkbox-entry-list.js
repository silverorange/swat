function SwatCheckboxEntryList(id)
{
	this.entry_list = [];

	SwatCheckboxEntryList.superclass.constructor.call(this, id);

	for (i = 0; i < this.check_list.length; i++) {
		this.entry_list[i] = document.getElementById(
			id + '_entry_' + this.check_list[i].value);

		this.check_list[i]._index = i;
	}

	this.updateFields();
}

YAHOO.lang.extend(SwatCheckboxEntryList, SwatCheckboxList, {

handleClick: function(event)
{
	SwatCheckboxEntryList.superclass.handleClick.call(this, event);
	var target = YAHOO.util.Event.getTarget(event);
	this.toggleEntry(target._index);
},

checkAll: function(checked)
{
	SwatCheckboxEntryList.superclass.checkAll.call(this, checked);
	for (i = 0; i < this.check_list.length; i++)
		this.setEntrySensitivity(i, checked);
}

});

SwatCheckboxEntryList.prototype.toggleEntry = function(index)
{
	if (this.entry_list[index])
		this.setEntrySensitivity(index, this.entry_list[index].disabled);
}

SwatCheckboxEntryList.prototype.setEntrySensitivity = function(index,
	sensitivity)
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

SwatCheckboxEntryList.prototype.updateFields = function()
{
	for (i = 0; i < this.check_list.length; i++)
		this.setEntrySensitivity(i, this.check_list[i].checked);
}
