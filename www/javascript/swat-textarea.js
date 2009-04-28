/**
 * A resizeable textarea widget
 *
 * @package   Swat
 * @copyright 2007-2009 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */

// {{{ function SwatTextarea()

/**
 * Creates a new textarea object
 *
 * @param string id the unique identifier of this textarea object.
 * @param boolean resizeable whether or not this textarea is resizeable.
 */
function SwatTextarea(id, resizeable)
{
	this.id = id;

	// WebKit has a built-in text-area resizer
	var is_webkit = (/AppleWebKit/gi).test(navigator.userAgent);

	if (resizeable && !is_webkit) {
		YAHOO.util.Event.onContentReady(
			this.id, this.handleOnAvailable, this, true);
	}
}

// }}}
// {{{ handleOnAvailable()

/**
 * Sets up the resize handle when the textarea is available and loaded in the
 * DOM tree
 */
SwatTextarea.prototype.handleOnAvailable = function()
{
	this.textarea = document.getElementById(this.id);

	this.handle_div = document.createElement('div');

	var style_width = YAHOO.util.Dom.getStyle(this.textarea, 'width');

	YAHOO.util.Dom.addClass(this.handle_div, 'swat-textarea-resize-handle');

	if (style_width.indexOf('%') != -1) {
		var left_border = YAHOO.util.Dom.getStyle(this.textarea,
			'borderLeftWidth');

		var right_border = YAHOO.util.Dom.getStyle(this.textarea,
			'borderRightWidth');

		this.handle_div.style.width = style_width;
		this.handle_div.style.paddingLeft = left_border;
		this.handle_div.style.paddingRight = right_border;
	} else {
		var width = this.textarea.offsetWidth;
		this.handle_div.style.width = width + 'px';
	}

	this.handle_div.style.height = SwatTextarea.resize_handle_height + 'px';
	this.handle_div.style.fontSize = '0'; // for IE6 height

	this.handle_div._textarea = this.textarea;

	this.textarea.parentNode.appendChild(this.handle_div);

	YAHOO.util.Event.addListener(this.handle_div, 'mousedown',
		SwatTextarea.mousedownEventHandler, this.handle_div);
}

// }}}
// {{{ static properties

/**
 * Current resize handle that is being dragged.
 *
 * If no drag is taking place, this is null.
 *
 * @var DOMElement
 */
SwatTextarea.dragging_item = null;

/**
 * The absolute y-position of the mouse when dragging started
 *
 * If no drag is taking place this value is null.
 *
 * @var number
 */
SwatTextarea.dragging_mouse_origin_y = null;

/**
 * The absolute height of the textarea when dragging started
 *
 * If no drag is taking place this value is null.
 *
 * @var number
 */
SwatTextarea.dragging_origin_height = null;

/**
 * Minimum height of resized textareas in pixels
 *
 * @var number
 */
SwatTextarea.min_height = 20;

/**
 * Height of the resize handle in pixels
 *
 * @var number
 */
SwatTextarea.resize_handle_height = 7;

// }}}
// {{{ SwatTextarea.mousedownEventHandler()

/**
 * Handles mousedown events for resize handles
 *
 * @param DOMEvent   event the event to handle.
 * @param DOMElement the drag handle being grabbed.
 *
 * @return boolean false
 */
SwatTextarea.mousedownEventHandler = function(e, handle)
{
	// prevent text selection
	YAHOO.util.Event.preventDefault(e);

	// only allow left click to do things
	var is_webkit = (/AppleWebKit|Konqueror|KHTML/gi).test(navigator.userAgent);
	var is_ie = (navigator.userAgent.indexOf('MSIE') != -1);
	if ((is_ie && (e.button & 1) != 1) ||
		(!is_ie && !is_webkit && e.button != 0))
		return false;

	SwatTextarea.dragging_item = handle;
	SwatTextarea.dragging_mouse_origin_y =
		YAHOO.util.Event.getPageY(e);

	var textarea = handle._textarea;

	YAHOO.util.Dom.setStyle(textarea, 'opacity', 0.25);

	var height = parseInt(YAHOO.util.Dom.getStyle(textarea, 'height'));
	if (height) {
		SwatTextarea.dragging_origin_height = height;
	} else {
		// get original height for IE6
		SwatTextarea.dragging_origin_height = textarea.clientHeight;
	}

	YAHOO.util.Event.addListener(document, 'mousemove',
		SwatTextarea.mousemoveEventHandler, handle);

	YAHOO.util.Event.addListener(document, 'mouseup',
		SwatTextarea.mouseupEventHandler, handle);
}

// }}}
// {{{ SwatTextarea.mousemoveEventHandler()

/**
 * Handles mouse movement when dragging a resize bar
 *
 * Updates the height of the associated textarea control.
 *
 * @param DOMEvent event the event that triggered this function.
 *
 * @return boolean false.
 */
SwatTextarea.mousemoveEventHandler = function(e, handle)
{
	var resize_handle = SwatTextarea.dragging_item;
	var textarea = resize_handle._textarea;

	var delta = YAHOO.util.Event.getPageY(e) -
		SwatTextarea.dragging_mouse_origin_y;

	var height = SwatTextarea.dragging_origin_height + delta;
	if (height >= SwatTextarea.min_height)
		textarea.style.height = height + 'px';

	return false;
}

// }}}
// {{{ SwatTextarea.mouseupEventHandler()

/**
 * Handles mouseup events when dragging a resize bar
 *
 * Stops dragging.
 *
 * @param DOMEvent   event the event that triggered this function.
 * @param DOMElement the drag handle being released.
 *
 * @return boolean false.
 */
SwatTextarea.mouseupEventHandler = function(e, handle)
{
	// only allow left click to do things
	var is_webkit = (/AppleWebKit|Konqueror|KHTML/gi).test(navigator.userAgent);
	var is_ie = (navigator.userAgent.indexOf('MSIE') != -1);
	if ((is_ie && (e.button & 1) != 1) ||
		(!is_ie && !is_webkit && e.button != 0))
		return false;

	YAHOO.util.Event.removeListener(document, 'mousemove',
		SwatTextarea.mousemoveEventHandler);

	YAHOO.util.Event.removeListener(document, 'mouseup',
		SwatTextarea.mouseupEventHandler);

	SwatTextarea.dragging_item = null;
	SwatTextarea.dragging_mouse_origin_y = null;
	SwatTextarea.dragging_origin_height = null;

	YAHOO.util.Dom.setStyle(handle._textarea, 'opacity', 1);

	return false;
}

// }}}
