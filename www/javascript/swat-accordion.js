import { Dom } from '../../../yui/www/dom/dom';
import { CustomEvent } from '../../../yui/www/event/event';
import { Anim, Easing } from '../../../yui/www/animation/animation';

import SwatAccordionPage from './swat-accordion-page';

import '../styles/swat-accordion.css';

class SwatAccordion {
	constructor(id) {
		this.id = id;
		this.current_page = null;
		this.animate = true;
		this.always_open = true; // by default, always keep one page open
		this.semaphore = false;
		this.pageChangeEvent = new CustomEvent('pageChange');
		this.postInitEvent = new CustomEvent('postInit');

		this.init = this.init.bind(this);

		document.addEventListener('DOMContentLoaded', this.init);
	}

	init() {
		this.container = document.getElementById(this.id);
		this.pages = [];

		var page;
		var pages = Dom.getChildren(this.container);

		// check to see if a page is open via a hash-tag
		var hash_open_page = this.getPageFromHash();

		for (var i = 0; i < pages.length; i++) {
			page = new SwatAccordionPage(pages[i]);

			var status_icon = document.createElement('span');
			status_icon.className = 'swat-accordion-toggle-status';

			page.toggle.insertBefore(status_icon, page.toggle.firstChild);
			this.addLinkHash(page);

			if (hash_open_page === page.element || (hash_open_page === null &&
				page.element.classList.contains('selected'))
			) {
				this.current_page = page;
				page.element.classList.remove('selected');
				page.element.classList.add('swat-accordion-page-opened');
			} else {
				page.animation.style.display = 'none';
				page.element.classList.add('swat-accordion-page-closed');
			}

			var that = this;
			(function() {
				var the_page = page;
				page.toggleLink.addEventListener('click', function (e) {
					var set_page;
					if (!that.always_open && the_page === that.current_page) {
						set_page = null;
					} else {
						set_page = the_page;
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
		var pages = Dom.getChildren(this.container);

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
		page.toggleLink.href = location.href.split('#')[0] + '#' +
			'open_' + page.element.id;
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
		var new_from_height = 0;

		// old_page === null means we're opening from a completely closed state
		if (old_page !== null) {
			var old_region = Dom.getRegion(old_page.animation);
			var old_from_height = old_region.height;
			var old_to_height = 0;
			old_page.animation.style.overflow = 'hidden';
		}

		// new_page === null means we're closing to a completely closed state
		if (new_page === null) {
			var new_to_height = 0;

			var anim = new Anim(
				old_page.animation, { },
				SwatAccordion.resize_period,
				Easing.easeBoth
			);
		} else {
			new_page.animation.style.overflow = 'hidden';

			if (new_page.animation.style.height === '' ||
				new_page.animation.style.height === 'auto'
			) {
				new_page.animation.style.height = '0';
			} else {
				new_from_height = parseInt(new_page.animation.style.height);
			}

			new_page.animation.style.display = 'block';

			var new_region = Dom.getRegion(new_page.content);
			var new_to_height = new_region.height;

			var anim = new Anim(
				new_page.animation, { },
				SwatAccordion.resize_period,
				Easing.easeBoth
			);
		}

		anim.onTween.subscribe(function (name, data) {
			if (old_page !== null) {
				var old_height = Math.ceil(
					anim.doMethod('height', old_from_height, old_to_height));

				old_page.animation.style.height = old_height + 'px';
			}

			if (new_page !== null) {
				var new_height = Math.floor(
					anim.doMethod('height', new_from_height, new_to_height));

				new_page.animation.style.height = new_height + 'px';
			}
		}, this, true);

		anim.onComplete.subscribe(function () {
			if (new_page !== null) {
				new_page.animation.style.height = 'auto';
			}

			this.semaphore = false;
		}, this, true);

		anim.animate();

		if (old_page !== null) {
			old_page.setStatus('closed');
		}

		if (new_page !== null) {
			new_page.setStatus('opened');
		}

		this.pageChangeEvent.fire(new_page, old_page);

		this.current_page = new_page;
	}
}

SwatAccordion.resize_period = 0.25; // seconds

export default SwatAccordion;
