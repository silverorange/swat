function SwatMessageDisplay(id, count)
{
	this.id = id;
}

SwatMessageDisplay.prototype.hideMessage = function(message_index)
{
	var id = this.id + '_' + message_index;
	var message = document.getElementById(id);

	if (message !== null) {
		var houdini_div = document.createElement('div');
		message.parentNode.insertBefore(houdini_div, message);
		houdini_div.appendChild(message);
		houdini_div.style.overflow = 'hidden';
		var attributes = { 
			height: { to: 0 }, 
		}; 
		var myAnim = new YAHOO.util.Anim(houdini_div, attributes, 0.25); 
		myAnim.animate();
		myAnim.onComplete.subscribe(SwatMessageDisplay.removeMessage,
			houdini_div);
	}
}

SwatMessageDisplay.removeMessage = function(type, args, houdini_div)
{
	var removed_node = houdini_div.parentNode.removeChild(houdini_div);
	removed_node = null;
}
