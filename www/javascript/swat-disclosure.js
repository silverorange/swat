export defaukt class SwatDisclosure {
	constructor(id, open) {
		this.id = id;
		this.div = document.getElementById(id);
		this.input = document.getElementById(id + '_input');
		this.animate_div = this.getAnimateDiv();

		// get initial state
		if (this.input.value.length) {
			// remembered state from post values
			this.opened = (this.input.value == 'opened');
		} else {
			// initial display
			this.opened = open;
		}

		// prevent closing during opening animation and vice versa
		this.semaphore = false;

		YAHOO.util.Event.onDOMReady(this.init, this, true);
	}

	init() {
		this.drawDisclosureLink();
		this.drawPeekabooFix();

		// set initial display state
		if (this.opened) {
			this.open();
		} else {
			this.close();
		}
	}

	toggle() {
		if (this.opened) {
			this.closeWithAnimation();
		} else {
			this.openWithAnimation();
		}
	}

	getSpan() {
		return this.div.firstChild;
	}

	getAnimateDiv() {
		return this.div.firstChild.nextSibling.nextSibling.firstChild;
	}

	drawPeekabooFix() {
		var container = document.getElementById(this.id);
		if (container.currentStyle &&
			typeof container.currentStyle.hasLayout !== 'undefined'
		) {
			/*
			 * This fix is needed for IE6/7 and fixes display of relative
			 * positioned elements below this disclosure during and after
			 * animations.
			 */

			var empty_div = document.createElement('div');
			var peekaboo_div = document.createElement('div');
			peekaboo_div.style.height = '0';
			peekaboo_div.style.margin = '0';
			peekaboo_div.style.padding = '0';
			peekaboo_div.style.border = 'none';
			peekaboo_div.appendChild(empty_div);

			if (container.nextSibling) {
				container.parentNode.insertBefore(
					peekaboo_div,
					container.nextSibling
				);
			} else {
				container.parentNode.appendChild(peekaboo_div);
			}
		}
	}

	drawDisclosureLink() {
		var text;
		var span = this.getSpan();
		if (span.firstChild && span.firstChild.nodeType === 3) {
			text = document.createTextNode(span.firstChild.nodeValue);
		} else {
			text = document.createTextNode('');
		}

		this.anchor = document.createElement('a');
		this.anchor.href = '#';
		if (this.opened) {
			YAHOO.util.Dom.addClass(
				this.anchor,
				'swat-disclosure-anchor-opened'
			);
		} else {
			YAHOO.util.Dom.addClass(
				this.anchor,
				'swat-disclosure-anchor-closed'
			);
		}

		YAHOO.util.Event.addListener(this.anchor, 'click',
			function(e) {
				YAHOO.util.Event.preventDefault(e);
				this.toggle();
			}, this, true);

		this.anchor.appendChild(text);

		span.parentNode.replaceChild(this.anchor, span);
	}

	close() {
		YAHOO.util.Dom.removeClass(this.div, 'swat-disclosure-control-opened');
		YAHOO.util.Dom.addClass(this.div, 'swat-disclosure-control-closed');

		YAHOO.util.Dom.removeClass(
			this.anchor,
			'swat-disclosure-anchor-opened'
		);
		YAHOO.util.Dom.addClass(this.anchor, 'swat-disclosure-anchor-closed');

		this.semaphore = false;

		this.input.value = 'closed';

		this.opened = false;
	}

	closeWithAnimation() {
		if (this.semaphore) {
			return;
		}

		YAHOO.util.Dom.removeClass(
			this.anchor,
			'swat-disclosure-anchor-opened'
		);
		YAHOO.util.Dom.addClass(this.anchor, 'swat-disclosure-anchor-closed');

		this.animate_div.style.overflow = 'hidden';
		this.animate_div.style.height = 'auto';
		var attributes = { height: { to: 0 } };
		var animation = new YAHOO.util.Anim(
			this.animate_div,
			attributes,
			0.25,
			YAHOO.util.Easing.easeOut
		);

		this.semaphore = true;
		animation.onComplete.subscribe(this.handleClose, this, true);
		animation.animate();

		this.input.value = 'closed';
		this.opened = false;
	}

	open() {
		YAHOO.util.Dom.removeClass(this.div, 'swat-disclosure-control-closed');
		YAHOO.util.Dom.addClass(this.div, 'swat-disclosure-control-opened');

		YAHOO.util.Dom.removeClass(
			this.anchor,
			'swat-disclosure-anchor-closed'
		);
		YAHOO.util.Dom.addClass(this.anchor, 'swat-disclosure-anchor-opened');

		this.semaphore = false;

		this.input.value = 'opened';
		this.opened = true;
	}

	openWithAnimation() {
		if (this.semaphore) {
			return;
		}

		YAHOO.util.Dom.removeClass(this.div, 'swat-disclosure-control-closed');
		YAHOO.util.Dom.addClass(this.div, 'swat-disclosure-control-opened');

		YAHOO.util.Dom.removeClass(
			this.anchor,
			'swat-disclosure-anchor-closed'
		);
		YAHOO.util.Dom.addClass(this.anchor, 'swat-disclosure-anchor-opened');

		// get display height
		this.animate_div.parentNode.style.overflow = 'hidden';
		this.animate_div.parentNode.style.height = '0';
		this.animate_div.style.visibility = 'hidden';
		this.animate_div.style.overflow = 'hidden';
		this.animate_div.style.display = 'block';
		this.animate_div.style.height = 'auto';
		var height = this.animate_div.offsetHeight;
		this.animate_div.style.height = '0';
		this.animate_div.style.visibility = 'visible';
		this.animate_div.parentNode.style.height = '';
		this.animate_div.parentNode.style.overflow = 'visible';

		var attributes = { height: { to: height, from: 0 } };
		var animation = new YAHOO.util.Anim(
			this.animate_div,
			attributes,
			0.5,
			YAHOO.util.Easing.easeOut
		);

		this.semaphore = true;
		animation.onComplete.subscribe(this.handleOpen, this, true);
		animation.animate();

		this.input.value = 'opened';
		this.opened = true;
	}

	handleClose() {
		YAHOO.util.Dom.removeClass(this.div, 'swat-disclosure-control-opened');
		YAHOO.util.Dom.addClass(this.div, 'swat-disclosure-control-closed');

		this.semaphore = false;
	}

	handleOpen() {
		// allow font resizing to work again
		this.animate_div.style.height = 'auto';

		// re-set overflow to visible for styles that might depend on it
		this.animate_div.style.overflow = 'visible';

		this.semaphore = false;
	}
}
