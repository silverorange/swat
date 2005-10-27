/**
 * An orderable list control widget
 *
 * Part of Swat
 *
 * @param string id the unique identifier of this object.
 *
 * @copyright 2004-2005 silverorange Inc.
 */
function SwatChangeOrder(id)
{
	this.id = id;

	this.list_div = document.getElementById(this.id + '_list');

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
			node.order_index = count;
			count++;
		}
	}

	this.active_div = this.list_div.firstChild;

	this.scrollList(this.getScrollPosition());

	// while not a real semaphore, this does prevent the user from breaking
	// things by clicking buttons or items while an animation is occuring.
	this.semaphore = true;
}

/**
 * Delay in milliseconds to use for animations
 *
 * @var number
 */
SwatChangeOrder.animation_delay = 10;

/**
 * A static callback function for the move-to-top window timeout.
 *
 * @param SwatChangeOrder change_order the change-order widget to work with.
 * @param number steps the number of steps to skip when moving the active
 *                      element.
 */
function SwatChangeOrder_staticMoveToTop(change_order, steps)
{
	change_order.moveToTopHelper(steps);
}

/**
 * A static callback function for the move-to-bottom window timeout.
 *
 * @param SwatChangeOrder change_order the change-order widget to work with.
 * @param number steps the number of steps to skip when moving the active
 *                      element.
 */
function SwatChangeOrder_staticMoveToBottom(change_order, steps)
{
	change_order.moveToBottomHelper(steps);
}

/**
 * Choses an element in this change order as the active div
 *
 * Only allows chosing if the semaphore is not set.
 *
 * @param DOMNode div the element to chose.
 */
SwatChangeOrder.prototype.choose = function(div)
{
	if (this.semaphore) {
		this.active_div.className = 'swat-order-control';
		div.className = 'swat-order-control-active';
		this.active_div = div;

		// update the index value of this element
		for (var i = 0; i < this.list_div.childNodes.length; i++) {
			if (this.list_div.childNodes[i] === this.active_div) {
				this.active_div.order_index = i;
				break;
			}
		}
	}
}

/**
 * Moves the active element to the top of the list
 *
 * Only functions if the semaphore is not set. Sets the semaphore.
 */
SwatChangeOrder.prototype.moveToTop = function()
{
	if (this.semaphore) {
		this.semaphore = false;

		var steps = Math.ceil(this.active_div.order_index / 5);

		this.moveToTopHelper(steps);
	}
}

/**
 * A helper method that moves the active element up and sets a timeout callback
 * to move it up again until it reaches the top
 *
 * Unsets the semaphore after the active element is at the top.
 *
 * @param number steps the number of steps to skip when moving the active
 *                      element.
 */
SwatChangeOrder.prototype.moveToTopHelper = function(steps)
{
	if (this.moveUpHelper(steps)) {
		window.setTimeout('SwatChangeOrder_staticMoveToTop(' +
			this.id + '_obj, ' + steps + ');',
			SwatChangeOrder.animation_delay);
	} else {
		this.semaphore = true;
	}
}

/**
 * Moves the active element to the bottom of the list
 *
 * Only functions if the semaphore is not set. Sets the semaphore.
 */
SwatChangeOrder.prototype.moveToBottom = function()
{
	if (this.semaphore) {
		this.semaphore = false;

		var steps = Math.ceil((this.list_div.childNodes.length - this.active_div.order_index - 1) / 5);

		this.moveToBottomHelper(steps);
	}
}

/**
 * A helper method that moves the active element down and sets a timeout
 * callback to move it down again until it reaches the bottom
 *
 * Unsets the semaphore after the active element is at the bottom.
 *
 * @param number steps the number of steps to skip when moving the active
 *                      element.
 */
SwatChangeOrder.prototype.moveToBottomHelper = function(steps)
{
	if (this.moveDownHelper(steps)) {
		window.setTimeout('SwatChangeOrder_staticMoveToBottom(' +
			this.id + '_obj, ' + steps + ');',
			SwatChangeOrder.animation_delay);
	} else {
		this.semaphore = true;
	}
}

/**
 * Moves the active element up one space
 *
 * Only functions if the semaphore is not set.
 */
SwatChangeOrder.prototype.moveUp = function()
{
	if (this.semaphore)
		this.moveUpHelper(1);
}

/**
 * Moves the active element down one space
 *
 * Only functions if the semaphore is not set.
 */
SwatChangeOrder.prototype.moveDown = function()
{
	if (this.semaphore)
		this.moveDownHelper(1);
}

/**
 * Moves the active element up a number of steps
 *
 * @param number steps the number of steps to move the active element up by.
 *
 * @return boolean true if the element is not hitting the top of the list,
 *                  false otherwise.
 */
SwatChangeOrder.prototype.moveUpHelper = function(steps)
{
	// can't move the top of the list up
	if (this.list_div.firstChild === this.active_div)
		return false;

	var return_val = true;

	var prev_div = this.active_div;
	for (var i = 0; i < steps; i++) {
		prev_div = prev_div.previousSibling;
		if (prev_div === this.list_div.firstChild) {
			return_val = false;
			break;
		}
	}

	this.list_div.insertBefore(this.active_div, prev_div);

	this.active_div.order_index =
		Math.max(this.active_div.order_index - steps, 0);

	this.updateValue();
	this.scrollList(this.getScrollPosition());

	return return_val;
}

/**
 * Moves the active element down a number of steps
 *
 * @param number steps the number of steps to move the active element down by.
 *
 * @return boolean true if the element is not hitting the bottom of the list,
 *                  false otherwise.
 */
SwatChangeOrder.prototype.moveDownHelper = function(steps)
{
	// can't move the bottom of the list down
	if (this.list_div.lastChild === this.active_div)
		return false;

	var return_val = true;

	var next_div = this.active_div;
	for (var i = 0; i < steps + 1; i++) {
		next_div = next_div.nextSibling;
		if (next_div === this.list_div.lastChild)
			break;
	}

	this.list_div.insertBefore(this.active_div, next_div);

	// really at the bottom
	if (i < steps) {
		return_val = false;
		this.list_div.insertBefore(next_div, this.active_div);
	}

	this.active_div.order_index =
		Math.min(this.active_div.order_index + steps,
			this.list_div.childNodes.length - 1);

	this.updateValue();
	this.scrollList(this.getScrollPosition());

	return return_val;
}

/**
 * Updates the value of the hidden field containing the ordering of elements
 */
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

/**
 * Gets the y-position of the active element in the scrolling section
 */
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

/**
 * Scrolls the list to a y-position
 *
 * This method acts the same as window.scrollTo() but it acts on a div instead
 * of the window.
 *
 * @param number y_coord the y value to scroll the list to in pixels.
 */
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
