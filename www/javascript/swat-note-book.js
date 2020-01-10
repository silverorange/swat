import { TabView } from '../../../yui/www/tabview/tabview';

import '../../../yui/www/tabview/assets/tabview.css';
import '../styles/swat-note-book.css';

export default class SwatNoteBook {
	constructor(id, options) {
		this.tabview = new TabView(id, options);
	}
}
