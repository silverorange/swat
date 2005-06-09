<?php

require_once 'Swat/SwatControl.php';

/**
 * A password entry widget
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatPasswordEntry extends SwatEntry
{
	/**
	 * Displays this password entry widget
	 *
	 * @see SwatEntry::display()
	 */
	public function display()
	{
		$this->html_input_type = 'password';
		parent::display();
	}
}

?>
