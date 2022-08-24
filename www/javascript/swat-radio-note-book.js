function SwatRadioNoteBook(id) {
  this.id = id;
  this.current_page = null;

  window.addEventListener('DOMContentLoaded', () => {
    this.init();
  });
}

SwatRadioNoteBook.FADE_DURATION = 0.25;
SwatRadioNoteBook.SLIDE_DURATION = 0.15;

SwatRadioNoteBook.prototype.init = function() {
  var table = document.getElementById(this.id);

  // get radio options
  var unfiltered_options = document.getElementsByName(this.id);
  this.options = [];
  var count = 0;
  for (var i = 0; i < unfiltered_options.length; i++) {
    if (unfiltered_options[i].name == this.id) {
      this.options.push(unfiltered_options[i]);
      (function() {
        var option = unfiltered_options[i];
        var index = count;
        YAHOO.util.Event.on(
          option,
          'click',
          function(e) {
            this.setPageWithAnimation(this.pages[index]);
          },
          this,
          true
        );
      }.call(this));
      count++;
    }
  }

  // get pages
  var tbody = YAHOO.util.Dom.getFirstChild(table);
  var rows = YAHOO.util.Dom.getChildrenBy(tbody, function(n) {
    return n.classList.contains('swat-radio-note-book-page-row');
  });

  this.pages = [];
  var page;
  for (var i = 0; i < rows.length; i++) {
    page = YAHOO.util.Dom.getFirstChild(
      YAHOO.util.Dom.getNextSibling(YAHOO.util.Dom.getFirstChild(rows[i]))
    );

    if (page.classList.contains('selected')) {
      this.current_page = page;
    }

    if (this.options[i].checked) {
      this.current_page = page;
    } else {
      this.closePage(page);
    }

    this.pages.push(page);
  }
};

SwatRadioNoteBook.prototype.setPage = function(page) {};

SwatRadioNoteBook.prototype.setPageWithAnimation = function(page) {
  if (this.current_page == page) {
    return;
  }

  this.closePageWithAnimation(this.current_page);
  this.openPageWithAnimation(page);

  this.current_page = page;
};

SwatRadioNoteBook.prototype.openPageWithAnimation = function(page) {
  page.style.overflow = 'visible';
  page.style.height = '0';
  page.firstChild.style.visibility = 'visible';
  page.firstChild.style.height = 'auto';

  var region = YAHOO.util.Dom.getRegion(page.firstChild);
  var height = region.height;

  var anim = new YAHOO.util.Anim(
    page,
    { height: { to: height } },
    SwatRadioNoteBook.SLIDE_DURATION,
    YAHOO.util.Easing.easeIn
  );

  anim.onComplete.subscribe(
    function() {
      page.style.height = 'auto';
      this.restorePageFocusability(page);

      var anim = new YAHOO.util.Anim(
        page,
        { opacity: { to: 1 } },
        SwatRadioNoteBook.FADE_DURATION,
        YAHOO.util.Easing.easeIn
      );

      anim.animate();
    },
    this,
    true
  );

  anim.animate();
};

SwatRadioNoteBook.prototype.closePage = function(page) {
  YAHOO.util.Dom.setStyle(page, 'opacity', '0');
  page.style.overflow = 'hidden';
  page.style.height = '0';
  this.removePageFocusability(page);
};

SwatRadioNoteBook.prototype.closePageWithAnimation = function(page) {
  var anim = new YAHOO.util.Anim(
    page,
    { opacity: { to: 0 } },
    SwatRadioNoteBook.FADE_DURATION,
    YAHOO.util.Easing.easeOut
  );

  anim.onComplete.subscribe(
    function() {
      page.style.overflow = 'hidden';
      this.removePageFocusability(page);

      var anim = new YAHOO.util.Anim(
        page,
        { height: { to: 0 } },
        SwatRadioNoteBook.SLIDE_DURATION,
        YAHOO.util.Easing.easeOut
      );

      anim.animate();
    },
    this,
    true
  );

  anim.animate();
};

SwatRadioNoteBook.prototype.removePageFocusability = function(page) {
  var elements = YAHOO.util.Selector.query(
    'input, select, textarea, button, a, *[tabindex]',
    page
  );

  for (var i = 0; i < elements.length; i++) {
    if (elements[i].getAttribute('_' + this.id + '_tabindex') === null) {
      var tabindex;

      if ('hasAttribute' in elements[i]) {
        tabindex = elements[i].getAttribute('tabindex');
      } else {
        tabindex = elements[i].tabIndex; // for old IE
        if (tabindex === 0) {
          tabindex = null;
        }
      }

      elements[i]['_' + this.id + '_tabindex'] = tabindex;
      elements[i].tabindex = -1;
      elements[i].setAttribute('tabIndex', -1); // For old IE
    }
  }
};

SwatRadioNoteBook.prototype.restorePageFocusability = function(page) {
  var elements = YAHOO.util.Selector.query(
    'input, select, textarea, button, a, *[tabindex]',
    page
  );

  for (var i = 0; i < elements.length; i++) {
    if (elements[i].getAttribute('_' + this.id + '_tabindex') !== null) {
      var tabindex = elements[i]['_' + this.id + '_tabindex'];
      if (tabindex === '' || tabindex === null) {
        elements[i].removeAttribute('tabindex');
        elements[i].removeAttribute('tabIndex'); // For old IE
      } else {
        elements[i].tabindex = tabindex;
        elements[i].setAttribute('tabIndex', tabindex); // For old IE
      }
      elements[i].removeAttribute('_' + this.id + '_tabindex');
    }
  }
};
