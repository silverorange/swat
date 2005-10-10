/**
 * Javascript SwatTableViewCheckboxColumn component
 *
 * @param id string Id of the matching {@link SwatTableViewCheckboxColumn}.
 * @param table SwatTableView A reference to the javascript object that
 * 		  represents the {@link SwatTableView}
 **/
function SwatTableViewCheckboxColumn(id, table) {
	var self = this;

	this.id = id;
	this.table = table;
	this.check_all = null; //a reference to a checkall widget (if it exists - set by the SwatCheckAll widget)
	this.check_list = new Array();

	//get all checkboxes with name = id + [] and that are contained in the
	//correct table view. Note: getElementsByName doesn't work from a node
	//element.
	var table_node = document.getElementById(this.table.id);
	var items = table_node.getElementsByTagName('input');
	for (i = 0; i < items.length; i++)
		if (items[i].name == id + '[]')
			this.check_list[i] = items[i]; 


	var is_ie = (document.addEventListener) ? false : true;

	for (i = 0; i < this.check_list.length; i++) {
		this.highlightRow(this.check_list[i]);

		if (is_ie)
			this.check_list[i].attachEvent("onclick", eventHandler);
		else 
			this.check_list[i].addEventListener("change", eventHandler, false);
	}

	function eventHandler(event) {
		var node = (is_ie) ? event.srcElement : event.target;
		self.highlightRow(node);

		self.checkAllInit();
	}
}

SwatTableViewCheckboxColumn.prototype.checkAllInit = function() {
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

SwatTableViewCheckboxColumn.prototype.checkAll = function(checked) {
	for (i = 0; i < this.check_list.length; i++) {
		this.check_list[i].checked = checked;
		this.highlightRow(this.check_list[i]);
	}
}

SwatTableViewCheckboxColumn.prototype.highlightRow = function(node) {
	if (this.table)
		this.table.highlightRow(node, node.checked);
}
