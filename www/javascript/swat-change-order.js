function SwatChangeOrder(id)
{
	this.id = id;

	this.list_div = document.getElementById(this.id + '_list');
	this.active_div = document.getElementById(this.id + '_option_0');

	// the following two lines must be split on two lines to
	// handle a Firefox bug.
	var hidden_value = document.getElementById(this.id);
	var value_array = hidden_value.value.split(',');
	var count = 0;
	var node = null;
	
	// remove text nodes and set value on nodes
	for (var i = 0; i < this.list_div.childNodes.length; i++) {
		node = this.list_div.childNodes[i];
		if (node.nodeType == 3) {
			this.list_div.removeChild(node);
			i--;
		} else if (node.nodeType == 1) {
			node.order_value = value_array[count];
			count++;
		}
	}

	this.scrollList(this.getScrollPosition());
}

SwatChangeOrder.prototype.choose = function(div)
{
	this.active_div.className = 'swat-order-control';
	div.className = 'swat-order-control-active';
	this.active_div = div;
}

SwatChangeOrder.prototype.moveToTop = function()
{
	// can't move the top of the list up
	if (this.list_div.firstChild === this.active_div)
		return false;

	var prev_div = this.list_div.firstChild;

	this.list_div.insertBefore(this.active_div, prev_div);

	this.updateValue(prev_div, this.active_div);
	this.scrollList(this.getScrollPosition());

	return true;
}

SwatChangeOrder.prototype.moveToBottom = function()
{
	// can't move the bottom of the list down
	if (this.list_div.lastChild === this.active_div)
		return false;

	var prev_div = this.list_div.lastChild;

	this.list_div.insertBefore(this.active_div, prev_div);
	this.list_div.insertBefore(prev_div, this.active_div);

	this.updateValue(prev_div, this.active_div);
	this.scrollList(this.getScrollPosition());

	return true;
}

SwatChangeOrder.prototype.moveUp = function()
{
	// can't move the top of the list up
	if (this.list_div.firstChild === this.active_div)
		return false;

	var prev_div = this.active_div.previousSibling;

	this.list_div.insertBefore(this.active_div, prev_div);

	this.updateValue();
	this.scrollList(this.getScrollPosition());

	return true;
}

SwatChangeOrder.prototype.moveDown = function()
{
	// can't move the bottom of the list down
	if (this.list_div.lastChild === this.active_div)
		return false;

	var next_div = this.active_div.nextSibling;

	this.list_div.insertBefore(next_div, this.active_div);

	this.updateValue();
	this.scrollList(this.getScrollPosition());

	return true;
}

SwatChangeOrder.prototype.updateValue = function()
{
	var temp = '';

	for (i = 0; i < this.list_div.childNodes.length; i++) {
		if (i > 0)
			temp = temp + ',';

		temp = temp + this.list_div.childNodes[i].order_value;
	}

	// update a hidden field with current order of keys
	document.getElementById(this.id).value = temp;
}

SwatChangeOrder.prototype.getScrollPosition = function()
{
	// this conditional is to fix behaviour in IE
	if (this.list_div.firstChild.offsetTop > this.list_div.offsetTop)
		var y_position = (this.active_div.offsetTop - this.list_div.offsetTop) +
			(this.active_div.offsetHeight / 2);
	else
		var y_position = this.active_div.offsetTop +
			(this.active_div.offsetHeight / 2);
	
	return y_position;
}

SwatChangeOrder.prototype.scrollList = function(y_coord)
{
	// clientHeight is the height of the visible scroll area
	var half_list_height = parseInt(this.list_div.clientHeight / 2);

	if (y_coord < half_list_height) {
		this.list_div.scrollTop = 0;
		return;
	}

	// scrollHeight is the height of the contents inside the scroll area
	if (this.list_div.scrollHeight - y_coord < half_list_height) {
		this.list_div.scrollTop = this.list_div.scrollHeight -
			this.list_div.clientHeight;

		return;
	}

	// offsetHeight is clientHeight + padding
	factor = (y_coord - half_list_height) /
		(this.list_div.scrollHeight - this.list_div.offsetHeight);
		
	this.list_div.scrollTop = Math.floor(
		(this.list_div.scrollHeight - this.list_div.clientHeight) * factor);
}
