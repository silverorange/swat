/**
 * JavaScript for the SwatTileView widget 
 *
 * @param id string Id of the matching {@link SwatTileView}.
 */
function SwatTileView(id)
{
	this.id = id;
}

SwatTileView.prototype.highlightRow = function(node, highlight)
{
	if (YAHOO.util.Dom.hasClass(node, 'swat-tile')) {
		if (highlight)
			YAHOO.util.Dom.addClass(node, 'highlight');
		else
			YAHOO.util.Dom.removeClass(node, 'highlight');

	} else if (node.parentNode) {
		this.highlightRow(node.parentNode, highlight);
	}
}
