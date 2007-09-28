<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatDisplayableContainer.php';
require_once 'Swat/SwatTitleable.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * Fieldset tag container
 *
 * An HTML fieldset tag with an optional HTML legend title.
 *
 * @package   Swat
 * @copyright 2004-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatFieldset extends SwatDisplayableContainer implements SwatTitleable
{
	// {{{ public properties

	/**
	 * Fieldset title
	 *
	 * A visible title for this fieldset, or null.
	 *
	 * @var string
	 */
	public $title = null;

	/**
	 * Access key
	 *
	 * Access key for this fieldset legend, for keyboard nagivation.
	 *
	 * @var string
	 */
	public $access_key = null;

	// }}}
	// {{{ public function getTitle()

	/**
	 * Gets the title of this fieldset
	 *
	 * Implements the {SwatTitleable::getTitle()} interface.
	 *
	 * @return the title of this fieldset.
	 */
	public function getTitle()
	{
		return $this->title;
	}

	// }}}
	// {{{ public function display()

	public function display()
	{
		if (!$this->visible)
			return;

		SwatWidget::display();

		$fieldset_tag = new SwatHtmlTag('fieldset');
		$fieldset_tag->id = $this->id;
		$fieldset_tag->class = $this->getCSSClassString();
		$fieldset_tag->open();

		if ($this->title !== null) {
			$legend_tag = new SwatHtmlTag('legend');

		if (strlen($this->access_key) > 0)
			$legend_tag->accesskey = $this->access_key;

			$legend_tag->setContent($this->title);
			$legend_tag->display();
		}

		$this->displayChildren();

		$fieldset_tag->close();
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this fieldset
	 *
	 * @return array the array of CSS classes that are applied to this fieldset.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-fieldset');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
}

?>
