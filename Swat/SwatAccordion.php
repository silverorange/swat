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
 * @copyright 2011-2012 silverorange
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
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this notebook
	 */
	public function display(SwatDisplayContext $context)
	{
		if (!$this->visible) {
			return;
		}

		SwatWidget::display($context);

		$li_counter = 0;

		$ul_tag = new SwatHtmlTag('ul');
		$ul_tag->id = $this->id;
		$ul_tag->class = 'swat-accordion';
		$ul_tag->open($context);

		foreach ($this->pages as $page) {
			if (!$page->visible) {
				continue;
			}

			$li_counter++;
			$li_tag = new SwatHtmlTag('li');
			$li_tag->id = $page->id;

			if ($page->id === $this->selected_page) {
				$li_tag->class = 'swat-accordion-page selected';
			} else {
				$li_tag->class = 'swat-accordion-page';
			}

			$li_tag->class.= ' '.implode(' ', $page->classes);
			$li_tag->open($context);

			// toggle link
			$title = ($page->title === null) ? '' : $page->title;
			$anchor_tag = new SwatHtmlTag('a');
			$anchor_tag->class = 'swat-accordion-page-toggle';
			$anchor_tag->href = '#'.$page->id;
			$em_tag = new SwatHtmlTag('em');
			$em_tag->setContent($title, $page->title_content_type);
			$anchor_tag->open($context);
			$em_tag->display($context);
			$anchor_tag->close($context);

			// content
			$context->out('<div class="swat-accordion-page-animation">');
			$context->out('<div class="swat-accordion-page-content">');
			$page->display($context);
			$context->out('</div>');
			$context->out('</div>');

			$li_tag->close($context);
		}

		$ul_tag->close($context);

		$context->addYUI('yahoo', 'dom', 'event', 'animation');
		$context->addStyleSheet('packages/swat/styles/swat-accordion.css');
		$context->addScript('packages/swat/javascript/swat-accordion.js');
		$context->addInlineScript($this->getInlineJavaScript());
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
		$javascript = sprintf("var %1\$s_obj = new %2\$s('%1\$s', %3\$s);",
			$this->id,
			$this->getJavascriptClassName(),
			$this->animate ? 'true' : 'false');

		$javascript.= sprintf("\n%s_obj.animate = %s;",
			$this->id,
			$this->animate ? 'true' : 'false');

		$javascript.= sprintf("\n%s_obj.always_open = %s;",
			$this->id,
			$this->always_open ? 'true' : 'false');

		return $javascript;
	}

	// }}}
	// {{{ protected function getJavaScriptClassName()

	protected function getJavaScriptClassName()
	{
		return 'SwatAccordion';
	}

	// }}}
}

?>
