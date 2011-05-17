<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatNoteBook.php';

/**
 * Accordion widget containing {@link SwatNoteBookPage} pages.
 *
 * This widget is like a notebook but instead of tabs, pages are displayed
 * stacked and open and close like disclosures. It sounds like a ye-olde
 * squeezebox.
 *
 * @package   Swat
 * @copyright 2011 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       SwatNoteBookPage
 */
class SwatAccordion extends SwatNoteBook
{
	// {{{ public function __construct()

	/**
	 * Creates a new accordion view
	 *
	 * @param string $id a non-visable unique id for this widget.
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->requires_id = true;

		$yui = new SwatYUI(array('yahoo', 'dom', 'event', 'animation'));
		$this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());

		$this->addStyleSheet('packages/swat/styles/swat-accordion.css',
			Swat::PACKAGE_ID);

		$this->addJavaScript('packages/swat/javascript/swat-accordion.js',
			Swat::PACKAGE_ID);
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this notebook
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		SwatWidget::display();

		$li_counter = 0;

		$ul_tag = new SwatHtmlTag('ul');
		$ul_tag->id = $this->id;
		$ul_tag->class = 'swat-accordion';
		$ul_tag->open();

		foreach ($this->pages as $page) {
			if (!$page->visible)
				continue;

			$li_counter++;
			$li_tag = new SwatHtmlTag('li');

			if (($this->selected_page === null && $li_counter == 1) ||
				($page->id == $this->selected_page)) {
				$li_tag->class = 'swat-accordion-page selected';
			} else {
				$li_tag->class = 'swat-accordion-page';
			}

			$li_tag->open();

			// toggle link
			$title = ($page->title === null) ? '' : $page->title;
			$anchor_tag = new SwatHtmlTag('a');
			$anchor_tag->class = 'swat-accordion-page-toggle';
			$anchor_tag->href = '#'.$page->id;
			$em_tag = new SwatHtmlTag('em');
			$em_tag->setContent($title);
			$anchor_tag->open();
			$em_tag->display();
			$anchor_tag->close();

			// content
			echo '<div class="swat-accordion-page-animation">';
			echo '<div class="swat-accordion-page-content">';
			$page->display();
			echo '</div>';
			echo '</div>';

			$li_tag->close();
		}

		$ul_tag->close();
		Swat::displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ protected function getInlineJavaScript()

	/**
	 * Gets the inline JavaScript used by this accordion view
	 *
	 * @return string the inline JavaScript used by this accordion view.
	 */
	protected function getInlineJavaScript()
	{
		return sprintf("var %s_obj = new SwatAccordion('%s');",
			$this->id, $this->id);
	}

	// }}}
}

?>
