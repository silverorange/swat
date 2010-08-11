function SwatImagePreviewDisplay(id, preview_src, preview_width, preview_height,
	show_title)
{
	this.id     = id;
	this.opened = false;
	this.show_title = show_title;

	this.preview_image = document.createElement('img');
	this.preview_image.id = this.id + '_preview';
	this.preview_image.src = preview_src;
	this.preview_image.width = preview_width;
	this.preview_image.height = preview_height;

	this.preview_close_text = document.createElement('span');
	this.preview_close_text.className = 'swat-image-preview-close';
	this.preview_close_text.appendChild(
		document.createTextNode(
			SwatImagePreviewDisplay.close_text
		)
	);

	this.preview_link = document.createElement('a');
	this.preview_link.href = '#';
	this.preview_link.appendChild(this.preview_close_text);
	this.preview_link.appendChild(this.preview_image);

	this.preview_container = document.createElement('div');
	this.preview_container.className = 'swat-image-preview-container';
	this.preview_container.style.display = 'none';
	this.preview_container.appendChild(this.preview_link);

	// list of select elements to hide for IE6
	this.select_elements = [];

	YAHOO.util.Event.onDOMReady(this.init, this, true);
}

SwatImagePreviewDisplay.ie6 = false /*@cc_on || @_jscript_version < 5.7 @*/;
SwatImagePreviewDisplay.close_text = 'Close';

/**
 * Padding of preview image
 *
 * @var Number
 */
SwatImagePreviewDisplay.padding = 16;

SwatImagePreviewDisplay.prototype.init = function()
{
	this.drawOverlay();

	var image_wrapper = document.getElementById(this.id + '_wrapper');
	if (image_wrapper.tagName == 'SPAN') {
		var image_link = document.createElement('a');

		image_link.title     = image_wrapper.title;
		image_link.className = image_wrapper.className;
		image_link.href      = '#';

		while (image_wrapper.firstChild) {
			image_link.appendChild(image_wrapper.firstChild);
		}

		image_wrapper.parentNode.replaceChild(image_link, image_wrapper);

		if (this.show_title) {
			var a_tag = document.createElement('a');
			a_tag.href = '#';
			a_tag.appendChild(document.createTextNode(image_wrapper.title))
			image_link.parentNode.appendChild(a_tag);
			YAHOO.util.Event.on(a_tag, 'click',
				this.handleClick, this, true);
		}

		YAHOO.util.Event.on(image_link, 'click',
			this.handleClick, this, true);
	} else {
		image_wrapper.href = '#';
		YAHOO.util.Event.on(image_wrapper, 'click',
			this.handleClick, this, true);
	}

	// add preview image to document
	var body = document.getElementsByTagName('body')[0];
	body.appendChild(this.preview_container);
	body.style.position = 'relative';

	// setup event handlers
	YAHOO.util.Event.on(this.preview_container, 'click',
		this.handleClick, this, true);

	YAHOO.util.Event.on(this.preview_link, 'keypress',
		this.handleKeyPress, this, true);
}

SwatImagePreviewDisplay.prototype.open = function()
{
	// get approximate max height and width excluding close text
	var padding = SwatImagePreviewDisplay.padding;
	var max_width = YAHOO.util.Dom.getViewportWidth() - (padding * 2);
	var max_height = YAHOO.util.Dom.getViewportHeight() - (padding * 2);

	this.showOverlay();

	this.scaleImage(max_width, max_height);

	this.preview_container.style.visibility = 'hidden';
	this.preview_container.style.display = 'block';

	// now that is it displayed, adjust height for the close text
	var region = YAHOO.util.Dom.getRegion(this.preview_close_text);
	max_height -= (region.bottom - region.top);
	this.scaleImage(max_width, max_height);

	this.preview_container.style.visibility = 'visible';

	// x is relative to center of page
	var scroll_top = YAHOO.util.Dom.getDocumentScrollTop();
	var x = -Math.round((this.preview_image.width  + padding) / 2);
	var y = Math.round((max_height - this.preview_image.height + padding) / 2) +
		scroll_top;

	YAHOO.util.Dom.setY(this.preview_container, y);

	// set x
	this.preview_container.style.left = '50%';
	this.preview_container.style.marginLeft = x + 'px';

	// focus link to capture keyboard events
	this.preview_link.focus();

	this.opened = true;
}

SwatImagePreviewDisplay.prototype.scaleImage = function(max_width, max_height)
{
	// if preview image is larger than viewport width, scale down
	if (this.preview_image.width > max_width) {
		this.preview_image.width = max_width;
		this.preview_image.height = (this.preview_image.height *
			(max_width / this.preview_image.width));
	}

	// if preview image is larger than viewport height, scale down
	if (this.preview_image.height > max_height) {
		this.preview_image.width = (this.preview_image.width *
			(max_height / this.preview_image.height));

		this.preview_image.height = max_height;
	}
}

SwatImagePreviewDisplay.prototype.drawOverlay = function()
{
	this.overlay = document.createElement('div');

	this.overlay.className = 'swat-image-preview-overlay';
	this.overlay.style.display = 'none';

	YAHOO.util.Event.on(this.overlay, 'click', this.close, this, true);

	var body = document.getElementsByTagName('body')[0];
	body.appendChild(this.overlay);
}

SwatImagePreviewDisplay.prototype.showOverlay = function()
{
	if (SwatImagePreviewDisplay.ie6) {
		this.select_elements = document.getElementsByTagName('select');
		for (var i = 0; i < this.select_elements.length; i++) {
			this.select_elements[i].style._visibility =
				this.select_elements[i].style.visibility;

			this.select_elements[i].style.visibility = 'hidden';
		}
	}
	this.overlay.style.height = YAHOO.util.Dom.getDocumentHeight() + 'px';
	this.overlay.style.display = 'block';
}

SwatImagePreviewDisplay.prototype.hideOverlay = function()
{
	this.overlay.style.display = 'none';
	if (SwatImagePreviewDisplay.ie6) {
		for (var i = 0; i < this.select_elements.length; i++) {
			this.select_elements[i].style.visibility =
				this.select_elements[i].style._visibility;
		}
	}
}

SwatImagePreviewDisplay.prototype.close = function()
{
	this.hideOverlay();

	this.preview_container.style.display = 'none';

	this.opened = false;
}

SwatImagePreviewDisplay.prototype.handleClick = function(e)
{
	YAHOO.util.Event.preventDefault(e);

	if (this.opened)
		this.close();
	else
		this.open();
}

SwatImagePreviewDisplay.prototype.handleKeyPress = function(e)
{
	YAHOO.util.Event.preventDefault(e);

	// close preview on backspace or escape
	if (e.keyCode == 8 || e.keyCode == 27)
		this.close();
}
