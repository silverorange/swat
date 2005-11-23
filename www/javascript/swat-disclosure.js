function SwatDisclosure(id)
{
	this.image = document.getElementById(id + '_img');
	this.input = document.getElementById(id + '_input');
	this.div = document.getElementById(id);

	// get initial state
	if (this.input.value == 'opened') {
		this.open();
	} else {
		this.close();
	}
}

SwatDisclosure.open_text = 'open';
SwatDisclosure.close_text = 'close';

SwatDisclosure.prototype.toggle = function()
{
	if (this.opened) {
		this.close();
	} else {
		this.open();
	}
}

SwatDisclosure.prototype.close = function()
{
	this.div.className = 'swat-disclosure-control-closed';
	this.image.src = 'swat/images/disclosure-closed.png';
	this.image.alt = SwatDisclosure.open_text;
	this.input.value = 'closed';
	this.opened = false;
}

SwatDisclosure.prototype.open = function()
{
	this.div.className = 'swat-disclosure-control-opened';
	this.image.src = 'swat/images/disclosure-open.png';
	this.image.alt = SwatDisclosure.close_text;
	this.input.value = 'opened';
	this.opened = true;
}
