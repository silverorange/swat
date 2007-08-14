function SwatCascade(parent, child)
{
	this.parent = document.getElementById(parent);
	this.child = document.getElementById(child);
	this.children = [];
	this.blank_option = this.child[0];

	YAHOO.util.Event.addListener(this.parent, 'change',
		SwatCascade.parentChangeHandler, this);
}

SwatCascade.parentChangeHandler = function(event, object)
{
	object.update();
}

SwatCascade.prototype.update = function()
{
	var display = this.children[this.parent.value];

	// reset the options
	for (var i = (this.child.options.length - 1); i > -1;  i--)
		this.child.removeChild(this.child.options[i])

	if (display) {
		this.child.disabled = false;

		for (i = 0; i < display.length; i++)
			this.child.appendChild(
				new Option(display[i].title, display[i].value, display[i].selected));

	} else {
		this.child.options[0] = this.blank_option;
		this.child.disabled = true;
	}
}

SwatCascade.prototype.addChild = function(parent, value, title, selected)
{
	if (!this.children[parent])
		this.children[parent] = [];

	this.children[parent].push(new SwatCascadeChild(value, title, selected));
}

SwatCascade.prototype.init = function()
{
	this.update();
}

function SwatCascadeChild(value, title, selected)
{
	this.value = value;
	this.title = title;
	this.selected = selected;
}
