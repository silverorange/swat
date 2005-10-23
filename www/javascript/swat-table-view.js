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
		var class_names = node.className.split(' ');
		if (highlight && findClass(class_names, 'odd'))
			class_names = replaceClass(class_names, 'odd', 'highlight-odd');

		else if (findClass(class_names, 'highlight-odd'))
			class_names = replaceClass(class_names, 'highlight-odd', 'odd');

		else if (highlight)
			class_names[class_names.length] = 'highlight';

		else
			class_names = replaceClass(class_names, 'highlight', '');

		node.className = classToString(class_names);

	} else if (node.parentNode)
		this.highlightRow(node.parentNode, highlight);

	function findClass(class_names, name) {
		for (var i = 0; i < class_names.length; i++)
			if (class_names[i] == name)
				return true;

		return false;
	}

	function replaceClass(class_names, from, to) {
		for (var i = 0; i < class_names.length; i++)
			if (class_names[i] == from)
				class_names[i] = to;

		return class_names;
	}

	function classToString(class_names) {
		var out = '';
		
		for (var i = 0; i < class_names.length; i++)
			out += class_names[i] + ' ';

		return out;
	}
}
