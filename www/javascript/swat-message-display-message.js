import { Dom } from '../../../yui/www/dom/dom';
import { Event } from '../../../yui/www/event/event';
import { Anim, Easing } from '../../../yui/www/animation/animation';

import '../styles/swat-message.css';

class SwatMessageDisplayMessage {
	/**
	 * A message in a message display
	 *
	 * @param Number message_index the message to hide from this list.
	 */
	constructor(message_display_id, message_index) {
		this.id = message_display_id + '_' + message_index;
		this.message_div = document.getElementById(this.id);
		this.drawDismissLink();
	}

	drawDismissLink() {
		var text = document.createTextNode(
			SwatMessageDisplayMessage.close_text
		);

		var anchor = document.createElement('a');
		anchor.href = '#';
		anchor.title = SwatMessageDisplayMessage.close_text;
		Dom.addClass(anchor, 'swat-message-display-dismiss-link');
		Event.addListener(
			anchor,
			'click',
			function(e, message) {
				Event.preventDefault(e);
				message.hide();
			},
			this
		);

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
	hide() {
		if (this.message_div !== null) {
			// fade out message
			var fade_animation = new Anim(
				this.message_div,
				{ opacity: { to: 0 } },
				SwatMessageDisplayMessage.fade_duration,
				Easing.easingOut
			);

			// after fading out, shrink the empty space away
			fade_animation.onComplete.subscribe(this.shrink, this, true);
			fade_animation.animate();
		}
	}

	shrink() {
		var duration = SwatMessageDisplayMessage.shrink_duration;
		var easing = Easing.easeInStrong;

		var attributes = {
			height: { to: 0 },
			marginBottom: { to: 0 }
		};

		// collapse margins
		if (this.message_div.nextSibling) {
			// shrink top margin of next message in message display
			var next_message_animation = new Anim(
				this.message_div.nextSibling,
				{ marginTop: { to: 0 } },
				duration,
				easing
			);
			next_message_animation.animate();
		} else {
			// shrink top margin of element directly below message display

			// find first element node
			var script_node = this.message_div.parentNode.nextSibling;
			var node = script_node.nextSibling;
			while (node && node.nodeType != 1)
				node = node.nextSibling;

			if (node) {
				var previous_message_animation = new Anim(
					node,
					{ marginTop: { to: 0 } },
					duration,
					easing
				);
				previous_message_animation.animate();
			}
		}

		// if this is the last message in the display, shrink the message display
		// top margin to zero.
		if (this.message_div.parentNode.childNodes.length === 1) {

			// collapse top margin of last message
			attributes.marginTop = { to: 0 };

			var message_display_animation = new Anim(
				this.message_div.parentNode,
				{ marginTop: { to: 0 } },
				duration,
				easing
			);
			message_display_animation.animate();
		}

		// disappear this message
		var shrink_animation = new Anim(
			this.message_div,
			attributes,
			duration,
			easing
		);
		shrink_animation.onComplete.subscribe(this.remove, this, true);
		shrink_animation.animate();
	}

	remove() {
		Event.purgeElement(this.message_div, true);

		this.message_div.parentNode.removeChild(
			this.message_div
		);
	};
}

SwatMessageDisplayMessage.close_text = 'Dismiss message';
SwatMessageDisplayMessage.fade_duration = 0.3;
SwatMessageDisplayMessage.shrink_duration = 0.3;

export default SwatMessageDisplayMessage;
