/**
 * Creates a new check-all object
 *
 * The check-all object is responsible for handling change events and
 * notifying its controller on state change.
 *
 * @param string id the unique identifier of this check-all object.
 */
function SwatCheckAll(id)
{
	this.id = id;
	this.check_all = document.getElementById(id);
	this.controller = null;
}

/**
 * Set the state of this check-all object
 *
 * SwatCheckboxList uses this method to update the check-all's state when all
 * checkbox list items are checked/unchecked.
 *
 * @param boolean checked the new state of this check-all object.
 */
SwatCheckAll.prototype.setState = function(checked)
{
	this.check_all.checked = checked;
}

/**
 * Sets the controlling checkbox list
 *
 * This adds an event handler to the check-all to update the list when this
 * check-all is checked/unchecked. 
 *
 * @param SwatCheckboxList controller the javascript object that represents the
 *                          checkbox list.
 */
SwatCheckAll.prototype.setController = function(controller)
{
	var self = this;
	var is_ie = (this.check_all.addEventListener) ? false : true;

	if (this.controller !== null) {
		// TODO: remove old event handlers
		// this means we have to remember the old event handler somehow.
	}

	this.controller = controller;

	controller.check_all = this;
	controller.checkAllInit();

	if (is_ie)
		this.check_all.attachEvent("onclick", eventHandler, false);
	else
		this.check_all.addEventListener("change", eventHandler, false);

	function eventHandler(event)
	{
		// check all checkboxes in the controller object
		controller.checkAll(self.check_all.checked);
	}
}
