function SwatCascade(from_flydown_id, to_flydown_id)
{
	this.from_flydown = document.getElementById(from_flydown_id);
	this.to_flydown = document.getElementById(to_flydown_id);
	this.children = [];

	YAHOO.util.Event.addListener(this.from_flydown, 'change',
		this.handleChange, this, true);
}

SwatCascade.prototype.handleChange = function(e)
{
	this.update(false);
}

SwatCascade.prototype.update = function(selected)
{
	var child_options = this.children[this.from_flydown.value];

	// clear old options
	this.to_flydown.options.length = 0;

	if (child_options) {
		this.to_flydown.disabled = false;

		for (var i = 0; i < child_options.length; i++) {
			// only select default option if we are intializing
			if (selected)
				this.to_flydown.options[this.to_flydown.options.length] =
					new Option(child_options[i].title,
						child_options[i].value, child_options[i].selected);
			else
				this.to_flydown.options[this.to_flydown.options.length] =
					new Option(child_options[i].title,
						child_options[i].value);
		}

	} else {
		// the following string contains UTF-8 encoded non breaking spaces
		this.to_flydown.options[0] = new Option('      ', 0);
		this.to_flydown.disabled = true;
	}
}

SwatCascade.prototype.addChild = function(from_flydown_value, value, title,
	selected)
{
	if (!this.children[from_flydown_value])
		this.children[from_flydown_value] = [];

	this.children[from_flydown_value].push(
		new SwatCascadeChild(value, title, selected));
}

SwatCascade.prototype.init = function()
{
	this.update(true);
}

function SwatCascadeChild(value, title, selected)
{
	this.value = value;
	this.title = title;
	this.selected = selected;
}
