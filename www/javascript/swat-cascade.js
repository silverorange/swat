function SwatCascade(parent, child)
{
	this.parent = document.getElementById(parent);
	this.child = document.getElementById(child);
	this.init();
}

SwatCascade.prototype.update = function()
{
	var display = this.children[this.parent.value];

	//reset the options
	for (var i = 0; i < this.child.options.length; i++)
		this.child.options[i] = null;
	
	if (display) {
		this.child.disabled = false;

		for (i = 0; i < display.length; i++)
			this.child.options[i] =
				new Option(display[i].title, display[i].value);
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
	this.children = [];
	this.blank_option = this.child[0];
	
	var child_id = this.child.id;

	if (!this.parent.value) 
		this.child.disabled = true;

	// TODO: use DOM event handling
	this.parent.onchange = function() {
		obj = eval(child_id + '_cascade');
		obj.update();
	}
}

function SwatCascadeChild(value, title, selected)
{
	this.value = value;
	this.title = title;
	this.selected = selected;
}
