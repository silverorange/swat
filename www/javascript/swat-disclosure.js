function SwatDisclosure(id, open)
{
	this.div = document.getElementById(id);
	this.input = document.getElementById(id + '_input');
	this.animate_div = this.div.firstChild.nextSibling.nextSibling.firstChild;

	// get initial state
	if (this.input.value.length) {
		// remembered state from post values
		this.opened = (this.input.value == 'opened');
	} else {
		// initial display
		this.opened = open;
	}

	this.drawDisclosureLink();

	// prevent closing during opening animation and vice versa
	this.semaphore = false;

	// set initial display state
	if (this.opened)
		this.open();
	else
		this.close();
}

SwatDisclosure.prototype.toggle = function()
{
	if (this.opened) {
		this.closeWithAnimation();
	} else {
		this.openWithAnimation();
	}
}

SwatDisclosure.prototype.getSpan = function()
{
	return this.div.firstChild;
}

SwatDisclosure.prototype.drawDisclosureLink = function()
{
	var span = this.getSpan();
	if (span.firstChild && span.firstChild.nodeType == 3)
		var text = document.createTextNode(span.firstChild.nodeValue);
	else
		var text = document.createTextNode('');

	this.anchor = document.createElement('a');
	this.anchor.href = '#';
	if (this.opened)
		YAHOO.util.Dom.addClass(this.anchor, 'swat-disclosure-anchor-opened');
	else
		YAHOO.util.Dom.addClass(this.anchor, 'swat-disclosure-anchor-closed');

	YAHOO.util.Event.addListener(this.anchor, 'click',
		function(e)
		{
				YAHOO.util.Event.preventDefault(e);
				this.toggle();
		}, this, true);

	this.anchor.appendChild(text);

	span.parentNode.replaceChild(this.anchor, span);
}

SwatDisclosure.prototype.close = function()
{
	YAHOO.util.Dom.removeClass(this.div, 'swat-disclosure-control-opened');
	YAHOO.util.Dom.addClass(this.div, 'swat-disclosure-control-closed');

	YAHOO.util.Dom.removeClass(this.anchor, 'swat-disclosure-anchor-opened');
	YAHOO.util.Dom.addClass(this.anchor, 'swat-disclosure-anchor-closed');

	this.semaphore = false;

	this.input.value = 'closed';

	this.opened = false;
}

SwatDisclosure.prototype.closeWithAnimation = function()
{
	if (this.semaphore)
		return;

	YAHOO.util.Dom.removeClass(this.anchor, 'swat-disclosure-anchor-opened');
	YAHOO.util.Dom.addClass(this.anchor, 'swat-disclosure-anchor-closed');

	this.animate_div.style.overflow = 'hidden';
	this.animate_div.style.height = '';
	var attributes = { height: { to: 0 } }; 
	var animation = new YAHOO.util.Anim(this.animate_div, attributes, 0.25,
		YAHOO.util.Easing.easingIn); 

	this.semaphore = true;
	animation.onComplete.subscribe(this.handleClose, this, true);
	animation.animate();

	this.input.value = 'closed';
	this.opened = false;
}

SwatDisclosure.prototype.open = function()
{
	YAHOO.util.Dom.removeClass(this.div, 'swat-disclosure-control-closed');
	YAHOO.util.Dom.addClass(this.div, 'swat-disclosure-control-opened');

	YAHOO.util.Dom.removeClass(this.anchor, 'swat-disclosure-anchor-closed');
	YAHOO.util.Dom.addClass(this.anchor, 'swat-disclosure-anchor-opened');

	this.semaphore = false;

	this.input.value = 'opened';
	this.opened = true;
}

SwatDisclosure.prototype.openWithAnimation = function()
{
	if (this.semaphore)
		return;

	YAHOO.util.Dom.removeClass(this.div, 'swat-disclosure-control-closed');
	YAHOO.util.Dom.addClass(this.div, 'swat-disclosure-control-opened');

	YAHOO.util.Dom.removeClass(this.anchor, 'swat-disclosure-anchor-closed');
	YAHOO.util.Dom.addClass(this.anchor, 'swat-disclosure-anchor-opened');

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
	animation.onComplete.subscribe(this.handleOpen, this, true);
	animation.animate();

	this.input.value = 'opened';
	this.opened = true;
}

SwatDisclosure.prototype.handleClose = function()
{
	YAHOO.util.Dom.removeClass(this.div, 'swat-disclosure-control-opened');
	YAHOO.util.Dom.addClass(this.div, 'swat-disclosure-control-closed');

	this.semaphore = false;
}

SwatDisclosure.prototype.handleOpen = function()
{
	// allow font resizing to work again
	this.animate_div.style.height = '';

	// re-set overflow to visible for styles that might depend on it
	this.animate_div.style.overflow = 'visible';

	this.semaphore = false;
}

function SwatFrameDisclosure(id, open)
{
	SwatFrameDisclosure.superclass.constructor.call(this, id, open);
}

YAHOO.lang.extend(SwatFrameDisclosure, SwatDisclosure, {

getSpan: function()
{
	return this.div.firstChild.firstChild;
}

});
