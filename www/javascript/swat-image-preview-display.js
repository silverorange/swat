function SwatImagePreviewDisplay(id, preview_src, preview_width, preview_height)
{
	this.id     = id;
	this.opened = false;

	this.preview_image = document.createElement('img');
	this.preview_image.src = preview_src;
	this.preview_image.width = preview_width;
	this.preview_image.height = preview_height;

	this.preview_link = document.createElement('a');
	this.preview_link.href = '#';
	this.preview_link.appendChild(this.preview_image);

	this.preview_container = document.createElement('div');
	this.preview_container.className = 'swat-image-preview-container';
	this.preview_container.style.display = 'none';
	this.preview_container.appendChild(this.preview_link);

	YAHOO.util.Event.onDOMReady(this.init, this, true);
}

/**
 * Padding of preview image
 *
 * @var Number
 */
SwatImagePreviewDisplay.padding = 20;

SwatImagePreviewDisplay.prototype.init = function()
{
	this.drawOverlay();

	// add preview image to document
	var image_link = document.getElementById(this.id + '_link');
	image_link.parentNode.appendChild(this.preview_container);

	// setup event handlers
	YAHOO.util.Event.addListener(image_link, 'click',
		this.handleClick, this, true);

	YAHOO.util.Event.addListener(this.preview_container, 'click',
		this.handleClick, this, true);

	YAHOO.util.Event.addListener(this.preview_link, 'keypress',
		this.handleKeyPress, this, true);
}

SwatImagePreviewDisplay.prototype.open = function()
{
	var padding = SwatImagePreviewDisplay.padding;
	var max_width = YAHOO.util.Dom.getViewportWidth() - (padding * 2);
	var max_height = YAHOO.util.Dom.getViewportHeight() - (padding * 2);

	this.showOverlay();

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

	this.preview_container.style.display = 'block';

	var x = (max_width - this.preview_image.width  + padding) / 2;
	var y = (max_height - this.preview_image.height + padding) / 2;

	YAHOO.util.Dom.setXY(this.preview_image, [x, y]);

	// focus link to capture keyboard events
	this.preview_link.focus();

	this.opened = true;
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
	this.overlay.style.height = YAHOO.util.Dom.getDocumentHeight() + 'px';
	this.overlay.style.display = 'block';
}

SwatImagePreviewDisplay.prototype.hideOverlay = function()
{
	this.overlay.style.display = 'none';
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
