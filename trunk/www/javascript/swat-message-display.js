function SwatMessageDisplay(id, count)
{
	this.id = id;
}

SwatMessageDisplay.prototype.hideMessage = function(message_index)
{
	var id = this.id + '_' + message_index;
	var message = document.getElementById(id);

	if (message !== null) {
		message.style.display = 'none';
	}
}
