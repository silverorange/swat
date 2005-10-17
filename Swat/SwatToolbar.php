<?php

require_once 'Swat/SwatContainer.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * A container for a group of related SwatToolLinks
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
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

	public function setToolLinkValues($value) {
		foreach ($this->getDescendants('SwatToolLink') as $tool)
			$tool->value = $value;
	}
}

?>
