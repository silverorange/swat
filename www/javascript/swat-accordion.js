class SwatAccordion {
  constructor(id) {
    this.id = id;
    this.current_page = null;
    this.animate = true;
    this.always_open = true; // by default, always keep one page open
    this.semaphore = false;
    this.pageChangeEvent = new YAHOO.util.CustomEvent('pageChange');
    this.postInitEvent = new YAHOO.util.CustomEvent('postInit');

    window.addEventListener('DOMContentLoaded', () => {
      this.init();
    });
  }

  static resize_period = 0.25; // seconds

  init() {
    this.container = document.getElementById(this.id);
    this.pages = [];

    var page;
    var pages = this.container.children;

    // check to see if a page is open via a hash-tag
    var hash_open_page = this.getPageFromHash();

    for (var i = 0; i < pages.length; i++) {
      page = new SwatAccordionPage(pages[i]);

      var status_icon = document.createElement('span');
      status_icon.className = 'swat-accordion-toggle-status';

      page.toggle.insertBefore(status_icon, page.toggle.firstChild);
      this.addLinkHash(page);

      if (
        hash_open_page === page.element ||
        (hash_open_page === null && page.element.classList.contains('selected'))
      ) {
        this.current_page = page;
        page.element.classList.remove('selected');
        page.element.classList.add('swat-accordion-page-opened');
      } else {
        page.animation.style.display = 'none';
        page.element.classList.add('swat-accordion-page-closed');
      }

      var that = this;
      (function () {
        var the_page = page;
        page.toggleLink.addEventListener('click', () => {
          if (!that.always_open && the_page === that.current_page) {
            var set_page = null;
          } else {
            var set_page = the_page;
          }

          if (that.animate) {
            that.setPageWithAnimation(set_page);
          } else {
            that.setPage(set_page);
          }
        });
      })();

      this.pages.push(page);
    }

    this.postInitEvent.fire();
  }

  getPageFromHash() {
    var pages = this.container.children;

    // check to see if a page is open via a hash-tag
    var hash_open_page = null;
    for (var i = 0; i < pages.length; i++) {
      if (location.hash == '#open_' + pages[i].id) {
        hash_open_page = pages[i];
      }
    }

    return hash_open_page;
  }

  addLinkHash(page) {
    page.toggleLink.href =
      location.href.split('#')[0] + '#' + 'open_' + page.element.id;
  }

  setPage(page) {
    if (this.current_page === page) {
      return;
    }

    for (var i = 0; i < this.pages.length; i++) {
      if (this.pages[i] === page) {
        this.pages[i].animation.style.display = 'block';
        this.pages[i].setStatus('opened');
      } else {
        this.pages[i].animation.style.display = 'none';
        this.pages[i].setStatus('closed');
      }
    }

    this.pageChangeEvent.fire(page, this.current_page);

    this.current_page = page;
  }

  setPageWithAnimation(new_page) {
    if (this.current_page === new_page || this.semaphore) {
      return;
    }

    this.semaphore = true;

    var old_page = this.current_page;

    // old_page === null means we're opening from a completely closed state
    if (old_page !== null) {
      var old_region = old_page.animation.getBoundingClientRect();
      var old_from_height = old_region.height;
      var old_to_height = 0;
      old_page.animation.style.overflow = 'hidden';

      old_page.animation
        .animate(
          [
            { height: old_from_height + 'px' },
            { height: old_to_height + 'px' }
          ],
          {
            duration: SwatAccordion.resize_period * 1000,
            easing: 'ease-in-out'
          }
        )
        .finished.then(() => {
          this.semaphore = false;
          old_page.animation.style.height = old_to_height;
        });

      old_page.setStatus('closed');
    }

    // new_page === null means we're closing to a completely closed state
    if (new_page !== null) {
      var new_from_height;
      new_page.animation.style.overflow = 'hidden';

      if (
        new_page.animation.style.height === '' ||
        new_page.animation.style.height == 'auto'
      ) {
        new_page.animation.style.height = '0';
        new_from_height = 0;
      } else {
        new_from_height = parseInt(new_page.animation.style.height);
      }

      new_page.animation.style.display = 'block';

      var new_region = new_page.content.getBoundingClientRect();
      var new_to_height = new_region.height;

      new_page.animation
        .animate(
          [
            { height: new_from_height + 'px' },
            { height: new_to_height + 'px' }
          ],
          {
            duration: SwatAccordion.resize_period * 1000,
            easing: 'ease-in-out'
          }
        )
        .finished.then(() => {
          this.semaphore = false;
          new_page.animation.style.height = 'auto';
        });

      new_page.setStatus('opened');
    }

    this.pageChangeEvent.fire(new_page, old_page);

    this.current_page = new_page;
  }
}

class SwatAccordionPage {
  constructor(el) {
    this.element = el;
    this.toggle = el.firstElementChild;
    this.toggleLink = this.toggle.querySelector('a.swat-accordion-page-link');
    this.animation = this.toggle.nextElementSibling;
    this.content = this.animation.firstElementChild;
  }

  setStatus(stat) {
    if (stat === 'opened') {
      this.element.classList.remove('swat-accordion-page-closed');
      this.element.classList.add('swat-accordion-page-opened');
    } else {
      this.element.classList.remove('swat-accordion-page-opened');
      this.element.classList.add('swat-accordion-page-closed');
    }
  }
}
