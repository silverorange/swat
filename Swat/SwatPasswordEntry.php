<?php

require_once 'Swat/SwatControl.php';

/**
 * A password entry widget
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
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
