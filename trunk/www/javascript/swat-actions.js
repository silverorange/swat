function SwatActions(id, values, selected)
{
	var self = this;

	this.id = id;
	this.flydown = document.getElementById(id + '_action_flydown');
	this.selected_element = (selected) ?
		document.getElementById(id + '_' + selected) : null;

	this.values = values;

	YAHOO.util.Event.addListener(this.flydown, 'change',
		SwatActions.handleChange, this);
}

SwatActions.handleChange = function(event, object)
{
	if (object.selected_element)
		YAHOO.util.Dom.addClass(object.selected_element, 'swat-hidden');

	var id = object.id + '_' +
		object.values[object.flydown.selectedIndex];

	object.selected_element = document.getElementById(id);

	if (object.selected_element)
		YAHOO.util.Dom.removeClass(object.selected_element, 'swat-hidden');
}
