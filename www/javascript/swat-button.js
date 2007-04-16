function SwatButton(id, show_processing_throbber)
{
	this.id = id;

	this.button = document.getElementById(this.id);
	this.show_processing_throbber = show_processing_throbber;
	this.processing_message = '';

	YAHOO.util.Event.addListener(this.button, 'click',
		this.handleClick, this, true);
}

SwatButton.throbber_alt_text = 'throbber';
SwatButton.throbber_image = new Image();
SwatButton.throbber_image.src = 'packages/swat/images/swat-button-throbber.gif';

SwatButton.prototype.handleClick = function()
{
	if (this.show_processing_throbber) {
		this.button.disabled = true;

		// add button to form data manually since we disabled it above
		var div = document.createElement('div');
		var hidden_field = document.createElement('input');
		hidden_field.type = 'hidden';
		hidden_field.name = this.id;
		hidden_field.value = this.button.value;
		div.appendChild(hidden_field);
		this.button.form.appendChild(div);
		
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
	image.src = SwatButton.throbber_image.src;
	image.width = '16';
	image.height = '16';
	image.alt = SwatButton.throbber_alt_text;
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
