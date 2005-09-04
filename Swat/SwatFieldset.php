<?php

require_once 'Swat/SwatContainer.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * Fieldset tag container
 *
 * An HTML fieldset tag with an optional HTML legend title.
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatFieldset extends SwatContainer
{
	/**
	 * Fieldset title
	 *
	 * A visible title for this fieldset, or null.
	 *
	 * @var string
	 */
	public $title = null;

	public function display()
	{
		if (!$this->visible)
			return;

		$fieldset_tag = new SwatHtmlTag('fieldset');
		$fieldset_tag->class = 'swat-fieldset';

		$fieldset_tag->open();

		if ($this->title !== null) {
			$legend_tag = new SwatHtmlTag('legend');
			$legend_tag->open();
			echo $this->title;
			$legend_tag->close();
		}

		foreach ($this->children as &$child)
			$child->display();

		$fieldset_tag->close();
	}
}

?>
