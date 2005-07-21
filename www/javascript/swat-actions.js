function swatActionsDisplay(action, id) {
	for (i = 0; i < action.options.length; i++) {
		element = document.getElementById(id + '_' + action.options[i].value);
		if (element) {
			if (action.value == action.options[i].value)
				element.className ='';
			else
				element.className = 'swat-hidden';
		}
	}
}
