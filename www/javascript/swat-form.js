function SwatForm(id)
{
	this.id = id;
	this.form_element = document.getElementById(id);
}

SwatForm.prototype.setDefaultFocus = function(element_id)
{
	// TODO: check if another element in this form is already focused

	var element = document.getElementById(element_id);
	
	if (element)
		element.focus();
}
