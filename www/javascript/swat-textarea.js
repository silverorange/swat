/**
 * A resizeable textarea widget
 *
 * @package   Swat
 * @copyright 2007-2011 silverorange
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

	if (resizeable) {
		YAHOO.util.Event.onContentReady(
			this.id, this.handleOnAvailable, this, true);
	}
}

// }}}
// {{{ SwatTextarea.registerPendingTextarea()

SwatTextarea.registerPendingTextarea = function(textarea)
{
	SwatTextarea.pending_textareas.push(textarea);

	if (SwatTextarea.pending_interval === null) {
		SwatTextarea.pending_interval = setInterval(
			SwatTextarea.pollPendingTextareas,
			SwatTextarea.pending_poll_interval * 1000
		);
	}
};

// }}}
// {{{ SwatTextarea.pollPendingTextareas()

SwatTextarea.pollPendingTextareas = function()
{
	for (var i = 0; i < SwatTextarea.pending_textareas.length; i++) {
		if (SwatTextarea.pending_textareas[i].textarea.offsetWidth > 0) {
			SwatTextarea.pending_textareas[i].initialize();
			SwatTextarea.pending_textareas.splice(i, 1);
			i--;
		}
	}

	if (SwatTextarea.pending_textareas.length === 0) {
		clearInterval(SwatTextarea.pending_interval)
		SwatTextarea.pending_interval = null;
	}
};

// }}}
// {{{ handleOnAvailable()

/**
 * Sets up the resize handle when the textarea is available and loaded in the
 * DOM tree
 */
SwatTextarea.prototype.handleOnAvailable = function()
{
	this.textarea = document.getElementById(this.id);

	// check if textarea already is resizable, and if so, don't add resize
	// handle.
	if (SwatTextarea.supports_resize) {
		var resize = YAHOO.util.Dom.getStyle(this.textarea, 'resize');
		if (resize == 'both' || resize == 'vertical') {
			return;
		}
	}

	this.handle_div = document.createElement('div');
	this.handle_div.className = 'swat-textarea-resize-handle';
	this.handle_div._textarea = this.textarea;

	YAHOO.util.Event.addListener(this.handle_div, 'mousedown',
		SwatTextarea.mousedownEventHandler, this.handle_div);

	this.textarea.parentNode.appendChild(this.handle_div);

	// if textarea is not currently visible, delay initilization
	if (this.textarea.offsetWidth == 0) {
		SwatTextarea.registerPendingTextarea(this);
		return;
	}

	this.initialize();
};

// }}}
// {{{ initialize()

SwatTextarea.prototype.initialize = function()
{
	var style_width = YAHOO.util.Dom.getStyle(this.textarea, 'width');

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
};

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
 * @var Number
 */
SwatTextarea.dragging_mouse_origin_y = null;

/**
 * The absolute height of the textarea when dragging started
 *
 * If no drag is taking place this value is null.
 *
 * @var Number
 */
SwatTextarea.dragging_origin_height = null;

/**
 * Minimum height of resized textareas in pixels
 *
 * @var Number
 */
SwatTextarea.min_height = 20;

/**
 * Height of the resize handle in pixels
 *
 * @var Number
 */
SwatTextarea.resize_handle_height = 7;

/**
 * Textarea objects that have not yet been initialized
 *
 * @var Array
 */
SwatTextarea.pending_textareas = [];

/**
 * Window interval used to check if pending textareas can be initialized
 *
 * @var Object
 */
SwatTextarea.pending_interval = null;

/**
 * Polling period in seconds for checking if pending textareas are ready
 * to be initialized
 *
 * @var Number
 */
SwatTextarea.pending_poll_interval = 0.1; // in seconds

/**
 * Whether or not the browser supports the CSS3 resize property
 *
 * @var Boolean
 */
SwatTextarea.supports_resize = (function() {
	var div = document.createElement('div');
	var resize = YAHOO.util.Dom.getStyle(div, 'resize');
	return (resize === '' || resize === 'none');
})();


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
};

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
};

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
};

// }}}
