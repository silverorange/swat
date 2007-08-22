<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatCheckbox.php';
require_once 'Swat/SwatYUI.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * A "check all" JavaScript powered checkbox
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatCheckAll extends SwatCheckbox
{
	// {{{ public properties

	/**
	 * Optional checkbox label title
	 *
	 * Defaults to "Check All".
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Optional content type for title
	 *
	 * Defaults to text/plain, use text/xml for XHTML fragments.
	 *
	 * @var string
	 */
	public $content_type = 'text/plain';

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new check-all widget
	 *
	 * Sets the widget title to a default value.
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);
		$this->title = Swat::_('Check All');
		$yui = new SwatYUI(array('event'));
		$this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());
		$this->addJavaScript('packages/swat/javascript/swat-check-all.js',
			Swat::PACKAGE_ID);
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this check-all widget
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id;
		$div_tag->class = $this->getCSSClassString();
		$div_tag->open();

		$label_tag = new SwatHtmlTag('label');
		$label_tag->for = $this->id.'_value';
		$label_tag->setContent($this->title, $this->content_type);
		$label_tag->open();

		$old_id = $this->id;
		$this->id.= '_value';
		parent::display();
		$this->id = $old_id;

		$label_tag->displayContent();
		$label_tag->close();

		$div_tag->close();

		Swat::displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this check-all widget
	 *
	 * @return array the array of CSS classes that are applied to this
	 *               check-all widget.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-check-all');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
	// {{{ protected function getInlineJavaScript()

	/**
	 * Gets the inline JavaScript for this check-all widget
	 *
	 * @return string the inline JavaScript for this check-all widget.
	 */
	protected function getInlineJavaScript()
	{
		return sprintf("var %s_obj = new SwatCheckAll('%s');",
			$this->id, $this->id);

	}

	// }}}
}

?>
