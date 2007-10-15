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

		label.style.display = 'none';

		YAHOO.util.Event.addListener(this.input, 'focus', this.handleFocus,
			this, true);

		YAHOO.util.Event.addListener(this.input, 'blur', this.handleBlur,
			this, true);

		var text = document.createTextNode(this.label_text);
		this.span = document.createElement('span');
		YAHOO.util.Dom.addClass(this.span, 'swat-search-entry-empty');
		this.span.appendChild(text);
		this.span.style.position = 'absolute';

		if (this.input.value == '')
			this.span.style.display = 'block';
		else
			this.span.style.display = 'none';

		YAHOO.util.Event.addListener(this.span, 'mousedown',
			this.handleMouseDown, this, true);

		this.input.parentNode.insertBefore(this.span, this.input);
	}
}

SwatSearchEntry.prototype.handleMouseDown = function(e)
{
	this.input.focus();
}

SwatSearchEntry.prototype.handleFocus = function(e)
{
	this.span.style.display = 'none';
}

SwatSearchEntry.prototype.handleBlur = function(e)
{
	if (this.input.value == '') {
		this.span.style.display = 'block';
	}
}
