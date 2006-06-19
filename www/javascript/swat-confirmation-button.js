function SwatConfirmationButton(id)
{
	var self = this;

	this.id = id;

	this.button = document.getElementById(this.id);

	var is_ie = (this.button.addEventListener) ? false : true;

	if (is_ie)
		this.button.attachEvent("onclick", eventHandler, false);
	else
		this.button.addEventListener("click", eventHandler, false);

	function eventHandler(event)
	{
		var confirmed = window.confirm(self.message);

		if (!confirmed) {
			if (event.preventDefault)
				event.preventDefault();
			else
				event.returnValue = false; //IE
		}
	}
}

SwatConfirmationButton.prototype.setMessage = function(message)
{
	this.message = message;
}
