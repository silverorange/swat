function SwatMessageDisplay(id, hideable_messages)
{
	this.id = id;

	// draw dismiss links
	for (var i = 0; i < hideable_messages.length; i++) {
		var message_container = document.getElementById(
			this.id + '_' + hideable_messages[i]).firstChild;

		message_container.innerHTML = '<a href="javascript:' + this.id +
			'_obj.hideMessage(' + hideable_messages[i] + ');" ' +
			'class="swat-message-display-dismiss-link" ' +
			'title="' + SwatMessageDisplay.close_text + '">' +
			SwatMessageDisplay.close_text + '</a>' +
			message_container.innerHTML;
	}
}

SwatMessageDisplay.close_text = 'Dismiss message';

/**
 * Hides a message in this display
 *
 * Uses the self-healing transition pattern described at
 * {@link http://developer.yahoo.com/ypatterns/pattern.php?pattern=selfhealing}.
 *
 * @param Number message_index the message to hide from this list.
 */
SwatMessageDisplay.prototype.hideMessage = function(message_index)
{
	var id = this.id + '_' + message_index;
	var message = document.getElementById(id);

	if (message !== null) {
		// fade out message
		var message_animation = new YAHOO.util.Anim(
			message, { opacity: { to: 0 } },
			0.3, YAHOO.util.Easing.easeOut);

		// after fading out, shrink the empty space away
		message_animation.onComplete.subscribe(
			SwatMessageDisplay.shrinkMessage, message);

		message_animation.animate();
	}
}

SwatMessageDisplay.shrinkMessage = function(type, args, message)
{
	var duration = 0.3;
	var easing = YAHOO.util.Easing.easingNone;

	var attributes = {
		height: { to: 0 },
		marginBottom: { to: 0 }
	}; 

	// collapse margins
	if (message.nextSibling) {
		// shrink top margin of next message in message display
		var next_message_animation = new YAHOO.util.Anim(
			message.nextSibling, { marginTop: { to: 0 } },
			duration, easing);

		next_message_animation.animate();
	} else {
		// shrink top margin of element directly below message display

		// find first element node
		var script_node = message.parentNode.nextSibling;
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
	var message_animation = new YAHOO.util.Anim(
		message, attributes,
		duration, easing);

	message_animation.onComplete.subscribe(
		SwatMessageDisplay.removeMessage, message);

	message_animation.animate();
}

SwatMessageDisplay.removeMessage = function(type, args, message)
{
	var removed_node = message.parentNode.removeChild(message);
	delete removed_node;
}
