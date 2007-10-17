function SwatSearchEntry(id)
{
	this.id = id;
	this.input = document.getElementById(this.id);

	var labels = document.getElementsByTagName('label');
	var label = null;

	for (var i = 0; i < labels.length; i++) {
		if (labels[i].htmlFor == this.id) {
			label = labels[i];
			break;
		}
	}

	if (label != null) {
		this.label_text =
			(label.innerText) ? label.innerText : label.textContent;

		this.input_name = this.input.name;
		this.input_value = this.input.value;

		label.style.display = 'none';

		YAHOO.util.Event.addListener(this.input, 'focus', this.handleFocus,
			this, true);

		YAHOO.util.Event.addListener(this.input, 'blur', this.handleBlur,
			this, true);

		if (this.input.value == '')
			this.showLabelText();
		else
			this.hideLabelText();
	}
}

SwatSearchEntry.prototype.handleFocus = function(e)
{
	this.hideLabelText();
}

SwatSearchEntry.prototype.handleBlur = function(e)
{
	if (this.input.value == '')
		this.showLabelText();
}

SwatSearchEntry.prototype.showLabelText = function()
{
	YAHOO.util.Dom.addClass(this.input, 'swat-search-entry-empty');
	this.input.name = '';
	this.input_value = this.input.value;
	this.input.value = this.label_text;
}

SwatSearchEntry.prototype.hideLabelText = function()
{
	if (this.input.name == '') {
		this.input.name = this.input_name;
		this.input.value = this.input_value;
		YAHOO.util.Dom.removeClass(this.input, 'swat-search-entry-empty');
	}
}
