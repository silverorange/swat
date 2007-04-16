function SwatMessageDisplay(id, hideable_messages)
{
	this.id = id;
	this.messages = [];

	// create message objects for this display
	for (var i = 0; i < hideable_messages.length; i++) {
		var message = new SwatMessageDisplayMessage(this.id,
			hideable_messages[i]);
	}
}

SwatMessageDisplayMessage.close_text = 'Dismiss message';

/**
 * A message in a message display
 *
 * @param Number message_index the message to hide from this list.
 */
function SwatMessageDisplayMessage(message_display_id, message_index)
{
	this.id = message_display_id + '_' + message_index;
	this.message_div = document.getElementById(this.id);
	this.drawDismissLink();
}

SwatMessageDisplayMessage.prototype.drawDismissLink = function()
{
	var text = document.createTextNode(SwatMessageDisplayMessage.close_text);

	var anchor = document.createElement('a');
	anchor.href = '#';
	anchor.title = SwatMessageDisplayMessage.close_text;
	YAHOO.util.Dom.addClass(anchor, 'swat-message-display-dismiss-link');
	YAHOO.util.Event.addListener(anchor, 'click', this.hide, this, true);
	anchor.appendChild(text);

	var container = this.message_div.firstChild;
	container.insertBefore(anchor, container.firstChild);
}

/**
 * Hides this message
 *
 * Uses the self-healing transition pattern described at
 * {@link http://developer.yahoo.com/ypatterns/pattern.php?pattern=selfhealing}.
 */
SwatMessageDisplayMessage.prototype.hide = function()
{
	if (this.message_div !== null) {
		// fade out message
		var fade_animation = new YAHOO.util.Anim(this.message_div,
			{ opacity: { to: 0 } }, 0.3, YAHOO.util.Easing.easingOut);

		// after fading out, shrink the empty space away
		fade_animation.onComplete.subscribe(this.shrink, this, true);
		fade_animation.animate();
	}
}

SwatMessageDisplayMessage.prototype.shrink = function()
{
	var duration = 0.3;
	var easing = YAHOO.util.Easing.easeInStrong;

	var attributes = {
		height: { to: 0 },
		marginBottom: { to: 0 }
	}; 

	// collapse margins
	if (this.message_div.nextSibling) {
		// shrink top margin of next message in message display
		var next_message_animation = new YAHOO.util.Anim(
			this.message_div.nextSibling, { marginTop: { to: 0 } },
			duration, easing);

		next_message_animation.animate();
	} else {
		// shrink top margin of element directly below message display

		// find first element node
		var script_node = this.message_div.parentNode.nextSibling;
		var node = script_node.nextSibling;
		while (node && node.nodeType != 1)
			node = node.nextSibling; 

		if (node) {
			var previous_message_animation = new YAHOO.util.Anim(
				node, { marginTop: { to: 0 } }, duration, easing);

			previous_message_animation.animate();
		}
	}

	// disappear this message
	var shrink_animation = new YAHOO.util.Anim(this.message_div,
		attributes, duration, easing);

	shrink_animation.onComplete.subscribe(this.remove, this, true);
	shrink_animation.animate();
}

SwatMessageDisplayMessage.prototype.remove = function()
{
	var removed_node =
		this.message_div.parentNode.removeChild(this.message_div);

	delete removed_node;
}
