function SwatExpandableCheckboxTree(id, dependent_boxes)
{
	var self = this;
	this.id = id;

	// This flag sets the behaviour of checkboxes. If it is true then checking
	// a parent will check its children and checking all children of a parent
	// will check the parent.
	if (dependent_boxes) {

		// get all checkboxes in this tree
		this.check_list = document.getElementsByName(id + '[]');

		this.has_add_event = (document.addEventListener) ? true : false;
		
		
		if (this.has_add_event) {
			for (var i = 0; i < this.check_list.length; i++) {
				this.check_list[i].addEventListener('change', handleClick, false);
			}
		} else {
			for (var i = 0; i < this.check_list.length; i++) {
				this.check_list[i].attachEvent('onclick', handleClick);
			}
		}

	}

	function handleClick(event)
	{
		self.handleClick(this);
	}
}

SwatExpandableCheckboxTree.open_text = 'open';
SwatExpandableCheckboxTree.close_text = 'close';

SwatExpandableCheckboxTree.prototype.handleClick = function(checkbox)
{
	// get path of checkbox from id
	var path = checkbox.id.substr(this.id.length + 1);

	branch = document.getElementById(this.id + '_' + path + '_branch');

	// check all sub-elements
	// ignore leaves
	if (branch) {
		
		checkboxes = branch.getElementsByTagName('input');

		for (var i = 0; i < checkboxes.length; i++) {
			if (checkboxes[i].getAttribute('type') == 'checkbox') {
				checkboxes[i].checked = checkbox.checked;
			}
		}

	}
	
	// check parent elements
	// split path into pieces
	var path_exp = path.split('/');

	// skip the root
	var root = path_exp.shift();

	var count = path_exp.length;

	// for each parent, check if all direct children are checked
	for (var i = 0; i < count - 1; i++) {
		path_exp.pop();

		var parent_path = root + '/' + path_exp.join('/');

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
	branch = document.getElementById(this.id + '_' + branch_id + '_branch');
	image = document.getElementById(this.id + '_' + branch_id + '_img');

	opened = (branch.className == 'swat-expandable-checkbox-tree-opened');

	if (opened) {
		branch.className = 'swat-expandable-checkbox-tree-closed';
		image.src = 'packages/swat/images/swat-disclosure-closed.png';
		image.alt = SwatExpandableCheckboxTree.open_text;
	} else {
		branch.className = 'swat-expandable-checkbox-tree-opened';
		image.src = 'packages/swat/images/swat-disclosure-open.png';
		image.alt = SwatExpandableCheckboxTree.closed_text;
	}
}
