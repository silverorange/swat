<?php
require_once('Swat/SwatPasswordEntry.php');

/**
 * A password confirmation entry widget
 *
 * Automatically compares the value of the confirmation with the matching
 * password widget to see if they match.
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatConfirmPasswordEntry extends SwatPasswordEntry {

	/**
	 * Matching password widget
	 */
	public $password_widget = null;
	
	public function process() {
		parent::process();
		
		if ($this->password_widget === null)
			throw new SwatException('SwatConfirmPasswordEntry: You must declare the matching password widget');

		if ($this->password_widget->value !== null) {
		
			if (strcmp($this->password_widget->value, $this->value) != 0) {
				$msg = _S("Password and Confirmation Password do not match.");
				$this->addMessage(new SwatMessage($msg, SwatMessage::USER_ERROR));
			}

		}
	}
}
?>
