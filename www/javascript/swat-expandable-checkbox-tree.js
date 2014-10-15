function SwatExpandableCheckboxTree(id, dependent_boxes, branch_state,
	expandable_node_ids)
{
	this.id = id;

	this.dependent_boxes = dependent_boxes;
	this.branch_state = branch_state;

	for (var i = 0; i < expandable_node_ids.length; i++)
		this.drawExpander(expandable_node_ids[i]);

	this.initTree();

	/*
	 * This flag sets the behaviour of checkboxes. If it is true then checking
	 * a parent will check its children and checking all children of a parent
	 * will check the parent.
	 */
	if (this.dependent_boxes) {
		// get all checkboxes in this tree
		this.check_list = document.getElementsByName(id + '[]');

		for (var i = 0; i < this.check_list.length; i++) {
			YAHOO.util.Event.addListener(this.check_list[i], 'click',
				this.handleClick, this, true);

			YAHOO.util.Event.addListener(this.check_list[i], 'dblclick',
				this.handleClick, this, true);
		}
	}
}

SwatExpandableCheckboxTree.BRANCH_STATE_OPEN   = 1;
SwatExpandableCheckboxTree.BRANCH_STATE_CLOSED = 2;
SwatExpandableCheckboxTree.BRANCH_STATE_AUTO   = 3;

SwatExpandableCheckboxTree.prototype.drawExpander = function(expander_node_id)
{
	var list_item = document.getElementById(
		this.id + '_' + expander_node_id + '_container');

	var anchor = document.createElement('a');
	anchor.id = this.id + '_' + expander_node_id + '_anchor';
	anchor.href = '#';
	YAHOO.util.Dom.addClass(anchor,
		'swat-expandable-checkbox-tree-anchor-opened');

	YAHOO.util.Event.addListener(anchor, 'click',
		function (e, args)
		{
			YAHOO.util.Event.preventDefault(e);
			args[0].toggleBranch(args[1]);
		}, [this, expander_node_id]);

	list_item.insertBefore(anchor, list_item.firstChild);

	YAHOO.util.Dom.addClass(list_item, 'swat-expandable-checkbox-tree-expander');
};

SwatExpandableCheckboxTree.getTreeNode = function(branch_child_node)
{
	var child_node = null;
	var returned_node = null;

	if (branch_child_node.nodeName == 'LI') {
		child_node = branch_child_node.firstChild;

		/*
		 * Some nodes have expander links, the node we're looking for is the
		 * next node.
		 */
		if (child_node.nodeName == 'A')
			child_node = child_node.nextSibling;

		// look for a checkbox
		if (child_node.nodeName == 'INPUT' &&
			child_node.getAttribute('type') == 'checkbox') {
			returned_node = child_node;
		}

		/*
		 * Look for a span. This occurs when dependent checkboxes is off and a
		 * null value is used for a node.
		 */
		if (child_node.nodeName == 'SPAN' &&
			YAHOO.util.Dom.hasClass(child_node,
				'swat-expandable-checkbox-tree-null-node')) {

			returned_node = child_node;
		}
	}

	return returned_node;
};

SwatExpandableCheckboxTree.prototype.initTree = function()
{
	var self = SwatExpandableCheckboxTree;
	var tree = document.getElementById(this.id);
	var branch = null;

	if (tree.firstChild && tree.firstChild.firstChild)
		branch = tree.firstChild.firstChild;

	// initialize all top-level tree nodes
	if (branch) {
		var child_node = null;
		for (var i = 0; i < branch.childNodes.length; i++) {
			child_node = self.getTreeNode(branch.childNodes[i]);
			if (child_node)
				this.initTreeNode(child_node);
		}
	}
};

SwatExpandableCheckboxTree.prototype.initTreeNode = function(node)
{
	var self = SwatExpandableCheckboxTree;
	var path = node.getAttribute('id').substr(this.id.length + 1);
	var branch = document.getElementById(this.id + '_' + path + '_branch');
	var is_checkbox_node =
		(node.nodeName == 'INPUT' && node.getAttribute('type') == 'checkbox');

	if (is_checkbox_node) {
		var all_children_checked = node.checked;
		var any_children_checked = node.checked;
	} else {
		var all_children_checked = false;
		var any_children_checked = false;
	}

	if (branch) {
		var state;
		var child_node = null;

		all_children_checked = true;
		any_children_checked = false;
		for (var i = 0; i < branch.childNodes.length; i++) {
			child_node = self.getTreeNode(branch.childNodes[i]);
			if (child_node) {
				state = this.initTreeNode(child_node);
				all_children_checked =
					all_children_checked && state['all_children_checked'];

				any_children_checked =
					any_children_checked || state['any_children_checked'];
			}
		}

		// check this node if all children are checked
		if (this.dependent_boxes && is_checkbox_node)
			node.checked = all_children_checked;

		// close this node if no children are checked or branch state is closed
		if (this.branch_state == self.BRANCH_STATE_CLOSED ||
			(!any_children_checked &&
			this.branch_state == self.BRANCH_STATE_AUTO))
			this.closeBranch(path);
	}

	return { 'all_children_checked': all_children_checked,
		'any_children_checked': any_children_checked };
};

SwatExpandableCheckboxTree.prototype.handleClick = function(e)
{
	var checkbox = YAHOO.util.Event.getTarget(e);

	// get path of checkbox from id
	var path = checkbox.id.substr(this.id.length + 1);
	var branch = document.getElementById(this.id + '_' + path + '_branch');

	// check all sub-elements
	// ignore leaves
	if (branch) {
		var checkboxes = branch.getElementsByTagName('input');

		for (var i = 0; i < checkboxes.length; i++) {
			if (checkboxes[i].getAttribute('type') == 'checkbox') {
				checkboxes[i].checked = checkbox.checked;
			}
		}
	}

	// check parent elements
	// split path into pieces
	var path_exp = path.split('.');

	// skip the root
	var root = path_exp.shift();

	var count = path_exp.length;

	// for each parent, check if all direct children are checked
	for (var i = 0; i < count - 1; i++) {
		path_exp.pop();

		var parent_path = root + '.' + path_exp.join('.');

		// get parent checkbox element
		var parent_checkbox =
			document.getElementById(this.id + '_' + parent_path);

		// get parent branch
		var branch =
			document.getElementById(this.id + '_' + parent_path + '_branch');

		var checkboxes = branch.getElementsByTagName('input');
		var all_checked = true;

		// get state of all checkboxes below parent
		for (var j = 0; j < checkboxes.length; j++) {
			if (checkboxes[j].getAttribute('type') == 'checkbox') {
				if (!checkboxes[j].checked) {
					all_checked = false;
					break;
				}
			}
		}

		parent_checkbox.checked = all_checked;
	}
};

SwatExpandableCheckboxTree.prototype.toggleBranch = function(branch_id)
{
	var branch = document.getElementById(this.id + '_' + branch_id + '_branch');
	var opened = YAHOO.util.Dom.hasClass(branch,
		'swat-expandable-checkbox-tree-opened');

	if (opened) {
		this.closeBranchWithAnimation(branch_id);
	} else {
		this.openBranchWithAnimation(branch_id);
	}
};

SwatExpandableCheckboxTree.prototype.openBranch = function(branch_id)
{
	var branch = document.getElementById(this.id + '_' + branch_id + '_branch');
	var anchor = document.getElementById(this.id + '_' + branch_id + '_anchor');

	YAHOO.util.Dom.removeClass(branch, 'swat-expandable-checkbox-tree-closed');
	YAHOO.util.Dom.addClass(branch, 'swat-expandable-checkbox-tree-opened');

	YAHOO.util.Dom.removeClass(anchor,
		'swat-expandable-checkbox-tree-anchor-closed');

	YAHOO.util.Dom.addClass(anchor,
		'swat-expandable-checkbox-tree-anchor-opened');
};

SwatExpandableCheckboxTree.prototype.closeBranch = function(branch_id)
{
	var branch = document.getElementById(this.id + '_' + branch_id + '_branch');
	var anchor = document.getElementById(this.id + '_' + branch_id + '_anchor');

	YAHOO.util.Dom.addClass(branch, 'swat-expandable-checkbox-tree-closed');
	YAHOO.util.Dom.removeClass(branch, 'swat-expandable-checkbox-tree-opened');

	YAHOO.util.Dom.addClass(anchor,
		'swat-expandable-checkbox-tree-anchor-closed');

	YAHOO.util.Dom.removeClass(anchor,
		'swat-expandable-checkbox-tree-anchor-opened');
};

SwatExpandableCheckboxTree.prototype.openBranchWithAnimation = function(
	branch_id)
{
	var branch = document.getElementById(this.id + '_' + branch_id + '_branch');
	var anchor = document.getElementById(this.id + '_' + branch_id + '_anchor');

	YAHOO.util.Dom.removeClass(branch, 'swat-expandable-checkbox-tree-closed');
	YAHOO.util.Dom.addClass(branch, 'swat-expandable-checkbox-tree-opened');

	YAHOO.util.Dom.removeClass(anchor,
		'swat-expandable-checkbox-tree-anchor-closed');

	YAHOO.util.Dom.addClass(anchor,
		'swat-expandable-checkbox-tree-anchor-opened');

	// get display height
	branch.parentNode.style.overflow = 'hidden';
	branch.parentNode.style.height = '0';
	branch.style.visibility = 'hidden';
	branch.style.overflow = 'hidden';
	branch.style.display = 'block';
	branch.style.height = '';
	var height = branch.offsetHeight;
	branch.style.height = '0';
	branch.style.visibility = 'visible';
	branch.parentNode.style.height = '';
	branch.parentNode.style.overflow = 'visible';

	var attributes = { height: { to: height, from: 0 } };
	var animation = new YAHOO.util.Anim(branch, attributes, 0.25,
		YAHOO.util.Easing.easeOut);

	animation.onComplete.subscribe(
		SwatExpandableCheckboxTree.handleBranchOpen, [this, branch]);

	animation.animate();
};

SwatExpandableCheckboxTree.prototype.closeBranchWithAnimation = function(
	branch_id)
{
	var branch = document.getElementById(this.id + '_' + branch_id + '_branch');
	var anchor = document.getElementById(this.id + '_' + branch_id + '_anchor');

	YAHOO.util.Dom.addClass(anchor,
		'swat-expandable-checkbox-tree-anchor-closed');

	YAHOO.util.Dom.removeClass(anchor,
		'swat-expandable-checkbox-tree-anchor-opened');

	branch.style.overflow = 'hidden';
	branch.style.height = '';

	var attributes = { height: { to: 0 } };
	var animation = new YAHOO.util.Anim(branch, attributes, 0.25,
		YAHOO.util.Easing.easingIn);

	animation.onComplete.subscribe(
		SwatExpandableCheckboxTree.handleBranchClose, [this, branch]);

	animation.animate();
};

SwatExpandableCheckboxTree.handleBranchOpen = function(type, args, data)
{
	var tree = data[0];
	var branch = data[1];

	branch.style.height = '';
};

SwatExpandableCheckboxTree.handleBranchClose = function(type, args, data)
{
	var tree = data[0];
	var branch = data[1];

	YAHOO.util.Dom.addClass(branch, 'swat-expandable-checkbox-tree-closed');
	YAHOO.util.Dom.removeClass(branch, 'swat-expandable-checkbox-tree-opened');
};
