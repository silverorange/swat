function toggleDisclosureWidget(id) {
	var img = document.getElementById(id + '_img');
	var div = document.getElementById(id);

	if (div.className == 'swat-disclosure-container-opened') {
		div.className = 'swat-disclosure-container-closed';
		img.src = 'swat/images/disclosure-closed.png';
		img.alt = 'open';
	} else {
		div.className = 'swat-disclosure-container-opened';
		img.src = 'swat/images/disclosure-open.png';
		img.alt = 'close';
	}
}
