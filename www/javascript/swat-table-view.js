import SwatView from './swat-view';

export default class SwatTableView extends SwatView {
	/**
	 * JavaScript for the SwatTableView widget
	 *
	 * @param id string Id of the matching {@link SwatTableView}.
	 */
	constructor(id) {
		super(id);

		this.table_node = document.getElementById(this.id);

		// look for tbody node
		var tbody_node = null;
		for (var i = 0; i < this.table_node.childNodes.length; i++) {
			if (this.table_node.childNodes[i].nodeName == 'TBODY') {
				tbody_node = this.table_node.childNodes[i];
				break;
			}
		}

		// no tbody node, so item rows are directly in table node
		if (tbody_node === null) {
			tbody_node = this.table_node;
		}

		for (var i = 0; i < tbody_node.childNodes.length; i++) {
			if (tbody_node.childNodes[i].nodeName === 'TR') {
				this.items.push(tbody_node.childNodes[i]);
			}
		}
	}

	/**
	 * Gets an item node in a table-view
	 *
	 * The item node is the closest parent table row element.
	 *
	 * @param DOMElement node the arbitrary descendant node.
	 *
	 * @return DOMElement the item node.
	 */
	getItemNode(node) {
		var row_node = node;

		// search for containing table row element
		while (row_node.nodeName !== 'TR' && row_node.nodeName !== 'BODY') {
			row_node = row_node.parentNode;
		}

		// we reached the body element without finding the row node
		if (row_node.nodeName === 'BODY') {
			row_node = node;
		}

		return row_node;
	}

	/**
	 * Selects an item node in this table-view
	 *
	 * For table-views, this method also highlights selected item rows.
	 *
	 * @param DOMElement node an arbitrary descendant node of the item node to
	 *                        be selected.
	 * @param String selector an identifier of the object that selected the
	 *                        item node.
	 */
	selectItem(node, selector) {
		super.selectItem(node, selector);

		var row_node = this.getItemNode(node);

		// highlight table row of selected item in this view
		if (this.isSelected(row_node)) {
			var odd = (YAHOO.util.Dom.hasClass(row_node, 'odd') ||
				YAHOO.util.Dom.hasClass(row_node, 'highlight-odd'));

			if (odd) {
				YAHOO.util.Dom.removeClass(row_node, 'odd');
				YAHOO.util.Dom.addClass(row_node, 'highlight-odd');
			} else {
				YAHOO.util.Dom.addClass(row_node, 'highlight');
			}

			var spanning_row = row_node.nextSibling;
			while (spanning_row && YAHOO.util.Dom.hasClass(
				spanning_row, 'swat-table-view-spanning-column')
			) {
				if (odd) {
					YAHOO.util.Dom.removeClass(spanning_row, 'odd');
					YAHOO.util.Dom.addClass(spanning_row, 'highlight-odd');
				} else {
					YAHOO.util.Dom.addClass(spanning_row, 'highlight');
				}

				spanning_row = spanning_row.nextSibling;
			}
		}
	}

	/**
	 * Deselects an item node in this table-view
	 *
	 * For table-views, this method also unhighlights deselected item rows.
	 *
	 * @param DOMElement node an arbitrary descendant node of the item node to be
	 *                         deselected.
	 * @param String selector an identifier of the object that deselected the item
	 *                         node.
	 */
	deselectItem(node, selector) {
		super.deselectItem(node, selector);

		var row_node = this.getItemNode(node);

		// unhighlight table row of item in this view
		if (!this.isSelected(row_node)) {
			var odd = (YAHOO.util.Dom.hasClass(row_node, 'odd') ||
				YAHOO.util.Dom.hasClass(row_node, 'highlight-odd'));

			if (odd) {
				YAHOO.util.Dom.removeClass(row_node, 'highlight-odd');
				YAHOO.util.Dom.addClass(row_node, 'odd');
			} else {
				YAHOO.util.Dom.removeClass(row_node, 'highlight');
			}

			var spanning_row = row_node.nextSibling;
			while (spanning_row && YAHOO.util.Dom.hasClass(
				spanning_row, 'swat-table-view-spanning-column')
			) {
				if (odd) {
					YAHOO.util.Dom.removeClass(spanning_row, 'highlight-odd');
					YAHOO.util.Dom.addClass(spanning_row, 'odd');
				} else {
					YAHOO.util.Dom.removeClass(spanning_row, 'highlight');
				}

				spanning_row = spanning_row.nextSibling;
			}
		}
	}
}
