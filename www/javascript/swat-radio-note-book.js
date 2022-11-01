class SwatRadioNoteBook {
  constructor(id) {
    this.id = id;
    this.current_page = null;

    window.addEventListener('DOMContentLoaded', () => {
      this.init();
    });
  }

  static FADE_DURATION = 0.25;
  static SLIDE_DURATION = 0.15;

  init() {
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
          option.addEventListener('click', () => {
            this.setPageWithAnimation(this.pages[index]);
          });
        }.call(this));
        count++;
      }
    }

    // get pages
    var tbody = table.firstElementChild;
    var rows = tbody.querySelectorAll('.swat-radio-note-book-page-row');

    this.pages = [];
    var page;
    for (var i = 0; i < rows.length; i++) {
      page = rows[i].firstElementChild.nextElementSibling.firstElementChild;

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
  }

  setPage(page) {
    // TODO
  }

  setPageWithAnimation(page) {
    if (this.current_page == page) {
      return;
    }

    this.closePageWithAnimation(this.current_page);
    this.openPageWithAnimation(page);

    this.current_page = page;
  }

  openPageWithAnimation(page) {
    page.style.overflow = 'visible';
    page.style.height = '0';
    page.firstChild.style.visibility = 'visible';
    page.firstChild.style.height = 'auto';

    var region = page.firstChild.getBoundingClientRect();
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
  }

  closePage(page) {
    page.style.opacity = 0;
    page.style.overflow = 'hidden';
    page.style.height = '0';
    this.removePageFocusability(page);
  }

  closePageWithAnimation(page) {
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
  }

  removePageFocusability(page) {
    var elements = page.querySelectorAll(
      'input, select, textarea, button, a, *[tabindex]'
    );

    for (var i = 0; i < elements.length; i++) {
      if (elements[i].dataset['_' + this.id + '_tabIndex'] === undefined) {
        elements[i].dataset['_' + this.id + '_tabIndex'] = elements[i].tabIndex;
        elements[i].tabIndex = -1;
      }
    }
  }

  restorePageFocusability(page) {
    var elements = page.querySelectorAll(
      'input, select, textarea, button, a, *[tabindex]'
    );

    for (var i = 0; i < elements.length; i++) {
      if (elements[i].dataset['_' + this.id + '_tabIndex'] !== undefined) {
        elements[i].tabIndex = elements[i].dataset['_' + this.id + '_tabIndex'];
        delete elements[i].dataset['_' + this.id + '_tabIndex'];
      }
    }
  }
}
