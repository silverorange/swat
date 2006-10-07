function SwatDisclosure(id)
{
	this.image = document.getElementById(id + '_img');
	this.input = document.getElementById(id + '_input');
	this.div = document.getElementById(id);
	this.content_div = this.div.firstChild.nextSibling;

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
	var attributes = { opacity: { from: 1, to: 0} }; 
	var fade_animation =
		new YAHOO.util.Anim(this.content_div, attributes, 0.25); 

	fade_animation.animate();
	fade_animation.onComplete.subscribe(SwatDisclosure.handleClose, this.div);
	this.image.src = 'packages/swat/images/swat-disclosure-closed.png';
	this.image.alt = SwatDisclosure.open_text;
	this.input.value = 'closed';
	this.opened = false;
}

SwatDisclosure.prototype.open = function()
{
	var attributes = { opacity: { from: 0, to: 1} }; 
	var fade_animation =
		new YAHOO.util.Anim(this.content_div, attributes, 0.5); 

	fade_animation.animate();
	this.div.className = 'swat-disclosure-control-opened';
	this.image.src = 'packages/swat/images/swat-disclosure-open.png';
	this.image.alt = SwatDisclosure.close_text;
	this.input.value = 'opened';
	this.opened = true;
}

SwatDisclosure.handleClose = function(type, args, div)
{
	div.className = 'swat-disclosure-control-closed';
}
