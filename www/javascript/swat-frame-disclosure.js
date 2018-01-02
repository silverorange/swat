import SwatDisclosure from './swat-disclosure';

import '../styles/swat-frame-disclosure.css';

export default class SwatFrameDisclosure extends SwatDisclosure {
	getSpan() {
		return this.div.firstChild.firstChild;
	}

	close() {
		super.close();

		this.div.classList.remove('swat-frame-disclosure-control-opened');
		this.div.classList.add('swat-frame-disclosure-control-closed');
	}

	handleClose() {
		super.handleClose();

		this.div.classList.remove('swat-frame-disclosure-control-opened');
		this.div.classList.add('swat-frame-disclosure-control-closed');
	}

	open() {
		super.open();

		this.div.classList.remove('swat-frame-disclosure-control-closed');
		this.div.classList.add('swat-frame-disclosure-control-opened');
	}

	openWithAnimation() {
		if (this.semaphore) {
			return;
		}

		super.openWithAnimation();

		this.div.classList.remove('swat-frame-disclosure-control-closed');
		this.div.classList.add('swat-frame-disclosure-control-opened');
	}
}
