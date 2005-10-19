<?php

require_once 'Swat/SwatContainer.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * A toolbar container for a group of related {@link SwatToolLink} objects
 *
 * @package   Swat
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatToolbar extends SwatContainer
{
	/**
	 * Displays this toolbar as an unordered list with each sub-item
	 * as a list item
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$toolbar_ul = new SwatHtmlTag('ul');
		$toolbar_ul->class = 'swat-toolbar';

		$toolbar_ul->open();

		foreach ($this->children as &$child) {
			echo '<li>';
			$child->display();
			echo '</li>';
		}

		$toolbar_ul->close();
	}

	/**
	 * Sets the value of all {@link SwatToolLink} objects within this toolbar
	 *
	 * This is usually more convenient than setting all the values by hand
	 * if the values are dynamic.
	 *
	 * @param string $value
	 */
	public function setToolLinkValues($value)
	{
		foreach ($this->getDescendants('SwatToolLink') as $tool)
			$tool->value = $value;
	}
}

?>
