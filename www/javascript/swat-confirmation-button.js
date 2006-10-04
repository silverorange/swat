function SwatConfirmationButton(id)
{
	var self = this;

	this.id = id;

	this.button = document.getElementById(this.id);

	YAHOO.util.Event.addListener(this.button, 'click', eventHandler);

	function eventHandler(event)
	{
		var confirmed = window.confirm(self.message);

		if (!confirmed) {
			YAHOO.util.Event.preventDefault(event);
		}
	}
}

SwatConfirmationButton.prototype.setMessage = function(message)
{
	this.message = message;
}
