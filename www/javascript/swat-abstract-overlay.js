/**
 * Abstract overlay widget
 *
 * @copyright 2005-2016 silverorange
 */

// {{{ function SwatAbstractOverlay()

/**
 * Creates an abstract overlay widget
 *
 * @param string id
 */
function SwatAbstractOverlay(id)
{
	this.id = id;
	this.container = document.getElementById(this.id);
	this.value_field = document.getElementById(this.id + '_value');

	this.is_open = false;
	this.is_drawn = false;

	// list of select elements to hide for IE6
	this.select_elements = [];

	YAHOO.util.Event.onDOMReady(this.init, this, true);
}

SwatAbstractOverlay.close_text = 'Close';

// }}}
// {{{ SwatAbstractOverlay.prototype.init

SwatAbstractOverlay.prototype.init = function()
{
	this.draw();
	this.drawCloseDiv();
	this.createOverlay();
};

// }}}
// {{{ SwatAbstractOverlay.prototype.draw

SwatAbstractOverlay.prototype.draw = function()
{
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
};

// }}}
// {{{ SwatAbstractOverlay.prototype.getToggleButton

SwatAbstractOverlay.prototype.getToggleButton = function()
{
	var toggle_button = document.createElement('button');
	YAHOO.util.Dom.addClass(toggle_button, 'swat-overlay-toggle-button');

	// the type property is readonly in IE so use setAttribute() here
	toggle_button.setAttribute('type', 'button');

	YAHOO.util.Event.on(toggle_button, 'click', this.toggle, this, true);

	return toggle_button;
};

// }}}
// {{{ SwatAbstractOverlay.prototype.getHeader

SwatAbstractOverlay.prototype.getHeader = function()
{
	var header = document.createElement('div');
	YAHOO.util.Dom.addClass(header, 'hd');
	header.appendChild(this.getCloseLink());
	return header;
};

// }}}
// {{{ SwatAbstractOverlay.prototype.getBody

SwatAbstractOverlay.prototype.getBody = function()
{
	var body = document.createElement('div');
	YAHOO.util.Dom.addClass(body, 'bd');
	return body;
};

// }}}
// {{{ SwatAbstractOverlay.prototype.getFooter

SwatAbstractOverlay.prototype.getFooter = function()
{
	var footer = document.createElement('div');
	YAHOO.util.Dom.addClass(footer, 'ft');
	return footer;
};

// }}}
// {{{ SwatAbstractOverlay.prototype.getCloseLink

SwatAbstractOverlay.prototype.getCloseLink = function()
{
	var close_link = document.createElement('a');
	close_link.className = 'swat-overlay-close-link';
	close_link.href = '#close';
	close_link.appendChild(document.createTextNode(
		SwatAbstractOverlay.close_text));

	YAHOO.util.Event.on(close_link, 'click', this.handleCloseLink, this, true);

	return close_link;
};

// }}}
// {{{ SwatAbstractOverlay.prototype.createOverlay

/**
 * Creates overlay widget when toggle button has been drawn
 */
SwatAbstractOverlay.prototype.createOverlay = function(event)
{
	this.overlay = new YAHOO.widget.Overlay(this.id + '_overlay',
		{ visible: false, constraintoviewport: true });

	this.overlay.body.appendChild(this.getBodyContent());

	this.overlay.render(this.container);
	this.overlay_content.style.display = 'block';
	this.is_drawn = true;
};

// }}}
// {{{ SwatAbstractOverlay.prototype.close

/**
 * Closes this overlay
 */
SwatAbstractOverlay.prototype.close = function()
{
	this.hideCloseDiv();

	this.overlay.hide();
	SwatZIndexManager.lowerElement(this.overlay_content);
	this.is_open = false;

	this.removeKeyPressHandler();
};

// }}}
// {{{ SwatAbstractOverlay.prototype.open

/**
 * Opens this overlay
 */
SwatAbstractOverlay.prototype.open = function()
{
	this.showCloseDiv();

	this.overlay.cfg.setProperty('context', this.getOverlayContext());

	this.overlay.show();
	this.is_open = true;

	SwatZIndexManager.raiseElement(this.overlay_content);

	this.addKeyPressHandler();
};

// }}}
// {{{ SwatAbstractOverlay.prototype.getOverlayContext

/**
 * Get the context for positioning the overlay
 */
SwatAbstractOverlay.prototype.getOverlayContext = function()
{
	return [this.toggle_button, 'tl', 'bl'];
};

// }}}
// {{{ SwatAbstractOverlay.prototype.getBodyContent

/**
 * Draws this overlay
 */
SwatAbstractOverlay.prototype.getBodyContent = function()
{
	return document.createElement('div');
};

// }}}
// {{{ SwatAbstractOverlay.prototype.toggle

SwatAbstractOverlay.prototype.toggle = function()
{
	if (this.is_open)
		this.close();
	else
		this.open();
};

// }}}
// {{{ SwatAbstractOverlay.prototype.drawCloseDiv

SwatAbstractOverlay.prototype.drawCloseDiv = function()
{
	this.close_div = document.createElement('div');

	this.close_div.className = 'swat-overlay-close-div';
	this.close_div.style.display = 'none';

	YAHOO.util.Event.on(this.close_div, 'click', this.close, this, true);

	this.container.appendChild(this.close_div);
};

// }}}
// {{{ SwatAbstractOverlay.prototype.showCloseDiv

SwatAbstractOverlay.prototype.showCloseDiv = function()
{
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
};

// }}}
// {{{ SwatAbstractOverlay.prototype.hideCloseDiv

SwatAbstractOverlay.prototype.hideCloseDiv = function()
{
	SwatZIndexManager.lowerElement(this.close_div);
	this.close_div.style.display = 'none';
	if (YAHOO.env.ua.ie == 6) {
		for (var i = 0; i < this.select_elements.length; i++) {
			this.select_elements[i].style.visibility =
				this.select_elements[i].style._visibility;
		}
	}
};

// }}}
// {{{ SwatAbstractOverlay.prototype.handleKeyPress

SwatAbstractOverlay.prototype.handleKeyPress = function(e)
{
	YAHOO.util.Event.preventDefault(e);

	// close preview on backspace or escape
	if (e.keyCode == 8 || e.keyCode == 27)
		this.close();
};

// }}}
// {{{ SwatAbstractOverlay.prototype.handleKeyPress

SwatAbstractOverlay.prototype.handleKeyPress = function(e)
{
	// close preview on escape or enter key
	if (e.keyCode == 27 || e.keyCode == 13) {
		YAHOO.util.Event.preventDefault(e);
		this.close();
	}
};

// }}}
// {{{ SwatAbstractOverlay.prototype.addKeyPressHandler

SwatAbstractOverlay.prototype.addKeyPressHandler = function()
{
	YAHOO.util.Event.on(document, 'keypress',
		this.handleKeyPress, this, true);
};

// }}}
// {{{ SwatAbstractOverlay.prototype.removeKeyPressHandler

SwatAbstractOverlay.prototype.removeKeyPressHandler = function()
{
	YAHOO.util.Event.removeListener(document, 'keypress',
		this.handleKeyPress, this, true);
};

// }}}
// {{{ SwatAbstractOverlay.prototype.handleCloseLink

SwatAbstractOverlay.prototype.handleCloseLink = function(e)
{
	YAHOO.util.Event.preventDefault(e);
	this.close();
};

// }}}
