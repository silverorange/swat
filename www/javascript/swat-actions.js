function SwatActions(id, values, selected)
{
	var self = this;
	var is_ie = (document.addEventListener) ? false : true;

	this.id = id;
	this.flydown = document.getElementById(id + '_action_flydown');
	this.selected_element = (selected) ?
		document.getElementById(id + '_' + selected) : null;

	this.values = values;

	function handleChange(event)
	{
		if (self.selected_element) {
			self.selected_element.className = 'swat-hidden';
		}

		var id = self.id + '_' + self.values[self.flydown.selectedIndex];
		self.selected_element = document.getElementById(id);

		if (self.selected_element) {
			self.selected_element.className = '';
		}
	}

	if (is_ie)
		this.flydown.attachEvent('onchange', handleChange);
	else
		this.flydown.addEventListener('change', handleChange, true);
}
