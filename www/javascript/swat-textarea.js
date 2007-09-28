/**
 * A resizeable textarea widget
 *
 * @package   Swat
 * @copyright 2007 silverorange
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
	var textarea = document.getElementById(this.id);
	var width = textarea.offsetWidth;

	var div = document.createElement('div');

	YAHOO.util.Dom.addClass(div, 'swat-textarea-resize-handle');
	div.style.width = width + 'px';
	div.style.height = SwatTextarea.resize_handle_height + 'px';
	div.style.fontSize = '0'; // for IE6 height

	div._textarea = textarea;

	textarea.parentNode.appendChild(div);

	YAHOO.util.Event.addListener(div, 'mousedown',
		SwatTextarea_mousedownEventHandler);
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
// {{{ function SwatTextarea_mousedownEventHandler()

/**
 * Handles mousedown events for resize handles
 *
 * @param DOMEvent event the event to handle.
 *
 * @return boolean false
 */
function SwatTextarea_mousedownEventHandler(event)
{
	// prevent text selection
	YAHOO.util.Event.preventDefault(event);

	// only allow left click to do things
	var is_webkit = (/AppleWebKit|Konqueror|KHTML/gi).test(navigator.userAgent);
	var is_ie = (navigator.userAgent.indexOf('MSIE') != -1);
	if ((is_ie && (event.button & 1) != 1) ||
		(!is_ie && !is_webkit && event.button != 0))
		return false;

	SwatTextarea.dragging_item = this;
	SwatTextarea.dragging_mouse_origin_y =
		YAHOO.util.Event.getPageY(event);

	var textarea = this._textarea;
	var height = parseInt(YAHOO.util.Dom.getStyle(textarea, 'height'));
	if (height) {
		SwatTextarea.dragging_origin_height = height;
	} else {
		// get original height for IE6
		SwatTextarea.dragging_origin_height = textarea.clientHeight;
	}

	YAHOO.util.Event.addListener(document, 'mousemove',
		SwatTextarea_mousemoveEventHandler);

	YAHOO.util.Event.addListener(document, 'mouseup',
		SwatTextarea_mouseupEventHandler);
}

// }}}
// {{{ function SwatTextarea_mousemoveEventHandler()

/**
 * Handles mouse movement when dragging a resize bar
 *
 * Updates the height of the associated textarea control.
 *
 * @param DOMEvent event the event that triggered this function.
 *
 * @return boolean false.
 */
function SwatTextarea_mousemoveEventHandler(event)
{
	var resize_handle = SwatTextarea.dragging_item;
	var textarea = resize_handle._textarea;

	var delta = YAHOO.util.Event.getPageY(event) -
		SwatTextarea.dragging_mouse_origin_y;

	var height = SwatTextarea.dragging_origin_height + delta;
	if (height >= SwatTextarea.min_height)
		textarea.style.height = height + 'px';

	return false;
}

// }}}
// {{{ function SwatTextarea_mouseupEventHandler()

/**
 * Handles mouseup events when dragging a resize bar
 *
 * Stops dragging.
 *
 * @param DOMEvent event the event that triggered this function.
 *
 * @return boolean false.
 */
function SwatTextarea_mouseupEventHandler(event)
{
	// only allow left click to do things
	var is_webkit = (/AppleWebKit|Konqueror|KHTML/gi).test(navigator.userAgent);
	var is_ie = (navigator.userAgent.indexOf('MSIE') != -1);
	if ((is_ie && (event.button & 1) != 1) ||
		(!is_ie && !is_webkit && event.button != 0))
		return false;

	YAHOO.util.Event.removeListener(document, 'mousemove',
		SwatTextarea_mousemoveEventHandler);

	YAHOO.util.Event.removeListener(document, 'mouseup',
		SwatTextarea_mouseupEventHandler);

	SwatTextarea.dragging_item = null;
	SwatTextarea.dragging_mouse_origin_y = null;
	SwatTextarea.dragging_origin_height = null;

	return false;
}

// }}}
