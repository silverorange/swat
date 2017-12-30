const SwatDisclosure = require('./swat-disclosure');

class SwatFrameDisclosure extends SwatDisclosure {
	getSpan() {
		return this.div.firstChild.firstChild;
	}

	close() {
		super.close();

		YAHOO.util.Dom.removeClass(
			this.div,
			'swat-frame-disclosure-control-opened'
		);
		YAHOO.util.Dom.addClass(
			this.div,
			'swat-frame-disclosure-control-closed'
		);
	}

	handleClose() {
		super.handleClose();

		YAHOO.util.Dom.removeClass(
			this.div,
			'swat-frame-disclosure-control-opened'
		);
		YAHOO.util.Dom.addClass(
			this.div,
			'swat-frame-disclosure-control-closed'
		);
	}

	open() {
		super.open();

		YAHOO.util.Dom.removeClass(
			this.div,
			'swat-frame-disclosure-control-closed'
		);
		YAHOO.util.Dom.addClass(
			this.div,
			'swat-frame-disclosure-control-opened'
		);
	}

	openWithAnimation() {
		if (this.semaphore) {
			return;
		}

		super.openWithAnimation();

		YAHOO.util.Dom.removeClass(
			this.div,
			'swat-frame-disclosure-control-closed'
		);
		YAHOO.util.Dom.addClass(
			this.div,
			'swat-frame-disclosure-control-opened'
		);
	}
}

module.exports = SwatFrameDisclosure;
