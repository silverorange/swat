function SwatChangeOrder(id, num_elements, first_key)
{
	this.id = id;
	this.num_elements = num_elements;

	this.list_div = document.getElementById(this.id + '_list');
	this.active_div = document.getElementById(this.id + '_option_' + first_key);
	this.first_div = document.getElementById(this.id + '_option_' + first_key);
}

SwatChangeOrder.prototype.choose = function(div)
{
	this.active_div.className = 'swat-order-control';
	div.className = 'swat-order-control-active';
	this.active_div = div;
}

SwatChangeOrder.prototype.updown = function(direction)
{
	var index = parseInt(this.active_div.id.match(/[0-9]+$/));
	
	var next = index + (direction == 'up' ? -1 : 1);
	
	// at the top or bottom - simply return
	if (next >= this.num_elements || next < 0)
		return;
	
	// swap the content of the current element and the next one
	var current_content = this.active_div.innerHTML;
	
	var next_div = document.getElementById(this.id + '_option_' + next);

	var next_content = next_div.innerHTML;
	
	next_div.innerHTML = current_content;
	this.active_div.innerHTML = next_content;
	
	// change the current element to be the next one
	this.choose(next_div);
	
	// update a hidden field with current order of keys
	var hidden_vals = document.getElementById(this.id);
	var val_array = hidden_vals.value.split(',');
	var current_val = val_array[index];
	val_array[index] = val_array[next];
	val_array[next] = current_val;
	hidden_vals.value = val_array.toString();

	// change the offset of the div to follow
	// this conditional is to fix behaviour in IE
	if (this.first_div.offsetTop > this.list_div.offsetTop)
		var y_position = (next_div.offsetTop - this.list_div.offsetTop) +
			(next_div.offsetHeight / 2);
	else
		var y_position = next_div.offsetTop + (next_div.offsetHeight / 2);

	this.scrollList(y_position);
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
