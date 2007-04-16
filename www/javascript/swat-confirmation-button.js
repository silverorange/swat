function SwatConfirmationButton(id, show_processing_throbber)
{
	this.confirmation_message = '';
	SwatConfirmationButton.superclass.constructor.call(this, id,
		show_processing_throbber);
}

YAHOO.lang.extend(SwatConfirmationButton, SwatButton, {

handleClick: function()
{
	var confirmed = window.confirm(this.confirmation_message);
	if (confirmed) {
		SwatConfirmationButton.superclass.handleClick.call(this, event);
	} else {
		YAHOO.util.Event.preventDefault(event);
	}
}

});

SwatConfirmationButton.prototype.setConfirmationMessage = function(message)
{
	this.confirmation_message = message;
}
