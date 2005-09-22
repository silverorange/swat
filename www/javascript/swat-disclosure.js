function SwatDisclosure(id)
{
	this.image = document.getElementById(id + '_img');
	this.div = document.getElementById(id);

	// get initial state
	if (this.div.className == 'swat-disclosure-container-opened') {
		this.opened = true;
	} else {
		this.opened = false;
	}
}

SwatDisclosure.open_text = 'open';
SwatDisclosure.close_text = 'close';

SwatDisclosure.prototype.toggle = function()
{
	if (this.opened) {
		this.div.className = 'swat-disclosure-container-closed';
		this.image.src = 'swat/images/disclosure-closed.png';
		this.image.alt = SwatDisclosure.open_text;
	} else {
		this.div.className = 'swat-disclosure-container-opened';
		this.image.src = 'swat/images/disclosure-open.png';
		this.image.alt = SwatDisclosure.close_text;
	}
	this.opened = !this.opened;
}
