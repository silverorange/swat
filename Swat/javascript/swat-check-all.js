function swatCheckAll(form, name, series) {
	check_all = document.getElementById(name);

	for (i = 0; i < form.elements[series + '[]'].length; i++) {
		var chkbox = form.elements[series + '[]'][i];
	
		if (check_all && chkbox.type=='checkbox') {
			chkbox.checked = check_all.checked;

			//TODO: make the highlighting work once we sort it out
			//if (theForm.chkall.checked) HLClass(chkbox,"highlight");
			//else HLClass(chkbox,"");
		}
	}
}

//TODO: add javascript to set check_all = true/false if all
// checkboxes are checked. We need to figure out how to best
// add this to the onclick event of the checkboxes on the page
