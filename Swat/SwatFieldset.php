<?php

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

	public function display()
	{
		if (!$this->visible)
			return;

		$fieldset_tag = new SwatHtmlTag('fieldset');
		$fieldset_tag->class = $this->getCssClasses('swat-fieldset');
		if ($this->id !== null)
			$fieldset_tag->id = $this->id;

		$fieldset_tag->open();

		if ($this->title !== null) {
			$legend_tag = new SwatHtmlTag('legend');

		if (strlen($this->access_key) > 0)
			$legend_tag->accesskey = $this->access_key;

			$legend_tag->setContent($this->title);
			$legend_tag->display();
		}

		foreach ($this->children as &$child)
			$child->display();

		$fieldset_tag->close();
	}
}

?>
