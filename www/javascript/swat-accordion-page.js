import { Dom } from '../../../yui/www/dom/dom';

export default class SwatAccordionPage {
	constructor(el) {
		this.element = el;
		this.toggle = Dom.getFirstChild(el);
		this.toggleLink = Dom.getElementsByClassName(
			'swat-accordion-page-link',
			'a',
			this.toggle
		)[0];
		this.animation = Dom.getNextSibling(this.toggle);
		this.content = Dom.getFirstChild(this.animation);
	}

	setStatus(status) {
		if (status === 'opened') {
			this.element.classList.remove('swat-accordion-page-closed');
			this.element.classList.add('swat-accordion-page-opened');
		} else {
			this.element.classList.remove('swat-accordion-page-opened');
			this.element.classList.add('swat-accordion-page-closed');
		}
	}
}
