import SwatZIndexManager from './swat-z-index-manager';

/**
 * Abstract overlay widget
 *
 * @copyright 2005-2016 silverorange
 */
class SwatAbstractOverlay {
	// {{{ constructor()

	/**
	 * Creates an abstract overlay widget
	 *
	 * @param string id
	 */
	constructor(id) {
		this.id = id;
		this.container = document.getElementById(this.id);
		this.value_field = document.getElementById(this.id + '_value');

		this.is_open = false;
		this.is_drawn = false;

		// list of select elements to hide for IE6
		this.select_elements = [];

		YAHOO.util.Event.onDOMReady(this.init, this, true);
	}

	// }}}
	// {{{ init()

	init() {
		this.draw();
		this.drawCloseDiv();
		this.createOverlay();
	}

	// }}}
	// {{{ draw()

	draw() {
		this.overlay_content = document.createElement('div');
		this.overlay_content.id = this.id + '_overlay';
		YAHOO.util.Dom.addClass(this.overlay_content, 'swat-overlay');
		this.overlay_content.style.display = 'none';

		this.overlay_content.appendChild(this.getHeader());
		this.overlay_content.appendChild(this.getBody());
		this.overlay_content.appendChild(this.getFooter());

		this.toggle_button = this.getToggleButton();
		this.toggle_button.appendChild(this.overlay_content);

		this.container.appendChild(this.toggle_button);
	}

	// }}}
	// {{{ getToggleButton()

	getToggleButton() {
		var toggle_button = document.createElement('button');
		YAHOO.util.Dom.addClass(toggle_button, 'swat-overlay-toggle-button');

		// the type property is readonly in IE so use setAttribute() here
		toggle_button.setAttribute('type', 'button');

		YAHOO.util.Event.on(toggle_button, 'click', this.toggle, this, true);

		return toggle_button;
	}

	// }}}
	// {{{ getHeader()

	getHeader() {
		var header = document.createElement('div');
		YAHOO.util.Dom.addClass(header, 'hd');
		header.appendChild(this.getCloseLink());
		return header;
	}

	// }}}
	// {{{ getBody()

	getBody() {
		var body = document.createElement('div');
		YAHOO.util.Dom.addClass(body, 'bd');
		return body;
	}

	// }}}
	// {{{ getFooter()

	getFooter() {
		var footer = document.createElement('div');
		YAHOO.util.Dom.addClass(footer, 'ft');
		return footer;
	}

	// }}}
	// {{{ getCloseLink()

	getCloseLink() {
		var close_link = document.createElement('a');

		close_link.className = 'swat-overlay-close-link';
		close_link.href = '#close';
		close_link.appendChild(
			document.createTextNode(
				SwatAbstractOverlay.close_text
			)
		);
		YAHOO.util.Event.on(
			close_link,
			'click',
			this.handleCloseLink,
			this,
			true
		);

		return close_link;
	}

	// }}}
	// {{{ createOverlay()

	/**
	 * Creates overlay widget when toggle button has been drawn
	 */
	createOverlay(event) {
		this.overlay = new YAHOO.widget.Overlay(
			this.id + '_overlay',
			{ visible: false, constraintoviewport: true }
		);

		this.overlay.body.appendChild(this.getBodyContent());

		this.overlay.render(this.container);
		this.overlay_content.style.display = 'block';
		this.is_drawn = true;
	}

	// }}}
	// {{{ close()

	/**
	 * Closes this overlay
	 */
	close() {
		this.hideCloseDiv();

		this.overlay.hide();
		SwatZIndexManager.lowerElement(this.overlay_content);
		this.is_open = false;

		this.removeKeyPressHandler();
	}

	// }}}
	// {{{ open()

	/**
	 * Opens this overlay
	 */
	open() {
		this.showCloseDiv();

		this.overlay.cfg.setProperty('context', this.getOverlayContext());

		this.overlay.show();
		this.is_open = true;

		SwatZIndexManager.raiseElement(this.overlay_content);

		this.addKeyPressHandler();
	}

	// }}}
	// {{{ getOverlayContext()

	/**
	 * Get the context for positioning the overlay
	 */
	getOverlayContext() {
		return [this.toggle_button, 'tl', 'bl'];
	}

	// }}}
	// {{{ getBodyContent

	/**
	 * Draws this overlay
	 */
	getBodyContent() {
		return document.createElement('div');
	}

	// }}}
	// {{{ toggle()

	toggle() {
		if (this.is_open) {
			this.close();
		} else {
			this.open();
		}
	}

	// }}}
	// {{{ drawCloseDiv()

	drawCloseDiv() {
		this.close_div = document.createElement('div');

		this.close_div.className = 'swat-overlay-close-div';
		this.close_div.style.display = 'none';

		YAHOO.util.Event.on(this.close_div, 'click', this.close, this, true);

		this.container.appendChild(this.close_div);
	}

	// }}}
	// {{{ showCloseDiv()

	showCloseDiv() {
		if (YAHOO.env.ua.ie == 6) {
			this.select_elements = document.getElementsByTagName('select');
			for (var i = 0; i < this.select_elements.length; i++) {
				this.select_elements[i].style._visibility =
					this.select_elements[i].style.visibility;

				this.select_elements[i].style.visibility = 'hidden';
			}
		}

		this.close_div.style.height = YAHOO.util.Dom.getDocumentHeight() + 'px';
		this.close_div.style.display = 'block';
		SwatZIndexManager.raiseElement(this.close_div);
	}

	// }}}
	// {{{ hideCloseDiv()

	hideCloseDiv() {
		SwatZIndexManager.lowerElement(this.close_div);
		this.close_div.style.display = 'none';
		if (YAHOO.env.ua.ie == 6) {
			for (var i = 0; i < this.select_elements.length; i++) {
				this.select_elements[i].style.visibility =
					this.select_elements[i].style._visibility;
			}
		}
	}

	// }}}
	// {{{ handleKeyPress()

	handleKeyPress(e) {
		YAHOO.util.Event.preventDefault(e);

		// close preview on backspace or escape
		if (e.keyCode === 8 || e.keyCode === 27) {
			this.close();
		}
	}

	// }}}
	// {{{ handleKeyPress()

	handleKeyPress(e) {
		// close preview on escape or enter key
		if (e.keyCode === 27 || e.keyCode === 13) {
			YAHOO.util.Event.preventDefault(e);
			this.close();
		}
	}

	// }}}
	// {{{ addKeyPressHandler()

	addKeyPressHandler() {
		YAHOO.util.Event.on(
			document,
			'keypress',
			this.handleKeyPress,
			this,
			true
		);
	}

	// }}}
	// {{{ removeKeyPressHandler()

	removeKeyPressHandler() {
		YAHOO.util.Event.removeListener(
			document,
			'keypress',
			this.handleKeyPress,
			this,
			true
		);
	}

	// }}}
	// {{{ handleCloseLink()

	handleCloseLink(e) {
		YAHOO.util.Event.preventDefault(e);
		this.close();
	}

	// }}}
}

SwatAbstractOverlay.close_text = 'Close';

export default SwatAbstractOverlay;
