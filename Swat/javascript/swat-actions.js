function swatActionsDisplay(id, value) {
	if (this.lastItem)
		this.lastItem.className = 'swat-hidden';
		
	element = document.getElementById(id + '_' + value);
	if (element) {
		element.className = '';
		this.lastItem = element;
	}
}
