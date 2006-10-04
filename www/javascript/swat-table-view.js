/**
 * JavaScript for the SwatTableView widget 
 *
 * @param id string Id of the matching {@link SwatTableView}.
 */
function SwatTableView(id)
{
	this.id = id;
	var self = this;
}

SwatTableView.prototype.highlightRow = function(node, highlight)
{
	if (node.nodeName == 'TR') {
		if (YAHOO.util.Dom.hasClass(node, 'odd') ||
			YAHOO.util.Dom.hasClass(node, 'highlight-odd')) {
			if (highlight) {
				YAHOO.util.Dom.removeClass(node, 'odd');
				YAHOO.util.Dom.addClass(node, 'highlight-odd');
			} else {
				YAHOO.util.Dom.removeClass(node, 'highlight-odd');
				YAHOO.util.Dom.addClass(node, 'odd');
			}
		} else {
			if (highlight)
				YAHOO.util.Dom.addClass(node, 'highlight');
			else
				YAHOO.util.Dom.removeClass(node, 'highlight');
		}
	} else if (node.parentNode) {
		this.highlightRow(node.parentNode, highlight);
	}
}
