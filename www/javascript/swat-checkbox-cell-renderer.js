export default class SwatCheckboxCellRenderer {
	/**
	 * Checkbox cell renderer controller
	 *
	 * @param string id the unique identifier of the checkbox column.
	 * @param SwatView view the view containing this checkbox cell renderer.
	 */
	constructor(id, view) {
		this.id = id;
		this.view = view;

		/*
		 * Reference to a checkall widget if it exists. This reference is set by
		 * the SwatCheckAll widget.
		 */
		this.check_all = null;
		this.last_clicked_index = null;

		this.init();
	}

	init() {
		this.check_list = [];

		/*
		 * Get all checkboxes with name = id + [] and that are contained in the
		 * currect view. Note: getElementsByName() does not work from a node
		 * element.
		 */
		var view_node = document.getElementById(this.view.id);
		var input_nodes = view_node.getElementsByTagName('input');
		for (var i = 0; i < input_nodes.length; i++) {
			if (input_nodes[i].name == this.id + '[]') {
				input_nodes[i]._index = this.check_list.length;
				this.check_list.push(input_nodes[i]);
				this.updateNode(input_nodes[i]);
				YAHOO.util.Event.addListener(input_nodes[i], 'click',
					this.handleClick, this, true);

				YAHOO.util.Event.addListener(input_nodes[i], 'dblclick',
					this.handleClick, this, true);

				// prevent selecting label text when shify key is held
				YAHOO.util.Event.addListener(
					input_nodes[i].parentNode,
					'mousedown',
					this.handleMouseDown,
					this,
					true
				);
			}
		}
	}

	handleMouseDown(e) {
		// prevent selecting label text when shify key is held
		YAHOO.util.Event.preventDefault(e);
	}

	handleClick(e) {
		var checkbox_node = YAHOO.util.Event.getTarget(e);
		this.updateNode(checkbox_node, e.shiftKey);
		this.updateCheckAll();
		this.last_clicked_index = checkbox_node._index;
	}

	updateCheckAll() {
		if (this.check_all === null) {
			return;
		}

		var count = 0;
		for (var i = 0; i < this.check_list.length; i++) {
			if (this.check_list[i].checked || this.check_list[i].disabled) {
				count++;
			} else if (count > 0) {
				break; // can't possibly be all checked or none checked
			}
		}

		this.check_all.setState(count > 0 && count === this.check_list.length);
	}

	checkAll(checked) {
		for (var i = 0; i < this.check_list.length; i++) {
			if (!this.check_list[i].disabled) {
				this.check_list[i].checked = checked;
			}
			this.updateNode(this.check_list[i]);
		}
	}

	checkBetween(a, b) {
		if (a > b) {
			var c = ++a;
			a = ++b;
			b = c;
		}

		for (var i = a; i < b; i++) {
			if (!this.check_list[i].disabled) {
				this.check_list[i].checked = true;
				this.view.selectItem(this.check_list[i], this.id);
			}
		}
	}

	uncheckBetween(a, b) {
		if (a > b) {
			var c = ++a;
			a = ++b;
			b = c;
		}

		for (var i = a; i < b; i++) {
			this.check_list[i].checked = false;
			this.view.deselectItem(this.check_list[i], this.id);
		}
	}

	updateNode(checkbox_node, shift_key) {
		if (checkbox_node.checked) {
			this.view.selectItem(checkbox_node, this.id);
			if (shift_key && this.last_clicked_index !== null) {
				this.checkBetween(
					this.last_clicked_index,
					checkbox_node._index
				);
			}
		} else {
			this.view.deselectItem(checkbox_node, this.id);
			if (shift_key && this.last_clicked_index !== null) {
				this.uncheckBetween(
					this.last_clicked_index,
					checkbox_node._index
				);
			}
		}
	}
}
