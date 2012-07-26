function SwatRadioNoteBook(id)
{
	this.id = id;
	this.current_page = null;

	YAHOO.util.Event.onDOMReady(this.init, this, true);
};

SwatRadioNoteBook.FADE_DURATION = 0.25;
SwatRadioNoteBook.SLIDE_DURATION = 0.15;

SwatRadioNoteBook.prototype.init = function()
{
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
				YAHOO.util.Event.on(option, 'click', function(e) {
					this.setPageWithAnimation(this.pages[index]);
				}, this, true);
			}).call(this);
			count++;
		}
	}

	// get pages
	var tbody = YAHOO.util.Dom.getFirstChild(table);
	var rows = YAHOO.util.Dom.getChildrenBy(tbody, function(n) {
		return (YAHOO.util.Dom.hasClass(n, 'swat-radio-note-book-page-row'));
	});

	this.pages = [];
	var page;
	for (var i = 0; i < rows.length; i++) {
		page = YAHOO.util.Dom.getFirstChild(
			YAHOO.util.Dom.getNextSibling(
				YAHOO.util.Dom.getFirstChild(rows[i])
			)
		);

		if (YAHOO.util.Dom.hasClass(page, 'selected')) {
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

SwatRadioNoteBook.handleClick = function(e)
{

};

SwatRadioNoteBook.prototype.setPage = function(page)
{
};

SwatRadioNoteBook.prototype.setPageWithAnimation = function(page)
{
	if (this.current_page == page) {
		return;
	}

	this.closePageWithAnimation(this.current_page);
	this.openPageWithAnimation(page);

	this.current_page = page;
};

SwatRadioNoteBook.prototype.openPageWithAnimation = function(page)
{
	page.style.overflow = 'visible';
	page.style.height = '0';
	page.firstChild.style.visibility = 'visible';
	page.firstChild.style.height = 'auto';

	var region = YAHOO.util.Dom.getRegion(page.firstChild);
	var height = region.height;

	page.style.height = 'auto';
	page.firstChild.style.height = '0';

	var anim = new YAHOO.util.Anim(
		page.firstChild,
		{ 'height': { to: height } },
		SwatRadioNoteBook.SLIDE_DURATION,
		YAHOO.util.Easing.easeIn
	);

	anim.onComplete.subscribe(function() {
		page.firstChild.style.height = 'auto';

		var anim = new YAHOO.util.Anim(
			page,
			{ opacity: { to: 1 } },
			SwatRadioNoteBook.FADE_DURATION,
			YAHOO.util.Easing.easeIn
		);

		anim.animate();
	});

	anim.animate();
};

SwatRadioNoteBook.prototype.closePage = function(page)
{
	YAHOO.util.Dom.setStyle(page, 'opacity', '0');
	page.style.overflow = 'hidden';
	page.firstChild.style.height = '0';
};

SwatRadioNoteBook.prototype.closePageWithAnimation = function(page)
{
	var anim = new YAHOO.util.Anim(
		page,
		{ opacity: { to: 0 } },
		SwatRadioNoteBook.FADE_DURATION,
		YAHOO.util.Easing.easeOut
	);

	anim.onComplete.subscribe(function() {
		page.style.overflow = 'hidden';
		var anim = new YAHOO.util.Anim(
			page.firstChild,
			{ height: { to: 0 } },
			SwatRadioNoteBook.SLIDE_DURATION,
			YAHOO.util.Easing.easeOut
		);

		anim.animate();
	});

	anim.animate();
};
