<?php

require_once('Swat/SwatControl.php');

/**
 * A password entry widget
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatPasswordEntry extends SwatEntry {
	
	public function display() {
		$this->html_input_type = 'password';
		parent::display();
	}
}

?>
