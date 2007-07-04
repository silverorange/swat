var count = 0;

function SwatSearchEntry(id)
{
	this.id = id;
	this.button = document.getElementById(this.id);

	YAHOO.util.Event.addListener(this.button, 'click', this.handleClick);
}

SwatSearchEntry.prototype.handleClick = function(event)
{
	if (count == 0)
	{		
		this.value = null;
		count = count + 1;
	}
}
