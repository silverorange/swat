function SwatSearchEntry(id)
{
	this.id = id;
	this.button = document.getElementById(this.id);

	var labels = document.getElementsByTagName('label');
	var label = null;
	
	for (var i = 0; i < labels.length; i++) {
		if (labels[i].htmlFor == this.id) {
			label = labels[i];
			break;
		}
	}

	if (label != null) {
		this.label_text = (label.textContent) ? label.textContent : label.text;
		if (this.button.value == '') {
			YAHOO.util.Dom.addClass(this.button, 'swat-search-entry-empty');
			this.button.value = this.label_text;
		}

		label.style.display = 'none';
		YAHOO.util.Event.addListener(this.button, 'focus', this.handleFocus, this, true);
		YAHOO.util.Event.addListener(this.button, 'blur', this.handleBlur, this, true);
	}
}

SwatSearchEntry.prototype.handleFocus = function(event)
{
	if (this.button.value == this.label_text) {
		this.button.value = '';
		YAHOO.util.Dom.removeClass(this.button, 'swat-search-entry-empty');
	}
}

SwatSearchEntry.prototype.handleBlur = function(event)
{
	if (this.button.value == '') {
		YAHOO.util.Dom.addClass(this.button, 'swat-search-entry-empty');
		this.button.value = this.label_text;
	}
}
