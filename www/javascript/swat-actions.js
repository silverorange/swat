function SwatActions(id, values, selected)
{
	this.id = id;
	this.flydown = document.getElementById(id + '_action_flydown');
	this.selected_element = (selected) ?
		document.getElementById(id + '_' + selected) : null;

	this.values = values;

	YAHOO.util.Event.addListener(this.flydown, 'change',
		this.handleChange, this, true);
}

SwatActions.prototype.handleChange = function()
{
	if (this.selected_element)
		YAHOO.util.Dom.addClass(this.selected_element, 'swat-hidden');

	var id = this.id + '_' +
		this.values[this.flydown.selectedIndex];

	this.selected_element = document.getElementById(id);

	if (this.selected_element)
		YAHOO.util.Dom.removeClass(this.selected_element, 'swat-hidden');
}
