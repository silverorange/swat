/**
 * Checkbox cell renderer controller
 *
 * @param string id the unique identifier of the checkbox column.
 * @param SwatView view the view containing this checkbox cell renderer.
 */
function SwatCheckboxCellRenderer(id, view)
{
	this.id = id;
	this.view = view;

	/*
	 * Reference to a checkall widget if it exists. This reference is set by
	 * the SwatCheckAll widget.
	 */
	this.check_all = null;
	this.check_list = [];

	/*
	 * Get all checkboxes with name = id + [] and that are contained in the
	 * currect view. Note: getElementsByName() does not work from a node
	 * element.
	 */
	var view_node = document.getElementById(this.view.id);
	var input_nodes = view_node.getElementsByTagName('input');
	for (i = 0; i < input_nodes.length; i++) {
		if (input_nodes[i].name == id + '[]') {
			this.check_list.push(input_nodes[i]);
			this.updateNode(input_nodes[i]);
			YAHOO.util.Event.addListener(input_nodes[i], 'click',
				this.handleClick, this, true);

			YAHOO.util.Event.addListener(input_nodes[i], 'dblclick',
				this.handleClick, this, true);
		}
	}
}

SwatCheckboxCellRenderer.prototype.handleClick = function(event)
{
	var checkbox_node = YAHOO.util.Event.getTarget(event);
	this.updateNode(checkbox_node);
	this.updateCheckAll();
}

SwatCheckboxCellRenderer.prototype.updateCheckAll = function()
{
	if (this.check_all == null)
		return;

	var count = 0;
	for (i = 0; i < this.check_list.length; i++)
		if (this.check_list[i].checked)
			count++;
		else if (count > 0)
			break; // can't possibly be all checked or none checked

	this.check_all.setState(count == this.check_list.length);
}

SwatCheckboxCellRenderer.prototype.checkAll = function(checked)
{
	for (i = 0; i < this.check_list.length; i++) {
		this.check_list[i].checked = checked;
		this.updateNode(this.check_list[i]);
	}
}

SwatCheckboxCellRenderer.prototype.updateNode = function(checkbox_node)
{
	if (checkbox_node.checked)
		this.view.selectItem(checkbox_node, this.id);
	else
		this.view.deselectItem(checkbox_node, this.id);
}
