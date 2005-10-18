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
	 * Displays this toolbar
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$toolbar_div = new SwatHtmlTag('div');
		$toolbar_div->class = 'swat-toolbar';

		$toolbar_div->open();
		parent::display();
		$toolbar_div->close();
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
