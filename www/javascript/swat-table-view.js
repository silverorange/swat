import SwatView from './swat-view';
import '../styles/swat-table-view.css';

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
			var odd = (
				row_node.classList.contains('odd') ||
				row_node.classList.contains('highlight-odd')
			);

			if (odd) {
				row_node.classList.remove('odd');
				row_node.classList.add('highlight-odd');
			} else {
				row_node.classList.add('highlight');
			}

			var spanning_row = row_node.nextSibling;
			while (spanning_row && spanning_row.classList.contains(
				'swat-table-view-spanning-column'
			)) {
				if (odd) {
					spanning_row.classList.remove('odd');
					spanning_row.classList.add('highlight-odd');

				} else {
					spanning_row.classList.add('highlight');
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
			var odd = (
				row_node.classList.contains('odd') ||
				row_node.classList.contains('highlight-odd')
			);

			if (odd) {
				row_node.classList.remove('highlight-odd');
				row_node.classList.add('odd');
			} else {
				row_node.classList.remove('highlight');
			}

			var spanning_row = row_node.nextSibling;
			while (spanning_row && spanning_row.classList.contains(
				'swat-table-view-spanning-column'
			)) {
				if (odd) {
					spanning_row.classList.remove('highlight-odd');
					spanning_row.classList.add('odd');
				} else {
					spanning_row.classList.remove('highlight');
				}

				spanning_row = spanning_row.nextSibling;
			}
		}
	}
}
