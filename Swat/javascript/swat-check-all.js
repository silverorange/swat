function SwatCheckbox() {
	this.highlightClass = 'swat-table-view-highlight';

	this.highlightRow = function(chk) {
		var tr = chk.parentNode.parentNode;
		if (tr.nodeName == 'TR') {
			if (chk.checked)
				var class_name = this.highlightClass;	
			else
				var class_name = '';
	
			for (j = 0; j < tr.childNodes.length; j++)
				tr.childNodes[j].className = class_name;
		}
	}
}


SwatCheckbox.prototype.checkAll = function (chk_all, series) {
	var elements = chk_all.form.elements[series + '[]'];
	for (i = 0; i < elements.length; i++) {
		elements[i].checked = chk_all.checked;
		this.highlightRow(elements[i]);
	}
}

SwatCheckbox.prototype.check = function (chk) {
	this.highlightRow(chk);

	//TODO: figure out how to name the check-all element dynamically
	check_all = document.getElementById('SwatCheckAll0');
	if (!check_all) return;

	var checked = 0;
	var elements = chk.form.elements[chk.name];
	for (i = 0; i < elements.length; i++)
		if (elements[i].checked == true)
			checked = checked + 1;
	
	check_all.checked = (elements.length == checked);
}

var SwatCheckbox = new SwatCheckbox();
