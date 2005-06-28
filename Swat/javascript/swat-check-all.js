function SwatCheckAll(id, series) {
	var check_all = document.getElementById(id);
	var check_list = document.getElementsByName(series);
	var form = check_all.form;
	var is_ie = (form.addEventListener) ? false : true;

	if (is_ie) {
		form.attachEvent("onclick", eventHandler);
		check_all.attachEvent("onclick", checkAll, false);
	} else {
		form.addEventListener("change", eventHandler, false);
		check_all.addEventListener("change", checkAll, false);
	}

	function eventHandler(event) {
		var name = (is_ie) ? event.srcElement.name : event.target.name;	

		if (name != series)
			return;

		var count = 0;
		for (i = 0; i < check_list.length; i++)
			if (check_list[i].checked)
				count++;
			else if (count > 0)
				break; //can't possibly be all checked or none checked

		check_all.checked = (count == check_list.length);
	}

	function checkAll(event) {
		for (i = 0; i < check_list.length; i++)
			check_list[i].checked = check_all.checked;
	}
}
