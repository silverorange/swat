function dateSet(id, activeFlydown) {
	var vDate = new Date();

	var year  = document.getElementById(id + '_year');
	var month = document.getElementById(id + '_month');
	var day   = document.getElementById(id + '_day');

	//month is required for this, so stop if it doesn't exist
	if (!month) return true;

	if (activeFlydown.value == 0) {
		//reset
		if (day) day.selectedIndex = 0;
		if (year) year.selectedIndex = 0;
		if (month) month.selectedIndex = 0;
	} else {
		var this_month = vDate.getMonth() + 1;
		
		if (day) {
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
		}
	
		if (year && year.selectedIndex == 0) {
			var first_year = year.options[1].value;
			var this_year  = vDate.getFullYear();
			year.selectedIndex = (this_year - first_year + 1);
		}
	}
}
