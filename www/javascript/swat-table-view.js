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
		var class_names = node.className.split(' ');

		if (node.className.match(/odd/)) {
			if (highlight) {
				node.className = node.className.replace(/ *highlight-odd/, '');
				node.className = node.className.replace(/ *odd/, '');
				node.className += ' highlight-odd';
			} else {
				node.className = node.className.replace(/ *highlight-odd/, '');
				node.className = node.className.replace(/ *odd/, '');
				node.className += ' odd';
			}
		} else {
			if (highlight)
				node.className += ' highlight';
			else
				node.className = node.className.replace(/ *highlight/, '');
		}
	} else if (node.parentNode) {
		this.highlightRow(node.parentNode, highlight);
	}
}
