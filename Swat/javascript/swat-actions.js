function swatActionsDisplay(action, name) {
	for (i = 0; i < action.options.length; i++) {
		element = document.getElementById(name + '_' + action.options[i].value);
		if (element) {
			if (action.value == action.options[i].value)
				element.className ='';
			else
				element.className = 'swat-hidden';
		}
	}
}
