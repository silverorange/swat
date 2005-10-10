/**
 * Javascript SwatTableView component
 *
 * @param id string Id of the matching {@link SwatTableView}.
 **/
function SwatTableView(id) {
	this.id = id;
	var self = this;
}

SwatTableView.prototype.highlightRow = function(node, highlight) {
	if (node.nodeName == 'TR') {
		if (node.className == 'odd' || node.className == 'highlight-odd')
			node.className = (highlight) ? 'highlight-odd' : 'odd';
		else
			node.className = (highlight) ? 'highlight' : '';

	} else if (node.parentNode)
		this.highlightRow(node.parentNode, highlight);
}
