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
 * @copyright 2011-2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       SwatNoteBookPage
 */
class SwatAccordion extends SwatNoteBook
{
	// {{{ public properties

	/**
	 * Whether or not to animate the opening/closing of the accordion
	 *
	 * @var boolean
	 */
	public $animate = true;

	/**
	 * Whether or not one page of the accordion is always open
	 *
	 * If false, the accordion can collapse to an all-closed state.
	 *
	 * @var boolean
	 */
	public $always_open = false;

	// }}}
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

		$this->addStyleSheet('packages/swat/styles/swat-accordion.css');
		$this->addJavaScript('packages/swat/javascript/swat-accordion.js');
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

		if (!$this->animate) {
			$ul_tag->{'data-no-animate'} = 'no-animate';
		}

		if (!$this->always_open) {
			$ul_tag->{'data-not-always-open'} = 'not-always-open';
		}

		$ul_tag->open();

		foreach ($this->pages as $page) {
			if (!$page->visible)
				continue;

			$li_counter++;
			$li_tag = new SwatHtmlTag('li');
			$li_tag->id = $page->id;

			if ($page->id === $this->selected_page) {
				$li_tag->class = 'swat-accordion-page selected';
			} else {
				$li_tag->class = 'swat-accordion-page';
			}

			$li_tag->class.= ' '.implode(' ', $page->classes);

			$li_tag->open();

			// toggle link
			$title = ($page->title === null) ? '' : $page->title;
			$anchor_tag = new SwatHtmlTag('a');
			$anchor_tag->class = 'swat-accordion-page-toggle';
			$anchor_tag->href = '#'.$page->id;
			$em_tag = new SwatHtmlTag('em');
			$em_tag->setContent($title, $page->title_content_type);
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
	}

	// }}}
}

?>
