function SwatActions(id, values, selected)
{
	var self = this;

	this.id = id;
	this.flydown = document.getElementById(id + '_action_flydown');
	this.selected_element = (selected) ?
		document.getElementById(id + '_' + selected) : null;

	this.values = values;

	function handleChange(event)
	{
		if (self.selected_element)
			YAHOO.util.Dom.addClass(self.selected_element, 'swat-hidden');

		var id = self.id + '_' + self.values[self.flydown.selectedIndex];
		self.selected_element = document.getElementById(id);

		if (self.selected_element)
			YAHOO.util.Dom.removeClass(self.selected_element, 'swat-hidden');
	}

	YAHOO.util.Event.addListener(this.flydown, 'change', handleChange);
}
