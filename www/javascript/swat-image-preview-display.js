class SwatImagePreviewDisplay {
  constructor(
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

  static close_text = 'Close';

  init() {
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
  }

  open() {
    document.addEventListener('keydown', this.handleKeyDown);
    this.showOverlay();
    this.opened = true;
  }

  drawOverlay() {
    this.overlay = document.createElement('div');

    this.overlay.className = 'swat-image-preview-overlay';
    this.overlay.style.display = 'none';

    SwatZIndexManager.raiseElement(this.overlay);

    this.draw();

    this.overlay.appendChild(this.preview_mask);
    this.overlay.appendChild(this.preview_container);

    var body = document.getElementsByTagName('body')[0];
    body.appendChild(this.overlay);
  }

  draw() {
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
    this.preview_container.appendChild(this.preview_header);
    this.preview_container.appendChild(this.preview_image);

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
  }

  drawCloseButton() {
    var button = document.createElement('span');

    button.className = 'swat-image-preview-close';
    button.appendChild(
      document.createTextNode(SwatImagePreviewDisplay.close_text)
    );

    return button;
  }

  showOverlay() {
    this.overlay.style.display = 'flex';
  }

  hideOverlay() {
    this.overlay.style.display = 'none';
  }

  close() {
    document.removeEventListener('keydown', this.handleKeyDown);
    this.hideOverlay();
    this.opened = false;
  }

  handleKeyDown(e) {
    // close preview on backspace or escape
    if (e.key === 'Backspace' || e.key === 'Escape') {
      e.preventDefault();
      if (this.opened) {
        this.onClose.fire('keyboard');
      }
      this.close();
    }
  }
}
