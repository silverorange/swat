function SwatImagePreviewDisplay(
  id,
  preview_src,
  preview_width,
  preview_height,
  show_title,
  preview_title
) {
  this.id = id;
  this.opened = false;
  this.show_title = show_title;
  this.preview_title = preview_title;
  this.preview_src = preview_src;
  this.preview_width = preview_width;
  this.preview_height = preview_height;

  this.handleKeyDown = this.handleKeyDown.bind(this);

  this.onOpen = new YAHOO.util.CustomEvent('open');
  this.onClose = new YAHOO.util.CustomEvent('close');

  window.addEventListener('DOMContentLoaded', () => {
    this.init();
  });
}

SwatImagePreviewDisplay.close_text = 'Close';

/**
 * Padding of preview image
 *
 * @var Number
 */
SwatImagePreviewDisplay.padding = 16;

SwatImagePreviewDisplay.prototype.init = function() {
  this.drawOverlay();

  // link up the thumbnail image
  var image_wrapper = document.getElementById(this.id + '_wrapper');
  if (image_wrapper.tagName == 'A') {
    image_wrapper.href = '#view';
    image_wrapper.addEventListener('click', e => {
      e.preventDefault();
      if (!this.opened) {
        this.onOpen.fire('thumbnail');
      }
      this.open();
    });
  } else {
    var image_link = document.createElement('a');

    image_link.title = image_wrapper.title;
    image_link.className = image_wrapper.className;
    image_link.href = '#view';

    while (image_wrapper.firstChild) {
      image_link.appendChild(image_wrapper.firstChild);
    }

    image_wrapper.parentNode.replaceChild(image_link, image_wrapper);

    if (this.show_title) {
      var span_tag = document.createElement('span');
      span_tag.className = 'swat-image-preview-title';
      span_tag.appendChild(document.createTextNode(image_wrapper.title));
      image_link.appendChild(span_tag);
    }

    image_link.addEventListener('click', e => {
      e.preventDefault();
      if (!this.opened) {
        this.onOpen.fire('thumbnail');
      }
      this.open();
    });
  }
};

SwatImagePreviewDisplay.prototype.open = function() {
  document.addEventListener('keydown', this.handleKeyDown);

  // get approximate max height and width excluding close text
  var padding = SwatImagePreviewDisplay.padding;
  var max_width = YAHOO.util.Dom.getViewportWidth() - padding * 2;
  var max_height = YAHOO.util.Dom.getViewportHeight() - padding * 2;

  this.showOverlay();

  this.scaleImage(max_width, max_height);

  this.preview_container.style.visibility = 'hidden';
  this.preview_container.style.display = 'block';

  // now that is it displayed, adjust height for the close text
  var region = YAHOO.util.Dom.getRegion(this.preview_header);
  max_height -= region.bottom - region.top;
  this.scaleImage(max_width, max_height);

  this.preview_container.style.visibility = 'visible';

  // x is relative to center of page
  var scroll_top = YAHOO.util.Dom.getDocumentScrollTop();
  var x = -Math.round((this.preview_image.width + padding) / 2);
  var y =
    Math.round((max_height - this.preview_image.height + padding) / 2) +
    scroll_top;

  this.preview_container.style.top = y + 'px';

  // set x
  this.preview_container.style.left = '50%';
  this.preview_container.style.marginLeft = x + 'px';

  this.opened = true;
};

SwatImagePreviewDisplay.prototype.scaleImage = function(max_width, max_height) {
  // if preview image is larger than viewport width, scale down
  if (this.preview_image.width > max_width) {
    this.preview_image.height =
      this.preview_image.height * (max_width / this.preview_image.width);

    this.preview_image.width = max_width;
  }

  // if preview image is larger than viewport height, scale down
  if (this.preview_image.height > max_height) {
    this.preview_image.width =
      this.preview_image.width * (max_height / this.preview_image.height);

    this.preview_image.height = max_height;
  }

  // For IE 6 & 7
  this.preview_container.style.width = this.preview_image.width + 'px';
};

SwatImagePreviewDisplay.prototype.drawOverlay = function() {
  this.overlay = document.createElement('div');

  this.overlay.className = 'swat-image-preview-overlay';
  this.overlay.style.display = 'none';

  SwatZIndexManager.raiseElement(this.overlay);

  this.draw();

  this.overlay.appendChild(this.preview_mask);
  this.overlay.appendChild(this.preview_container);

  var body = document.getElementsByTagName('body')[0];
  body.appendChild(this.overlay);
};

SwatImagePreviewDisplay.prototype.draw = function() {
  // overlay mask
  this.preview_mask = document.createElement('a');
  this.preview_mask.className = 'swat-image-preview-mask';
  this.preview_mask.href = '#close';

  SwatZIndexManager.raiseElement(this.preview_mask);

  this.preview_mask.addEventListener('click', e => {
    e.preventDefault();
    if (this.opened) {
      this.onClose.fire('overlayMask');
    }
    this.close();
  });

  this.preview_mask.addEventListener('mouseover', () => {
    this.preview_close_button.classList.add('swat-image-preview-close-hover');
  });

  this.preview_mask.addEventListener('mouseout', () => {
    this.preview_close_button.classList.remove(
      'swat-image-preview-close-hover'
    );
  });

  // preview title
  this.title = document.createElement('span');
  this.title.className = 'swat-image-preview-title';
  if (this.preview_title) {
    this.title.appendChild(document.createTextNode(this.preview_title));
  } else {
    // non-breaking space to hold container open when there is no title
    this.title.appendChild(document.createTextNode(' '));
  }

  // close button
  this.preview_close_button = this.drawCloseButton();
  SwatZIndexManager.raiseElement(this.preview_close_button);

  // header
  this.preview_header = document.createElement('span');
  this.preview_header.className = 'swat-image-preview-header';
  this.preview_header.appendChild(this.preview_close_button);
  this.preview_header.appendChild(this.title);

  SwatZIndexManager.raiseElement(this.preview_header);

  // image
  this.preview_image = document.createElement('img');
  this.preview_image.id = this.id + '_preview';
  this.preview_image.src = this.preview_src;
  this.preview_image.width = this.preview_width;
  this.preview_image.height = this.preview_height;

  // image container
  this.preview_container = document.createElement('a');
  this.preview_container.href = '#close';
  this.preview_container.className = 'swat-image-preview-container';
  this.preview_container.style.display = 'none';
  this.preview_container.appendChild(this.preview_header);
  this.preview_container.appendChild(this.preview_image);

  // For IE6 & 7
  this.preview_container.style.width = this.preview_width + 'px';

  SwatZIndexManager.raiseElement(this.preview_container);

  this.preview_container.addEventListener('click', e => {
    e.preventDefault();
    if (this.opened) {
      this.onClose.fire('container');
    }
    this.close();
  });

  this.preview_container.addEventListener('mouseover', () => {
    this.preview_close_button.classList.add('swat-image-preview-close-hover');
  });

  this.preview_container.addEventListener('mouseout', () => {
    this.preview_close_button.classList.remove(
      'swat-image-preview-close-hover'
    );
  });
};

SwatImagePreviewDisplay.prototype.drawCloseButton = function() {
  var button = document.createElement('span');

  button.className = 'swat-image-preview-close';
  button.appendChild(
    document.createTextNode(SwatImagePreviewDisplay.close_text)
  );

  return button;
};

SwatImagePreviewDisplay.prototype.showOverlay = function() {
  this.overlay.style.height = YAHOO.util.Dom.getDocumentHeight() + 'px';
  this.overlay.style.display = 'block';
};

SwatImagePreviewDisplay.prototype.hideOverlay = function() {
  this.overlay.style.display = 'none';
};

SwatImagePreviewDisplay.prototype.close = function() {
  document.removeEventListener('keydown', this.handleKeyDown);

  this.hideOverlay();

  this.preview_container.style.display = 'none';

  this.opened = false;
};

SwatImagePreviewDisplay.prototype.handleKeyDown = function(e) {
  // close preview on backspace or escape
  if (e.key === 'Backspace' || e.key === 'Escape') {
    e.preventDefault();
    if (this.opened) {
      this.onClose.fire('keyboard');
    }
    this.close();
  }
};
