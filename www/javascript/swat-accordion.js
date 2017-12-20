function SwatAccordion(id)
{
	this.id = id;
	this.current_page = null;
	this.animate = true;
	this.always_open = true; // by default, always keep one page open
	this.semaphore = false;
	this.pageChangeEvent = new YAHOO.util.CustomEvent('pageChange');
	this.postInitEvent = new YAHOO.util.CustomEvent('postInit');

	YAHOO.util.Event.onDOMReady(this.init, this, true);
}

SwatAccordion.resize_period = 0.25; // seconds

SwatAccordion.prototype.init = function()
{
	this.container = document.getElementById(this.id);
	this.pages = [];

	var page;
	var pages = YAHOO.util.Dom.getChildren(this.container);

	// check to see if a page is open via a hash-tag
	var hash_open_page = this.getPageFromHash();

	for (var i = 0; i < pages.length; i++) {
		page = new SwatAccordionPage(pages[i]);

		var status_icon = document.createElement('span');
		status_icon.className = 'swat-accordion-toggle-status';

		page.toggle.insertBefore(status_icon, page.toggle.firstChild);
		this.addLinkHash(page);

		if (hash_open_page === page.element || (hash_open_page === null &&
			YAHOO.util.Dom.hasClass(page.element, 'selected'))) {

			this.current_page = page;
			YAHOO.util.Dom.removeClass(page.element, 'selected');
			YAHOO.util.Dom.addClass(page.element, 'swat-accordion-page-opened');
		} else {
			page.animation.style.display = 'none';
			YAHOO.util.Dom.addClass(page.element, 'swat-accordion-page-closed');
		}

		var that = this;
		(function() {
			var the_page = page;
			YAHOO.util.Event.on(page.toggle, 'click', function (e) {
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

};

SwatAccordion.prototype.getPageFromHash = function()
{
	var pages = YAHOO.util.Dom.getChildren(this.container);

	// check to see if a page is open via a hash-tag
	var hash_open_page = null;
	for (var i = 0; i < pages.length; i++) {
		if (location.hash == '#open_' + pages[i].id) {
			hash_open_page = pages[i];
		}
	}

	return hash_open_page;
};

SwatAccordion.prototype.addLinkHash = function(page)
{
	page.toggleLink.href = location.href.split('#')[0] + '#' +
		'open_' + page.element.id;
};

SwatAccordion.prototype.setPage = function(page)
{
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
};

SwatAccordion.prototype.setPageWithAnimation = function(new_page)
{
	if (this.current_page === new_page || this.semaphore) {
		return;
	}

	this.semaphore = true;

	var old_page = this.current_page;

	// old_page === null means we're opening from a completely closed state
	if (old_page !== null) {
		var old_region = YAHOO.util.Dom.getRegion(old_page.animation);
		var old_from_height = old_region.height;
		var old_to_height = 0;
		old_page.animation.style.overflow = 'hidden';
	}

	// new_page === null means we're closing to a completely closed state
	if (new_page === null) {
		var new_to_height = 0;

		var anim = new YAHOO.util.Anim(
			old_page.animation, { },
			SwatAccordion.resize_period,
			YAHOO.util.Easing.easeBoth);
	} else {
		new_page.animation.style.overflow = 'hidden';

		if (new_page.animation.style.height === '' ||
			new_page.animation.style.height == 'auto') {
			new_page.animation.style.height = '0';
			new_from_height = 0;
		} else {
			new_from_height = parseInt(new_page.animation.style.height);
		}

		new_page.animation.style.display = 'block';

		var new_region = YAHOO.util.Dom.getRegion(new_page.content);
		var new_to_height = new_region.height;

		var anim = new YAHOO.util.Anim(
			new_page.animation, { },
			SwatAccordion.resize_period,
			YAHOO.util.Easing.easeBoth);
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
};

function SwatAccordionPage(el)
{
	this.element    = el;
	this.toggle     = YAHOO.util.Dom.getFirstChild(el);
	this.toggleLink = YAHOO.util.Dom.getElementsByClassName(
		'swat-accordion-page-link', 'a', this.toggle)[0];

	this.animation  = YAHOO.util.Dom.getNextSibling(this.toggle);
	this.content    = YAHOO.util.Dom.getFirstChild(this.animation);
}

SwatAccordionPage.prototype.setStatus = function(stat)
{
	if (stat === 'opened') {
		YAHOO.util.Dom.removeClass(
			this.element,
			'swat-accordion-page-closed');

		YAHOO.util.Dom.addClass(
			this.element,
			'swat-accordion-page-opened');
	} else {
		YAHOO.util.Dom.removeClass(
			this.element,
			'swat-accordion-page-opened');

		YAHOO.util.Dom.addClass(
			this.element,
			'swat-accordion-page-closed');
	}
};
