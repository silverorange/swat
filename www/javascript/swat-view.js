/**
 * Creates a new recordset view
 *
 * This is the base class used for recordset views. It is primarily
 * responsible for providing helper methods for dynamically highlighting
 * selecgted items in the view.
 *
 * @param String id the identifier of this view.
 *
 * @see SwatTableView
 * @see SwatTileView
 */
function SwatView(id)
{
	this.id = id;
	this.item_selection_counts = [];
	this.item_selectors = [];
	this.selector_item_counts = [];
	this.items = [];
}

/**
 * Gets an item node given an arbitrary descendant node
 *
 * @param DOMElement node the arbitrary descendant node.
 *
 * @return DOMElement the item node.
 */
SwatView.prototype.getItemNode = function(node)
{
	return node;
};

/**
 * Gets an identifier for an item in this view
 *
 * @param DOMElement item_node an item node in this view
 *
 * @return String an identifier for the given item node.
 *
 * @see SwatView::getItemNode()
 * @todo I'd like to improve this method to not use an O(n) lookup algorithm.
 */
SwatView.prototype.getItemNodeKey = function(item_node)
{
	var key = null;
	for (var i = 0; i < this.items.length; i++) {
		if (item_node === this.items[i]) {
			key = i;
			break;
		}
	}
	return key;
};

/**
 * Gets the number of items selected in this view for the specified selector
 *
 * @param String the selector identifier to coult the selected items for.
 *
 * @return Number the number of selected items for the given selector.
 */
SwatView.prototype.getSelectorItemCount = function(selector)
{
	if (this.selector_item_counts[selector])
		return this.selector_item_counts[selector];

	return 0;
};

/**
 * Selects an item node in this view
 *
 * An item may be selected multiple times by different selectors. This can
 * be checked using the SwatView::isSelected() method.
 *
 * @param DOMElement node an arbitrary descendant node of the item node to be
 *                         selected.
 * @param String selector an identifier of the object that selected the item
 *                         node.
 */
SwatView.prototype.selectItem = function(node, selector)
{
	// get main selectable item node key
	var key = this.getItemNodeKey(this.getItemNode(node));

	if (!this.item_selectors[key])
		this.item_selectors[key] = [];

	// if this item node is already not selected by the selector, increment
	// the selection count
	if (!this.item_selectors[key][selector]) {
		// increment selection count for the item
		if (this.item_selection_counts[key]) {
			this.item_selection_counts[key]++;
		} else {
			this.item_selection_counts[key] = 1;
		}

		// increment item count for the selector
		if (this.selector_item_counts[selector]) {
			this.selector_item_counts[selector]++;
		} else {
			this.selector_item_counts[selector] = 1;
		}
	}

	// remember that this node is selected by the selector
	this.item_selectors[key][selector] = true;
};

/**
 * Deselects an item node in this view
 *
 * An item may be selected multiple times by different selectors. This can
 * be checked using the SwatView::isSelected() method.
 *
 * @param DOMElement node an arbitrary descendant node of the item node to be
 *                         deselected.
 * @param String selector an identifier of the object that deselected the item
 *                         node.
 */
SwatView.prototype.deselectItem = function(node, selector)
{
	// get main selectable item node
	var key = this.getItemNodeKey(this.getItemNode(node));

	// can only deselect if the item node is selected
	if (this.item_selectors[key]) {
		// check if the item node is selected by the selector
		if (this.item_selectors[key][selector]) {
			// remember that the item node is not selected by the selector
			this.item_selectors[key][selector] = false;

			// decrement the selection count for the item
			if (this.item_selection_counts[key]) {
				this.item_selection_counts[key] =
					Math.max(this.item_selection_counts[key] - 1, 0);
			} else {
				this.item_selection_counts[key] = 0;
			}

			// decrement the item count for the selector
			if (this.selector_item_counts[selector]) {
				this.selector_item_counts[selector] =
					Math.max(this.selector_item_counts[selector] - 1, 0);
			} else {
				this.selector_item_counts[selector] = 0;
			}
		}
	}
};

/**
 * Checks whether or not an item node is selected given an arbitrary
 * descendant node
 *
 * An item is considered selected if one or more selectors have selected
 * it.
 *
 * @param DOMElement node an arbitrary descendant node of the item node to
 *                         be checked for selection.
 *
 * @return Boolean true if the item node is selected and false if it is not.
 */
SwatView.prototype.isSelected = function(node)
{
	var key = this.getItemNodeKey(this.getItemNode(node));
	if (typeof(this.item_selection_counts[key]) == 'undefined')
		var selected = false;
	else
		var selected = (this.item_selection_counts[key] > 0);

	return selected;
};
