import { Dom } from '../../../yui/www/dom/dom';
import { Event } from '../../../yui/www/event/event';

import '../styles/swat-textarea.css';

/**
 * A resizeable textarea widget
 *
 * @package   Swat
 * @copyright 2007-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTextarea {
	// {{{ constructor()

	/**
	 * Creates a new textarea object
	 *
	 * @param string id the unique identifier of this textarea object.
	 * @param boolean resizeable whether or not this textarea is resizeable.
	 */
	constructor(id, resizeable) {
		this.id = id;

		if (resizeable) {
			Event.onContentReady(
				this.id,
				this.handleOnAvailable,
				this,
				true
			);
		}
	}

	// }}}
	// {{{ handleOnAvailable()

	/**
	 * Sets up the resize handle when the textarea is available and loaded in
	 * the DOM tree
	 */
	handleOnAvailable() {
		this.textarea = document.getElementById(this.id);

		// check if textarea already is resizable, and if so, don't add resize
		// handle.
		if (SwatTextarea.supports_resize) {
			var resize = Dom.getStyle(this.textarea, 'resize');
			if (resize == 'both' || resize == 'vertical') {
				return;
			}
		}

		this.handle_div = document.createElement('div');
		this.handle_div.className = 'swat-textarea-resize-handle';
		this.handle_div._textarea = this.textarea;

		this.textarea._resize = this;

		Event.addListener(this.handle_div, 'touchstart',
			SwatTextarea.touchstartEventHandler, this.handle_div);

		Event.addListener(this.handle_div, 'mousedown',
			SwatTextarea.mousedownEventHandler, this.handle_div);

		this.textarea.parentNode.appendChild(this.handle_div);
		this.textarea.parentNode.classList.add(
			'swat-textarea-with-resize'
		);

		// if textarea is not currently visible, delay initilization
		if (this.textarea.offsetWidth === 0) {
			SwatTextarea.registerPendingTextarea(this);
			return;
		}

		this.initialize();
	}

	// }}}
	// {{{ initialize()

	initialize() {
		var style_width = Dom.getStyle(this.textarea, 'width');
		var left_border, right_border;

		if (style_width.indexOf('%') != -1) {
			left_border = parseInt(
				Dom.getComputedStyle(
					this.textarea,
					'borderLeftWidth'
				),
				10
			) - parseInt(
				Dom.getComputedStyle(
					this.handle_div,
					'borderLeftWidth'
				),
				10
			);

			right_border = parseInt(
				Dom.getComputedStyle(
					this.textarea,
					'borderRightWidth'
				),
				10
			) - parseInt(
				Dom.getComputedStyle(
					this.handle_div,
					'borderRightWidth'
				),
				10
			);

			this.handle_div.style.width = style_width;
			this.handle_div.style.paddingLeft = left_border;
			this.handle_div.style.paddingRight = right_border;
		} else {
			var width = this.textarea.offsetWidth;

			left_border = parseInt(
				Dom.getComputedStyle(
					this.handle_div,
					'borderLeftWidth'
				),
				10
			);

			right_border = parseInt(
				Dom.getComputedStyle(
					this.handle_div,
					'borderRightWidth'
				),
				10
			);

			this.handle_div.style.width =
				(width - left_border - right_border) + 'px';
		}

		this.handle_div.style.height = SwatTextarea.resize_handle_height + 'px';
		this.handle_div.style.fontSize = '0'; // for IE6 height

		if ('ontouchstart' in window) {
			// make it taller for fingers
			this.handle_div.style.height =
				(SwatTextarea.resize_handle_height + 16) + 'px';
		}
	}

	// }}}
}

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
	var resize = Dom.getStyle(div, 'resize');

	// Both iOS and Android feature detection say they support resize, but
	// they do not. Fall back to checking the UA here.
	return (!YAHOO.env.ua.ios && !YAHOO.env.ua.android &&
		(resize === '' || resize === 'none'));
})();

// }}}
// {{{ SwatTextarea.registerPendingTextarea()

SwatTextarea.registerPendingTextarea = function(textarea) {
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

SwatTextarea.pollPendingTextareas = function() {
	for (var i = 0; i < SwatTextarea.pending_textareas.length; i++) {
		if (SwatTextarea.pending_textareas[i].textarea.offsetWidth > 0) {
			SwatTextarea.pending_textareas[i].initialize();
			SwatTextarea.pending_textareas.splice(i, 1);
			i--;
		}
	}

	if (SwatTextarea.pending_textareas.length === 0) {
		clearInterval(SwatTextarea.pending_interval);
		SwatTextarea.pending_interval = null;
	}
};

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
SwatTextarea.mousedownEventHandler = function(e, handle) {
	// prevent text selection
	Event.preventDefault(e);

	// only allow left click to do things
	var is_webkit = (/AppleWebKit|Konqueror|KHTML/gi).test(navigator.userAgent);
	var is_ie = (navigator.userAgent.indexOf('MSIE') != -1);
	if ((is_ie && (e.button & 1) != 1) ||
		(!is_ie && !is_webkit && e.button !== 0))
		return false;

	SwatTextarea.dragging_item = handle;
	SwatTextarea.dragging_mouse_origin_y =
		Event.getPageY(e);

	var textarea = handle._textarea;

	Dom.setStyle(textarea, 'opacity', 0.25);

	var height = parseInt(Dom.getStyle(textarea, 'height'), 10);
	if (height) {
		SwatTextarea.dragging_origin_height = height;
	} else {
		// get original height for IE6
		SwatTextarea.dragging_origin_height = textarea.clientHeight;
	}

	Event.removeListener(handle, 'mousedown',
		SwatTextarea.mousedownEventHandler);

	Event.removeListener(handle, 'touchstart',
		SwatTextarea.touchstartEventHandler);

	Event.addListener(document, 'mousemove',
		SwatTextarea.mousemoveEventHandler, handle);

	Event.addListener(document, 'mouseup',
		SwatTextarea.mouseupEventHandler, handle);
};

// }}}
// {{{ SwatTextarea.touchstartEventHandler()

/**
 * Handles touchstart events for resize handles
 *
 * @param DOMEvent   event the event to handle.
 * @param DOMElement the drag handle being grabbed.
 *
 * @return boolean false
 */
SwatTextarea.touchstartEventHandler = function(e, handle) {
	// prevent text selection
	Event.preventDefault(e);

	SwatTextarea.dragging_item = handle;

	if ('touches' in e) {
		for (var i = 0; i < e.touches.length; i++) {
			SwatTextarea.dragging_mouse_origin_y = e.touches[i].pageY;
			break;
		}

		var textarea = handle._textarea;

		Dom.setStyle(textarea, 'opacity', 0.25);

		var height = parseInt(Dom.getStyle(textarea, 'height'));
		if (height) {
			SwatTextarea.dragging_origin_height = height;
		} else {
			// get original height for IE6
			SwatTextarea.dragging_origin_height = textarea.clientHeight;
		}

		Event.removeListener(handle, 'mousedown',
			SwatTextarea.mousedownEventHandler);

		Event.removeListener(handle, 'touchstart',
			SwatTextarea.touchstartEventHandler);

		Event.addListener(document, 'touchmove',
			SwatTextarea.touchmoveEventHandler, handle);

		Event.addListener(document, 'touchend',
			SwatTextarea.touchendEventHandler, handle);
	}
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
SwatTextarea.mousemoveEventHandler = function(e, handle) {
	var resize_handle = SwatTextarea.dragging_item;
	var textarea = resize_handle._textarea;

	var delta = Event.getPageY(e) -
		SwatTextarea.dragging_mouse_origin_y;

	var height = SwatTextarea.dragging_origin_height + delta;
	if (height >= SwatTextarea.min_height) {
		textarea.style.height = height + 'px';
	}

	return false;
};

// }}}
// {{{ SwatTextarea.touchmoveEventHandler()

/**
 * Handles touch movement when dragging a resize bar
 *
 * Updates the height of the associated textarea control.
 *
 * @param DOMEvent event the event that triggered this function.
 *
 * @return boolean false.
 */
SwatTextarea.touchmoveEventHandler = function(e, handle) {
	var resize_handle = SwatTextarea.dragging_item;
	var textarea = resize_handle._textarea;

	if ('touches' in e) {
		for (var i = 0; i < e.touches.length; i++) {

			var delta = e.touches[i].pageY
				- SwatTextarea.dragging_mouse_origin_y;

			var height = SwatTextarea.dragging_origin_height + delta;
			if (height >= SwatTextarea.min_height) {
				textarea.style.height = height + 'px';
			}

			break;
		}

		e.preventDefault();
	}

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
SwatTextarea.mouseupEventHandler = function(e, handle) {
	// only allow left click to do things
	var is_webkit = (/AppleWebKit|Konqueror|KHTML/gi).test(navigator.userAgent);
	var is_ie = (navigator.userAgent.indexOf('MSIE') != -1);
	if ((is_ie && (e.button & 1) != 1) ||
		(!is_ie && !is_webkit && e.button !== 0))
		return false;

	Event.removeListener(document, 'mousemove',
		SwatTextarea.mousemoveEventHandler);

	Event.removeListener(document, 'touchmove',
		SwatTextarea.touchmoveEventHandler);

	Event.removeListener(document, 'mouseup',
		SwatTextarea.mouseupEventHandler);

	Event.removeListener(document, 'touchend',
		SwatTextarea.touchendEventHandler);

	Event.addListener(handle, 'mousedown',
		SwatTextarea.mousedownEventHandler, handle);

	Event.addListener(handle, 'touchstart',
		SwatTextarea.touchstartEventHandler, handle);

	SwatTextarea.dragging_item = null;
	SwatTextarea.dragging_mouse_origin_y = null;
	SwatTextarea.dragging_origin_height = null;

	Dom.setStyle(handle._textarea, 'opacity', 1);

	return false;
};

// }}}
// {{{ SwatTextarea.touchendEventHandler()

/**
 * Handles touchend events when dragging a resize bar
 *
 * Stops dragging.
 *
 * @param DOMEvent   event the event that triggered this function.
 * @param DOMElement the drag handle being released.
 *
 * @return boolean false.
 */
SwatTextarea.touchendEventHandler = function(e, handle) {
	return SwatTextarea.mouseupEventHandler(e, handle);
};

// }}}

export default SwatTextarea;
