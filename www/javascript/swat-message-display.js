import SwatMessageDisplayMessage from './swat-message-display-message';

export default class SwatMessageDisplay {
	constructor(id, hideable_messages) {
		this.id = id;
		this.messages = [];

		// create message objects for this display
		for (var i = 0; i < hideable_messages.length; i++) {
			var message = new SwatMessageDisplayMessage(
				this.id,
				hideable_messages[i]
			);
			this.messages[i] = message;
		}
	}

	getMessage(index) {
		if (this.messages[index]) {
			return this.messages[index];
		}
		return false;
	}
}
