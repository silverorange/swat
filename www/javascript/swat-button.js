function SwatButton(id, show_processing_throbber)
{
	this.id = id;

	this.button = document.getElementById(this.id);
	this.show_processing_throbber = show_processing_throbber;
	this.processing_message = '';

	YAHOO.util.Event.addListener(this.button, 'click',
		this.handleClick, this, true);
}

SwatButton.throbber_image = new Image();
SwatButton.throbber_image.src = 'packages/swat/images/swat-button-throbber.gif';

SwatButton.prototype.handleClick = function(event, object)
{
	if (this.show_processing_throbber) {
		this.button.disabled = true;
		this.button.form.submit(); // needed for IE
		this.showThrobber();
	}
}

SwatButton.prototype.showThrobber = function()
{
	var span = document.createElement('span');
	YAHOO.util.Dom.addClass(span, 'swat-button-processing-throbber');

	var text = document.createTextNode(this.processing_message);
	var image = document.createElement('img');
	image.setAttribute('src', SwatButton.throbber_image.src);
	image.setAttribute('width', '16');
	image.setAttribute('height', '16');
	image.setAttribute('alt', 'throbber');
	span.appendChild(image);
	span.appendChild(text);
	this.button.parentNode.appendChild(span);
	var animation = new YAHOO.util.Anim(span, { opacity: { to: 0.5 }}, 1,
		YAHOO.util.Easing.easingNone);

	animation.animate();
}

SwatButton.prototype.setProcessingMessage = function(message)
{
	this.processing_message = message;
}
