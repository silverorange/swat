(function($) {

/**
 * Creates a new recordset view
 *
 * This is the base class used for recordset views. It is primarily
 * responsible for providing helper methods for dynamically highlighting
 * selected items in the view.
 *
 * @see SwatTableView
 * @see SwatTileView
 */
$.widget('swat.swatview', {
	version: '2.2.3',
	_create: function() {
		this._itemSelectionCounts = {};
		this._itemSelectors = {};
		this._selectorItemCounts = {};
		this._items = [];

		this._createItems();
	},

	/**
	 * Populates the items array with the items in this view.
	 */
	_createItems: $.noop,

	/**
	 * Gets a view item given an arbitrary descendant
	 *
	 * @param jQuery the the arbitrary descendant.
	 *
	 * @return jQuery the view item to which the descendant belongs.
	 */
	_getItem: function(node) {
		return node;
	},

	/**
	 * Gets an identifier for an item in this view
	 *
	 * @param jQuery item the item in this view.
	 *
	 * @return String an identifier for the given item. If the item is not an
	 *                item in this view, null is returned.
	 *
	 * @see _getItem()
	 *
	 * @todo Improve this method to not use an O(n) lookup algorithm.
	 */
	_getItemId: function(item) {
		var id = null;
		for (var i = 0; i < this._items.length; i++) {
			if (item.eq(this._items[i])) {
				id = i;
				break;
			}
		}
		return id;
	},

	/**
	 * Gets the number of items selected in this view for the specified
	 * selector
	 *
	 * @param String selector the selector identifier for which to count the
	 *                        selected items.
	 *
	 * @return Number the number of selected items for the given selector.
	 */
	_getSelectorItemCount: function(selector) {
		if (this._selectorItemCounts[selector]) {
			return this._selectorItemCounts[selector];
		}

		return 0;
	},

	selectItem: function(item, selector) {
		var that = this;
		item.each(function() {
			that._selectItem($(this), selector);
		});
	},

	deselectItem: function(item, selector) {
		var that = this;
		item.each(function() {
			that._deselectItem($(this), selector);
		});
	},

	/**
	 * Selects an item in this view
	 *
	 * An item may be selected multiple times by different selectors. This can
	 * be checked using the isSelected() method.
	 *
	 * @param jQuery item     an arbitrary descendant of the item to be
	 *                        selected.
	 * @param String selector the selector identifier.
	 */
	_selectItem: function(item, selector) {
		// get main selectable item node key
		var itemId = this._getItemId(this._getItem(item));

		// create item selection if it doesn't exist
		if (!this._itemSelectors[itemId]) {
			this._itemSelectors[itemId] = {};
			this._itemSelectors[itemId][selector] = false;
		}

		// if this item node is already not selected by the selector, increment
		// the selection count
		if (!this._itemSelectors[itemId][selector]) {
			// increment selection count for the item
			if (this._itemSelectionCounts[itemId]) {
				this._itemSelectionCounts[itemId]++;
			} else {
				this._itemSelectionCounts[itemId] = 1;
			}

			// increment item count for the selector
			if (this._selectorItemCounts[selector]) {
				this._selectorItemCounts[selector]++;
			} else {
				this._selectorItemCounts[selector] = 1;
			}
		}

		// remember that the item is selected by the selector
		this._itemSelectors[itemId][selector] = true;

		return this;
	},

	/**
	 * Deselects an item in this view
	 *
	 * An item may be selected multiple times by different selectors. This can
	 * be checked using the isSelected() method.
	 *
	 * @param jQuery item     an arbitrary descendant of the item to be
	 *                        deselected.
	 * @param String selector the selector identifier.
	 */
	_deselectItem: function(item, selector) {
		// get main selectable item node
		var itemId = this._getItemId(this._getItem(item));

		// can only deselect if the item node is selected
		if (this._itemSelectors[itemId]) {
			// check if the item node is selected by the selector
			if (this._itemSelectors[itemId][selector]) {
				// remember that the item node is not selected by the selector
				this._itemSelectors[itemId][selector] = false;

				// decrement the selection count for the item
				if (this._itemSelectionCounts[itemId]) {
					this._itemSelectionCounts[itemId] = Math.max(
						this._itemSelectionCounts[itemId] - 1,
						0
					);
				} else {
					this._itemSelectionCounts[itemId] = 0;
				}

				// decrement the item count for the selector
				if (this._selectorItemCounts[selector]) {
					this._selectorItemCounts[selector] = Math.max(
						this._selectorItemCounts[selector] - 1,
						0
					);
				} else {
					this._selectorItemCounts[selector] = 0;
				}
			}
		}

		return this;
	},

	/**
	 * Checks whether or not an item is selected given an arbitrary descendant
	 *
	 * An item is considered selected if one or more selectors have selected
	 * it.
	 *
	 * @param jQuery item an arbitrary descendant of the item to be checked for
	 *                    selection.
	 *
	 * @return Boolean true if the item is selected and false if it is not.
	 */
	isSelected: function(item) {
		var itemId = this._getItemId(this._getItem(item));
		var selected = false;

		if (typeof this._itemSelectionCounts[itemId] !== 'undefined') {
			selected = (this._itemSelectionCounts[itemId] > 0);
		}

		return selected;
	}

});

})(jQuery);
