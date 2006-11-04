function SwatDisclosure(id, open)
{
	this.image = document.getElementById(id + '_img');
	this.input = document.getElementById(id + '_input');
	this.div = document.getElementById(id);
	this.animate_div = this.div.firstChild.nextSibling.firstChild;

	// prevent closing during opening animation and vice versa
	this.semaphore = false;

	// get initial state
	if (this.input.value.length) {
		// remembered state from post values
		if (this.input.value == 'opened') {
			this.open();
		} else {
			this.close();
		}
	} else {
		// initial display
		if (open) {
			this.open();
		} else {
			this.close();
		}
	}
}

SwatDisclosure.open_text = 'open';
SwatDisclosure.close_text = 'close';

SwatDisclosure.prototype.toggle = function()
{
	if (this.opened) {
		this.closeWithAnimation();
	} else {
		this.openWithAnimation();
	}
}

SwatDisclosure.prototype.close = function()
{
	YAHOO.util.Dom.removeClass(this.div, 'swat-disclosure-control-opened');
	YAHOO.util.Dom.addClass(this.div, 'swat-disclosure-control-closed');

	this.semaphore = false;

	this.image.src = 'packages/swat/images/swat-disclosure-closed.png';
	this.image.alt = SwatDisclosure.open_text;
	this.input.value = 'closed';

	this.opened = false;
}

SwatDisclosure.prototype.closeWithAnimation = function()
{
	if (this.semaphore)
		return;

	this.animate_div.style.overflow = 'hidden';
	this.animate_div.style.height = '';
	var attributes = { height: { to: 0 } }; 
	var animation = new YAHOO.util.Anim(this.animate_div, attributes, 0.25,
		YAHOO.util.Easing.easingIn); 

	this.semaphore = true;
	animation.onComplete.subscribe(SwatDisclosure.handleClose, this);
	animation.animate();

	this.image.src = 'packages/swat/images/swat-disclosure-closed.png';
	this.image.alt = SwatDisclosure.open_text;
	this.input.value = 'closed';
	this.opened = false;
}

SwatDisclosure.prototype.open = function()
{
	YAHOO.util.Dom.removeClass(this.div, 'swat-disclosure-control-closed');
	YAHOO.util.Dom.addClass(this.div, 'swat-disclosure-control-opened');

	this.semaphore = false;

	this.image.src = 'packages/swat/images/swat-disclosure-open.png';
	this.image.alt = SwatDisclosure.close_text;
	this.input.value = 'opened';
	this.opened = true;
}

SwatDisclosure.prototype.openWithAnimation = function()
{
	if (this.semaphore)
		return;

	YAHOO.util.Dom.removeClass(this.div, 'swat-disclosure-control-closed');
	YAHOO.util.Dom.addClass(this.div, 'swat-disclosure-control-opened');

	// get display height
	this.animate_div.parentNode.style.overflow = 'hidden';
	this.animate_div.parentNode.style.height = '0';
	this.animate_div.style.visibility = 'hidden';
	this.animate_div.style.overflow = 'hidden';
	this.animate_div.style.display = 'block';
	this.animate_div.style.height = '';
	var height = this.animate_div.offsetHeight;
	this.animate_div.style.height = '0';
	this.animate_div.style.visibility = 'visible';
	this.animate_div.parentNode.style.height = '';
	this.animate_div.parentNode.style.overflow = 'visible';
	
	var attributes = { height: { to: height, from: 0 } }; 
	var animation = new YAHOO.util.Anim(this.animate_div, attributes, 0.5,
		YAHOO.util.Easing.easeOut); 

	this.semaphore = true;
	animation.onComplete.subscribe(SwatDisclosure.handleOpen, this);
	animation.animate();

	this.image.src = 'packages/swat/images/swat-disclosure-open.png';
	this.image.alt = SwatDisclosure.close_text;
	this.input.value = 'opened';
	this.opened = true;
}

SwatDisclosure.handleClose = function(type, args, disclosure)
{
	YAHOO.util.Dom.removeClass(disclosure.div,
		'swat-disclosure-control-opened');

	YAHOO.util.Dom.addClass(disclosure.div, 'swat-disclosure-control-closed');
	disclosure.semaphore = false;
}

SwatDisclosure.handleOpen = function(type, args, disclosure)
{
	// allow font resizing to work again
	disclosure.animate_div.style.height = '';
	disclosure.semaphore = false;
}
