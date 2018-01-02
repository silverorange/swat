import { Dom } from '../../../yui/www/dom/dom';
import { ColorAnim, Easing } from '../../../yui/www/animation/animation';

/**
 * Gets an XML parser with a loadXML() method
 */
function SwatTableViewInputRow_getXMLParser() {
	var parser = null;
	var is_ie = true;

	try {
		var dom = new ActiveXObject('Msxml2.XMLDOM');
	} catch (err1) {
		try {
			var dom = new ActiveXObject('Microsoft.XMLDOM');
		} catch (err2) {
			is_ie = false;
		}
	}

	if (is_ie) {
		/*
		 * Internet Explorer's XMLDOM object has a proprietary loadXML()
		 * method. Our method returns the document.
		 */
		parser = function() {};
		parser.loadXML = function(document_string) {
			if (!dom.loadXML(document_string))
				alert(dom.parseError.reason);

			return dom;
		};
	}

	if (parser === null && typeof DOMParser !== 'undefined') {
		/*
		 * Mozilla, Safari and Opera have a proprietary DOMParser()
		 * class.
		 */
		var dom_parser = new DOMParser();

		// Cannot add loadXML method to a newly created DOMParser because it
		// crashes Safari
		parser = function() {};
		parser.loadXML = function(document_string) {
			return dom_parser.parseFromString(document_string, 'text/xml');
		};
	}

	return parser;
};

/*
 * If the browser does not support the importNode() method then we need to
 * manually copy nodes from one document to the current document. These methods
 * parse a HTMLTableRowNode in one document into a table row in this document.
 */

/**
 * Parses a table row from one document into another
 *
 * Internet Explorer does not allow writing to the innerHTML property for
 * table row nodes so the table cells are individually inserted and cloned.
 *
 * @param HTMLTableRowNode source_tr the table row from the source document.
 * @param HTMLTableRowNode dest_tr the table row in the destination
 *                                  document.
 */
function SwatTableViewInputRow_parseTableRow(source_tr, dest_tr) {
	var child_node;
	var dest_td;
	var source_attributes;
	for (var i = 0; i < source_tr.childNodes.length; i++) {
		child_node = source_tr.childNodes[i];
		if (child_node.nodeType == 1 && child_node.nodeName == 'td') {
			dest_td = dest_tr.insertCell(-1);
			source_attributes = child_node.attributes;
			for (var j = 0; j < source_attributes.length; j++) {
				if (source_attributes[j].name == 'class') {
					dest_td.className = source_attributes[j].value;
				} else {
					dest_td.setAttribute(source_attributes[j].name,
						source_attributes[j].value);
				}
			}
			SwatTableViewInputRow_parseTableCell(child_node, dest_td);
		}
	}
}

/**
 * Parses a table cell from one document into another
 *
 * @param HTMLTableRowNode source_td the table cell from the source
 *                                    document.
 * @param HTMLTableRowNode dest_td the table cell in the destination
 *                                  document.
 */
function SwatTableViewInputRow_parseTableCell(source_td, dest_td) {
	// Internet Explorer does not have an innerHTML property set on
	// imported DOM nodes even if the imported document is XHTML.
	if (source_td.innerHTML)
		dest_td.innerHTML = source_td.innerHTML;
	else
		dest_td.innerHTML = source_td.xml;
}

/**
 * A table view row that can add an arbitrary number of data entry rows
 *
 * @package   Swat
 * @copyright 2004-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTableViewInputRow {
	/**
	 * A table view row that can add an arbitrary number of data entry rows
	 *
	 * @param String id the identifier of this row.
	 * @param String row_string the XML string to use when inserting a new row. The
	 *                           XML string can contain '%s' placeholders. Before
	 *                           a new row is added, the placeholders are replaced
	 *                           with the new row index.
	 */
	constructor(id, row_string) {
		this.id = id;

		// decode string
		row_string = row_string.replace(/&lt;/g, '<').replace(/&gt;/g, '>');
		row_string = row_string.replace(/&quot;/g, '"').replace(/&amp;/g, '&');

		/*
		 * Pack row string in an XHTML document
		 *
		 * We purposly do not specify a DTD here as Internet Explorer is too slow
		 * when given a DTD. The XML string is encoded in UTF-8 with no special
		 * entities at this point.
		 */
		this.row_string = "<?xml version='1.0' encoding='UTF-8'?>\n" +
			'<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" ' +
			'lang="en">' +
			'<head><title>row</title></head><body><table>' +
			row_string +
			'</table></body></html>';

		this.enter_row = document.getElementById(this.id + '_enter_row');

		// get table node belonging to the enter row
		this.table = this.enter_row;
		while (this.table.nodeName.toLowerCase() !== 'table') {
			this.table = this.table.parentNode;
		}

		this.replicators = null;
		this.replicators_input = null;
	}

	/**
	 * Initializes the replicator array
	 *
	 * @return boolean true if the replicators were successfully initialized and
	 *          false if they were not.
	 */
	initReplicators() {
		if (this.replicators === null) {
			this.replicators_input = document.getElementsByName(
				this.id + '_replicators'
			)[0];

			if (this.replicators_input === null) {
				return false;
			}

			if (this.replicators_input.value === '') {
				this.replicators = [];
			} else {
				this.replicators = this.replicators_input.value.split(',');
			}
		}
		return true;
	}

	/**
	 * Adds a new data row to the table
	 */
	addRow() {
		if (!this.initReplicators()) {
			return;
		}

		var replicator_id;
		if (this.replicators.length > 0) {
			replicator_id = (parseInt(
				this.replicators[this.replicators.length - 1],
				10
			) + 1).toString();
		} else {
			replicator_id = '0';
		}

		this.replicators.push(replicator_id);
		this.replicators_input.value = this.replicators.join(',');

		var document_string = this.row_string.replace(/%s/g, replicator_id);
		var dom = SwatTableViewInputRow.parser.loadXML(document_string);
		var source_tr = dom.documentElement.getElementsByTagName('tr')[0];

		if (document.importNode && !SwatTableViewInputRow.is_webkit) {
			try {
				var dest_tr = document.importNode(source_tr, true);
				this.enter_row.parentNode.insertBefore(dest_tr, this.enter_row);
			} catch (ex) {
				/*
				 * IE9 specific code. IE9 claims to support importNode, but fails
				 * when it is called.
				 */
				var dest_tr = this.table.insertRow(this.enter_row.rowIndex);
				SwatTableViewInputRow_parseTableRow(source_tr, dest_tr);
			}
		} else {
			/*
			 * Internet Explorer and Safari specific code
			 *
			 * Uses the table object model instead of the DOM. IE does not
			 * implement importNode() and Safari's importNode() implementation is
			 * broken.
			 */
			var dest_tr = this.table.insertRow(this.enter_row.rowIndex);
			SwatTableViewInputRow_parseTableRow(source_tr, dest_tr);
		}

		dest_tr.className = 'swat-table-view-input-row';
		dest_tr.id = this.id + '_row_' + replicator_id;

		var node = dest_tr;
		var dest_color = 'transparent';
		while (dest_color == 'transparent' && node) {
			dest_color = Dom.getStyle(node, 'background-color');
			node = node.parentNode;
		}
		if (dest_color == 'transparent') {
			dest_color = '#ffffff';
		}

		var animation = new ColorAnim(
			dest_tr,
			{ backgroundColor: { from: '#fffbc9', to: dest_color } },
			1,
			Easing.easeOut
		);

		animation.animate();

		/*
		 * Run scripts
		 *
		 * A better way to do this might be to remove the script nodes from the
		 * document and then run the scripts. This way we can ensure the scripts
		 * are only run once.
		 */
		var scripts = dom.documentElement.getElementsByTagName('script');
		for (var i = 0; i < scripts.length; i++) {
			if (scripts[0].getAttribute('type') === 'text/javascript' &&
				scripts[0].childNodes.length > 0
			) {
				eval(scripts[i].firstChild.nodeValue);
			}
		}
	}

	/**
	 * Removes a data row from the table
	 */
	removeRow(replicator_id) {
		if (!this.initReplicators()) {
			return;
		}

		// remove replicator_id from replicators array
		var replicator_index = -1;
		for (var i = 0; i < this.replicators.length; i++) {
			if (this.replicators[i] == replicator_id) {
				replicator_index = i;
				break;
			}
		}
		if (replicator_index != -1) {
			this.replicators.splice(replicator_index, 1);
			this.replicators_input.value = this.replicators.join(',');
		}

		// remove row from document
		var row_id = this.id + '_row_' + replicator_id;
		var row = document.getElementById(row_id);
		if (row && row.parentNode !== null) {
			row.parentNode.removeChild(row);
		}
	}
}

/**
 * Check if the browser is Safari
 *
 * Safari's DOM importNode() method is broken so we use the IE table object
 * model hack for it as well.
 *
 * @var boolean
 */
SwatTableViewInputRow.is_webkit =
	(/AppleWebKit|Konqueror|KHTML/gi).test(navigator.userAgent);

/**
 * The XML parser used by this widget
 *
 * @var Object
 */
SwatTableViewInputRow.parser = SwatTableViewInputRow_getXMLParser();

export default SwatTableViewInputRow;
