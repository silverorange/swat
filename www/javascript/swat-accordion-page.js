export default class SwatAccordionPage {
	constructor(el) {
		this.element = el;
		this.toggle = YAHOO.util.Dom.getFirstChild(el);
		this.toggleLink = YAHOO.util.Dom.getElementsByClassName(
			'swat-accordion-page-link',
			'a',
			this.toggle
		)[0];
		this.animation = YAHOO.util.Dom.getNextSibling(this.toggle);
		this.content = YAHOO.util.Dom.getFirstChild(this.animation);
	}

	setStatus(status) {
		if (status === 'opened') {
			YAHOO.util.Dom.removeClass(
				this.element,
				'swat-accordion-page-closed'
			);
			YAHOO.util.Dom.addClass(
				this.element,
				'swat-accordion-page-opened'
			);
		} else {
			YAHOO.util.Dom.removeClass(
				this.element,
				'swat-accordion-page-opened'
			);
			YAHOO.util.Dom.addClass(
				this.element,
				'swat-accordion-page-closed'
			);
		}
	}
}
