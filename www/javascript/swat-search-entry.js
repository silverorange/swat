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

		if (this.input.value == '') {
			YAHOO.util.Dom.addClass(this.input, 'swat-search-entry-empty');
			this.input.value = this.label_text;
		}

		label.style.display = 'none';

		YAHOO.util.Event.addListener(this.input, 'focus', this.handleFocus,
			this, true);

		YAHOO.util.Event.addListener(this.input, 'blur', this.handleBlur,
			this, true);
	}
}

SwatSearchEntry.prototype.handleFocus = function(e)
{
	if (this.input.value == this.label_text) {
		this.input.value = '';
		YAHOO.util.Dom.removeClass(this.input, 'swat-search-entry-empty');
	}
}

SwatSearchEntry.prototype.handleBlur = function(e)
{
	if (this.input.value == '') {
		YAHOO.util.Dom.addClass(this.input, 'swat-search-entry-empty');
		this.input.value = this.label_text;
	}
}
