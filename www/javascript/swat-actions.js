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

SwatActions.handleChange = function(event, actions_object)
{
	if (actions_object.selected_element)
		YAHOO.util.Dom.addClass(actions_object.selected_element,
			'swat-hidden');

	var id = actions_object.id + '_' +
		actions_object.values[actions_object.flydown.selectedIndex];

	actions_object.selected_element = document.getElementById(id);

	if (actions_object.selected_element)
		YAHOO.util.Dom.removeClass(actions_object.selected_element,
			'swat-hidden');
}
