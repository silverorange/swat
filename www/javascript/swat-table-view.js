(function($) {

$.widget('swat.swattableview', $.swat.swatview, {

	_createItems: function() {
		var items = this._items;
		var body = this.element.children('tbody');
		if (body.length) {
			// get rows from tbody
			body.children('tr').each(function() {
				items.push($(this));
			});
		} else {
			// get rows directly from table
			this.element.children('tr').each(function() {
				items.push($(this));
			});
		}
	},

	_getItem: function(item) {
		item = item.first();

		var row = null;

		while (item.length && !item.is('tr') && !item.is('body')) {
			item = item.parent();
		}
		
		if (!item.is('body')) {
			row = item;
		}

		return row;
	},

	_selectItem: function(item, selector) {
		this._super(item, selector);

		var row = this._getItem(item);

		// highlight table row of selected item in this view
		if (this.isSelected(row)) {
			var odd = (row.hasClass('odd') || row.hasClass('highlight-odd'));

			if (odd) {
				row.removeClass('odd').addClass('highlight-odd');
			} else {
				row.addClass('highlight');
			}

			// TODO
			/*var spanning_row = row_node.nextSibling;
			while (spanning_row && YAHOO.util.Dom.hasClass(
				spanning_row, 'swat-table-view-spanning-column')) {

				if (odd) {
					YAHOO.util.Dom.removeClass(spanning_row, 'odd');
					YAHOO.util.Dom.addClass(spanning_row, 'highlight-odd');
				} else {
					YAHOO.util.Dom.addClass(spanning_row, 'highlight');
				}

				spanning_row = spanning_row.nextSibling;
			}*/
		}
	},
	_deselectItem: function(item, selector) {
		this._super(item, selector);

		var row = this._getItem(item);

		// unhighlight table row of item in this view
		if (!this.isSelected(row)) {
			var odd = (row.hasClass('odd') || row.hasClass('highlight-odd'));

			if (odd) {
				row.removeClass('highlight-odd').addClass('odd');
			} else {
				row.removeClass('highlight');
			}

			// TODO
			/*var spanning_row = row_node.nextSibling;
			while (spanning_row && YAHOO.util.Dom.hasClass(
				spanning_row, 'swat-table-view-spanning-column')) {

				if (odd) {
					YAHOO.util.Dom.removeClass(spanning_row, 'highlight-odd');
					YAHOO.util.Dom.addClass(spanning_row, 'odd');
				} else {
					YAHOO.util.Dom.removeClass(spanning_row, 'highlight');
				}

				spanning_row = spanning_row.nextSibling;
			}*/
		}
	}

});

})(jQuery);

$(function() {
	$('.swat-table-view').swattableview();
});
