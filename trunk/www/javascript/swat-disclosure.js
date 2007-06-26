function SwatDisclosure(id, open)
{
	this.div = document.getElementById(id);
	this.input = document.getElementById(id + '_input');
	this.animate_div = this.div.firstChild.nextSibling.nextSibling.firstChild;

	this.drawDisclosureLink();

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

// preload images
SwatDisclosure.open_image = new Image();
SwatDisclosure.open_image.src =
	'packages/swat/images/swat-disclosure-open.png';

SwatDisclosure.closed_image = new Image();
SwatDisclosure.closed_image.src =
	'packages/swat/images/swat-disclosure-closed.png';

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
		var text = document.createTextNode(' ' + span.firstChild.nodeValue);
	else
		var text = document.createTextNode('');

	this.image = document.createElement('img');
	this.image.src = SwatDisclosure.open_image.src;
	this.image.alt = SwatDisclosure.close_text;
	this.image.width = '16';
	this.image.height = '16';
	YAHOO.util.Dom.addClass(this.image, 'swat-disclosure-image');

	var anchor = document.createElement('a');
	anchor.href = '#';
	YAHOO.util.Dom.addClass(anchor, 'swat-disclosure-anchor');
	YAHOO.util.Event.addListener(anchor, 'click',
		function(e, disclosure)
		{
				YAHOO.util.Event.preventDefault(e);
				disclosure.toggle();
		}, this);

	anchor.appendChild(this.image);
	anchor.appendChild(text);

	span.parentNode.replaceChild(anchor, span);
}

SwatDisclosure.prototype.close = function()
{
	YAHOO.util.Dom.removeClass(this.div, 'swat-disclosure-control-opened');
	YAHOO.util.Dom.addClass(this.div, 'swat-disclosure-control-closed');

	this.semaphore = false;

	this.image.src = SwatDisclosure.closed_image.src;
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
	animation.onComplete.subscribe(this.handleClose, this, true);
	animation.animate();

	this.image.src = SwatDisclosure.closed_image.src;
	this.image.alt = SwatDisclosure.open_text;
	this.input.value = 'closed';
	this.opened = false;
}

SwatDisclosure.prototype.open = function()
{
	YAHOO.util.Dom.removeClass(this.div, 'swat-disclosure-control-closed');
	YAHOO.util.Dom.addClass(this.div, 'swat-disclosure-control-opened');

	this.semaphore = false;

	this.image.src = SwatDisclosure.open_image.src;
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
	animation.onComplete.subscribe(this.handleOpen, this, true);
	animation.animate();

	this.image.src = SwatDisclosure.open_image.src;
	this.image.alt = SwatDisclosure.close_text;
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
