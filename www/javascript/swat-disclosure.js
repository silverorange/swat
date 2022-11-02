class SwatDisclosure {
  constructor(id, open) {
    this.id = id;
    this.div = document.getElementById(id);
    this.input = document.getElementById(id + '_input');
    this.animate_div = this.getAnimateDiv();

    // get initial state
    if (this.input.value.length) {
      // remembered state from post values
      this.opened = this.input.value == 'opened';
    } else {
      // initial display
      this.opened = open;
    }

    // prevent closing during opening animation and vice versa
    this.semaphore = false;

    window.addEventListener('DOMContentLoaded', () => {
      this.init();
    });
  }

  init() {
    this.drawDisclosureLink();
    this.drawPeekabooFix();

    // set initial display state
    if (this.opened) {
      this.open();
    } else {
      this.close();
    }
  }

  toggle() {
    if (this.opened) {
      this.closeWithAnimation();
    } else {
      this.openWithAnimation();
    }
  }

  getSpan() {
    return this.div.firstChild;
  }

  getAnimateDiv() {
    return this.div.firstChild.nextSibling.nextSibling.firstChild;
  }

  drawPeekabooFix() {
    var container = document.getElementById(this.id);
    if (
      container.currentStyle &&
      typeof container.currentStyle.hasLayout != 'undefined'
    ) {
      /*
       * This fix is needed for IE6/7 and fixes display of relative
       * positioned elements below this disclosure during and after
       * animations.
       */
      var empty_div = document.createElement('div');
      var peekaboo_div = document.createElement('div');
      peekaboo_div.style.height = '0';
      peekaboo_div.style.margin = '0';
      peekaboo_div.style.padding = '0';
      peekaboo_div.style.border = 'none';
      peekaboo_div.appendChild(empty_div);

      if (container.nextSibling) {
        container.parentNode.insertBefore(peekaboo_div, container.nextSibling);
      } else {
        container.parentNode.appendChild(peekaboo_div);
      }
    }
  }

  drawDisclosureLink() {
    var span = this.getSpan();
    if (span.firstChild && span.firstChild.nodeType == 3) {
      var text = document.createTextNode(span.firstChild.nodeValue);
    } else {
      var text = document.createTextNode('');
    }

    this.anchor = document.createElement('a');
    this.anchor.href = '#';

    if (this.opened) {
      this.anchor.classList.add('swat-disclosure-anchor-opened');
    } else {
      this.anchor.classList.add('swat-disclosure-anchor-closed');
    }

    this.anchor.addEventListener('click', e => {
      e.preventDefault();
      this.toggle();
    });

    this.anchor.appendChild(text);

    span.parentNode.replaceChild(this.anchor, span);
  }

  close() {
    this.div.classList.remove('swat-disclosure-control-opened');
    this.div.classList.add('swat-disclosure-control-closed');

    this.anchor.classList.remove('swat-disclosure-anchor-opened');
    this.anchor.classList.add('swat-disclosure-anchor-closed');

    this.semaphore = false;

    this.input.value = 'closed';

    this.opened = false;
  }

  closeWithAnimation() {
    if (this.semaphore) {
      return;
    }

    this.anchor.classList.remove('swat-disclosure-anchor-opened');
    this.anchor.classList.add('swat-disclosure-anchor-closed');

    this.animate_div.style.overflow = 'hidden';
    this.animate_div.style.height = 'auto';
    this.animate_div
      .animate(
        [{ height: this.animate_div.offsetHeight + 'px' }, { height: 0 }],
        {
          duration: 250,
          easing: 'ease-out'
        }
      )
      .finished.then(() => {
        this.handleClose();
      });

    this.semaphore = true;
    this.input.value = 'closed';
    this.opened = false;
  }

  open() {
    this.div.classList.remove('swat-disclosure-control-closed');
    this.div.classList.add('swat-disclosure-control-opened');

    this.anchor.classList.remove('swat-disclosure-anchor-closed');
    this.anchor.classList.add('swat-disclosure-anchor-opened');

    this.semaphore = false;

    this.input.value = 'opened';
    this.opened = true;
  }

  openWithAnimation() {
    if (this.semaphore) {
      return;
    }

    this.div.classList.remove('swat-disclosure-control-closed');
    this.div.classList.add('swat-disclosure-control-opened');

    this.anchor.classList.remove('swat-disclosure-anchor-closed');
    this.anchor.classList.add('swat-disclosure-anchor-opened');

    // get display height
    this.animate_div.parentNode.style.overflow = 'hidden';
    this.animate_div.parentNode.style.height = '0';
    this.animate_div.style.visibility = 'hidden';
    this.animate_div.style.overflow = 'hidden';
    this.animate_div.style.display = 'block';
    this.animate_div.style.height = 'auto';
    var height = this.animate_div.offsetHeight;
    this.animate_div.style.height = '0';
    this.animate_div.style.visibility = 'visible';
    this.animate_div.parentNode.style.height = '';
    this.animate_div.parentNode.style.overflow = 'visible';
    this.animate_div
      .animate([{ height: 0 }, { height: height + 'px' }], {
        duration: 500,
        easing: 'ease-out'
      })
      .finished.then(() => {
        this.handleOpen();
      });

    this.semaphore = true;
    this.input.value = 'opened';
    this.opened = true;
  }

  handleClose() {
    this.animate_div.style.height = 0;

    this.div.classList.remove('swat-disclosure-control-opened');
    this.div.classList.add('swat-disclosure-control-closed');

    this.semaphore = false;
  }

  handleOpen() {
    // allow font resizing to work again
    this.animate_div.style.height = 'auto';

    // re-set overflow to visible for styles that might depend on it
    this.animate_div.style.overflow = 'visible';

    this.semaphore = false;
  }
}

class SwatFrameDisclosure extends SwatDisclosure {
  getSpan() {
    return this.div.firstChild.firstChild;
  }

  close() {
    super.close();

    this.div.classList.remove('swat-frame-disclosure-control-opened');
    this.div.classList.add('swat-frame-disclosure-control-closed');
  }

  handleClose() {
    super.handleClose();

    this.div.classList.remove('swat-frame-disclosure-control-opened');
    this.div.classList.add('swat-frame-disclosure-control-closed');
  }

  open() {
    super.open();

    this.div.classList.remove('swat-frame-disclosure-control-closed');
    this.div.classList.add('swat-frame-disclosure-control-opened');
  }

  openWithAnimation() {
    if (this.semaphore) {
      return;
    }

    super.openWithAnimation();

    this.div.classList.remove('swat-frame-disclosure-control-closed');
    this.div.classList.add('swat-frame-disclosure-control-opened');
  }
}
