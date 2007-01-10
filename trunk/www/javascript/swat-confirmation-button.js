function SwatConfirmationButton(id)
{
	this.id = id;

	this.button = document.getElementById(this.id);

	YAHOO.util.Event.addListener(this.button, 'click',
		SwatConfirmationButton.clickHandler, this);
}

SwatConfirmationButton.clickHandler = function(event, object)
{
	var confirmed = window.confirm(object.message);

	if (!confirmed) {
		YAHOO.util.Event.preventDefault(event);
	}
}

SwatConfirmationButton.prototype.setMessage = function(message)
{
	this.message = message;
}
