function SwatExpandableCheckboxTree(id, dependent_boxes)
{
	this.id = id;

	// This flag sets the behaviour of checkboxes. If it is true then checking
	// a parent will check its children and checking all children of a parent
	// will check the parent.
	if (dependent_boxes) {
		this.initTree();

		// get all checkboxes in this tree
		this.check_list = document.getElementsByName(id + '[]');

		for (var i = 0; i < this.check_list.length; i++) {
			YAHOO.util.Event.addListener(this.check_list[i], 'click',
				SwatExpandableCheckboxTree.handleClick, this);
		}
	}
}

SwatExpandableCheckboxTree.open_text = 'open';
SwatExpandableCheckboxTree.close_text = 'close';

SwatExpandableCheckboxTree.handleClick = function(event, tree)
{
	var checkbox = YAHOO.util.Event.getTarget(event);
	tree.handleClick(checkbox);
}

SwatExpandableCheckboxTree.prototype.initTree = function()
{
	var tree = document.getElementById(this.id);
	var branch = null;

	if (tree.firstChild && tree.firstChild.firstChild)
		branch = tree.firstChild.firstChild;

	// init all top-level checkboxes
	if (branch !== null) {
		var child_node = null;
		var child_checkbox = null;

		for (var i = 0; i < branch.childNodes.length; i++) {
			child_node = branch.childNodes[i];
			if (child_node.nodeName == 'LI') {
				child_checkbox = child_node.firstChild;

				// some nodes have expander links, the checkbox is the next node
				if (child_checkbox.nodeName == 'A')
					child_checkbox = child_checkbox.nextSibling;

				if (child_checkbox.nodeName == 'INPUT' &&
					child_checkbox.getAttribute('type') == 'checkbox') {
					this.initTreeNode(child_checkbox);
				}
			}
		}
	}
}

SwatExpandableCheckboxTree.prototype.initTreeNode = function(checkbox)
{
	var path = checkbox.id.substr(this.id.length + 1);
	var branch = document.getElementById(this.id + '_' + path + '_branch');

	if (branch) {
		var children_checked = true;
		var child_node = null;
		var child_checkbox = null;

		for (var i = 0; i < branch.childNodes.length; i++) {
			child_node = branch.childNodes[i];
			if (child_node.nodeName == 'LI') {
				child_checkbox = child_node.firstChild;

				// some nodes have expander links, the checkbox is the next node
				if (child_checkbox.nodeName == 'A')
					child_checkbox = child_checkbox.nextSibling;

				if (child_checkbox.nodeName == 'INPUT' &&
					child_checkbox.getAttribute('type') == 'checkbox' &&
					!this.initTreeNode(child_checkbox)) {
					children_checked = false;
					break;
				}
			}
		}

		// check this node based on state of children
		checkbox.checked = children_checked;
	}

	return checkbox.checked;
}

SwatExpandableCheckboxTree.prototype.handleClick = function(checkbox)
{
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
}

SwatExpandableCheckboxTree.prototype.toggleBranch = function(branch_id)
{
	var branch = document.getElementById(this.id + '_' + branch_id + '_branch');
	var opened = YAHOO.util.Dom.hasClass(branch,
		'swat-expandable-checkbox-tree-opened');

	if (opened) {
		this.closeBranch(branch_id);
	} else {
		this.openBranch(branch_id);
	}
}

SwatExpandableCheckboxTree.prototype.openBranch = function(branch_id)
{
	var branch = document.getElementById(this.id + '_' + branch_id + '_branch');
	var image = document.getElementById(this.id + '_' + branch_id + '_img');

	YAHOO.util.Dom.removeClass(branch, 'swat-expandable-checkbox-tree-closed');
	YAHOO.util.Dom.addClass(branch, 'swat-expandable-checkbox-tree-opened');

	image.src = 'packages/swat/images/swat-disclosure-open.png';
	image.alt = SwatExpandableCheckboxTree.closed_text;

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
}

SwatExpandableCheckboxTree.prototype.closeBranch = function(branch_id)
{
	var branch = document.getElementById(this.id + '_' + branch_id + '_branch');
	var image = document.getElementById(this.id + '_' + branch_id + '_img');

	image.src = 'packages/swat/images/swat-disclosure-closed.png';
	image.alt = SwatExpandableCheckboxTree.open_text;

	branch.style.overflow = 'hidden';
	branch.style.height = '';

	var attributes = { height: { to: 0 } };
	var animation = new YAHOO.util.Anim(branch, attributes, 0.25,
		YAHOO.util.Easing.easingIn);

	animation.onComplete.subscribe(
		SwatExpandableCheckboxTree.handleBranchClose, [this, branch]);

	animation.animate();
}

SwatExpandableCheckboxTree.handleBranchOpen = function(type, args, data)
{
	var tree = data[0];
	var branch = data[1];

	branch.style.height = '';
}

SwatExpandableCheckboxTree.handleBranchClose = function(type, args, data)
{
	var tree = data[0];
	var branch = data[1];

	YAHOO.util.Dom.addClass(branch, 'swat-expandable-checkbox-tree-closed');
	YAHOO.util.Dom.removeClass(branch, 'swat-expandable-checkbox-tree-opened');
}
