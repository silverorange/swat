function SwatForm(id)
{
	this.id = id;
	this.form_element = document.getElementById(id);
}

SwatForm.prototype.setDefaultFocus = function(element_id)
{
	// TODO: check if another element in this form is already focused

	function is_function(obj)
	{
		return (typeof obj == 'function' || typeof obj == 'object');
	}

	var element = document.getElementById(element_id);
	if (element && element.disabled == false && is_function(element.focus))
		element.focus();
}
