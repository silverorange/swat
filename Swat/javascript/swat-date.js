function dateSet(id) {
	var vDate = new Date();

	var year  = document.getElementById(id + '_year');
	var month = document.getElementById(id + '_month');
	var day   = document.getElementById(id + '_day');

	// stop if all 3 date parts aren't present
	if (!year || !month || !day) return false;

	if (month.selectedIndex == 0) {
		//reset
		day.selectedIndex = 0;
		year.selectedIndex = 0;
	} else {
		var this_month = vDate.getMonth() + 1;

		if (month.selectedIndex == this_month)
			today = true;
		else
			today = false;
					
		if (day.selectedIndex == 0) {
			if (today)
				day.selectedIndex = vDate.getDate();
			else
				day.selectedIndex = 1;
		}
	
		if (year.selectedIndex==0) {
			var first_year = year.options[1].value;
			var this_year  = vDate.getFullYear();
			year.selectedIndex = (this_year - first_year + 1);
		}
	}
}
