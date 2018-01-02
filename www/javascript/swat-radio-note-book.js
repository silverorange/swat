import { Dom } from '../../../yui/www/dom/dom';
import { Selector } from '../../../yui/www/selector/selector';
import { Event } from '../../../yui/www/event/event';
import { Anim, Easing } from '../../../yui/www/animation/animation';

import '../styles/swat-radio-note-book.css';

class SwatRadioNoteBook {
	constructor(id) {
		this.id = id;
		this.current_page = null;

		Event.onDOMReady(this.init, this, true);
	}

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
					Event.on(option, 'click', function(e) {
						this.setPageWithAnimation(this.pages[index]);
					}, this, true);
				}).call(this);
				count++;
			}
		}

		// get pages
		var tbody = Dom.getFirstChild(table);
		var rows = Dom.getChildrenBy(tbody, function(n) {
			return (Dom.hasClass(
				n,
				'swat-radio-note-book-page-row'
			));
		});

		this.pages = [];
		var page;
		for (var i = 0; i < rows.length; i++) {
			page = Dom.getFirstChild(
				Dom.getNextSibling(
					Dom.getFirstChild(rows[i])
				)
			);

			if (Dom.hasClass(page, 'selected')) {
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
	}

	setPageWithAnimation(page) {
		if (this.current_page === page) {
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

		var region = Dom.getRegion(page.firstChild);
		var height = region.height;

		var anim = new Anim(
			page,
			{ 'height': { to: height } },
			SwatRadioNoteBook.SLIDE_DURATION,
			Easing.easeIn
		);

		anim.onComplete.subscribe(function() {
			page.style.height = 'auto';
			this.restorePageFocusability(page);

			var anim = new Anim(
				page,
				{ opacity: { to: 1 } },
				SwatRadioNoteBook.FADE_DURATION,
				Easing.easeIn
			);

			anim.animate();
		}, this, true);

		anim.animate();
	}

	closePage(page) {
		Dom.setStyle(page, 'opacity', '0');
		page.style.overflow = 'hidden';
		page.style.height = '0';
		this.removePageFocusability(page);
	};

	closePageWithAnimation(page) {
		var anim = new Anim(
			page,
			{ opacity: { to: 0 } },
			SwatRadioNoteBook.FADE_DURATION,
			Easing.easeOut
		);

		anim.onComplete.subscribe(function() {
			page.style.overflow = 'hidden';
			this.removePageFocusability(page);

			var anim = new Anim(
				page,
				{ height: { to: 0 } },
				SwatRadioNoteBook.SLIDE_DURATION,
				Easing.easeOut
			);

			anim.animate();
		}, this, true);

		anim.animate();
	}

	removePageFocusability(page) {
		var elements = Selector.query(
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
	}

	restorePageFocusability(page) {
		var elements = Selector.query(
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
	}
}

SwatRadioNoteBook.FADE_DURATION = 0.25;
SwatRadioNoteBook.SLIDE_DURATION = 0.15;

export default SwatRadioNoteBook;
