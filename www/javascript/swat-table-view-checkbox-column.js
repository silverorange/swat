/**
 * Javascript SwatTableViewCheckboxColumn component
 *
 * @param string id the unique identifier of the checkbox column.
 * @param SwatTableView table the javascript object that represents the
 *                             table-view.
 */
function SwatTableViewCheckboxColumn(id, table)
{
	var self = this;

	this.id = id;
	this.table = table;

	// a reference to a checkall widget (if it exists - set by the SwatCheckAll widget)
	this.check_all = null;
	this.check_list = new Array();

	/*
	 * Get all checkboxes with name = id + [] and that are contained in the
	 * correct table-view. Note: getElementsByName() does not work from a node
	 * element.
	 */
	var table_node = document.getElementById(this.table.id);
	var items = table_node.getElementsByTagName('input');
	for (i = 0; i < items.length; i++) {
		if (items[i].name == id + '[]') {
			this.check_list.push(items[i]);
			this.highlightRow(items[i]);

			if (is_ie)
				items[i].attachEvent("onclick", eventHandler);
			else 
				items[i].addEventListener("change", eventHandler, false);
		}
	}

	var is_ie = (document.addEventListener) ? false : true;

	function eventHandler(event)
	{
		var node = (is_ie) ? event.srcElement : event.target;
		self.highlightRow(node);

		self.checkAllInit();
	}
}

SwatTableViewCheckboxColumn.prototype.checkAllInit = function()
{
	if (this.check_all == null)
		return;

	var count = 0;
	for (i = 0; i < this.check_list.length; i++)
		if (this.check_list[i].checked)
			count++;
		else if (count > 0)
			break; //can't possibly be all checked or none checked

	this.check_all.setState(count == this.check_list.length);
}

SwatTableViewCheckboxColumn.prototype.checkAll = function(checked)
{
	for (i = 0; i < this.check_list.length; i++) {
		this.check_list[i].checked = checked;
		this.highlightRow(this.check_list[i]);
	}
}

SwatTableViewCheckboxColumn.prototype.highlightRow = function(node)
{
	if (this.table)
		this.table.highlightRow(node, node.checked);
}
