function SwatImagePreviewDisplay(id, preview_src, preview_width, preview_height)
{
	var image = document.getElementById(id);
	image.style.cursor = 'pointer';

	this.overlay = document.createElement('div');
	this.overlay.className = 'swat-image-preview-overlay';

	this.preview_image = document.createElement('img');
	this.preview_image.src = preview_src;
	this.preview_image.width = preview_width;
	this.preview_image.height = preview_height;

	this.lightbox = document.createElement('div');
	this.lightbox.className = 'swat-image-preview-lightbox';
	this.lightbox.appendChild(this.preview_image);

	YAHOO.util.Event.addListener(image, 'click',
		this.display, this, true);

	YAHOO.util.Event.addListener(this.lightbox, 'click',
		this.close, this, true);

	YAHOO.util.Event.addListener(this.overlay, 'click',
		this.close, this, true);
}

SwatImagePreviewDisplay.prototype.display = function(event)
{
	var padding = 20;
	var max_width = YAHOO.util.Dom.getViewportWidth() - (padding * 2);
	var max_height = YAHOO.util.Dom.getViewportHeight() - (padding * 2);

	this.overlay.style.width = YAHOO.util.Dom.getDocumentWidth() + 'px';
	this.overlay.style.height = YAHOO.util.Dom.getDocumentHeight() + 'px';

	if (this.preview_image.width > max_width) {
		this.preview_image.height =
			(this.preview_image.height * (max_width / this.preview_image.width));

		this.preview_image.width = max_width;
	}

	if (this.preview_image.height > max_height) {
		this.preview_image.width =
			(this.preview_image.width * (max_height / this.preview_image.height));

		this.preview_image.height = max_height;
	}

	document.getElementsByTagName('body')[0].appendChild(this.overlay);
	document.getElementsByTagName('body')[0].appendChild(this.lightbox);

	var x = ((max_width - this.preview_image.width) / 2) + (padding / 2);
	var y = ((max_height - this.preview_image.height) / 2) + (padding / 2);

	YAHOO.util.Dom.setX(this.preview_image, x);
	YAHOO.util.Dom.setY(this.preview_image, y);
}

SwatImagePreviewDisplay.prototype.close = function(event)
{
	document.getElementsByTagName('body')[0].removeChild(this.overlay);
	document.getElementsByTagName('body')[0].removeChild(this.lightbox);
}
