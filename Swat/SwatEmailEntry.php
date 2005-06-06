<?php

require_once('Swat/SwatEntry.php');

/**
 * An email entry widget
 *
 * Automatically verifies that the value of the widget is a valid
 * email address.
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatEmailEntry extends SwatEntry {
	
	public function process() {
		parent::process();
		
		$valid_address_word = '[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+';
		$valid_address_ereg = '^'.$valid_address_word.'@'.
			$valid_address_word.'\.'.$valid_address_word.'$';
			
		if (($this->required || strlen($this->value)) &&
			!ereg($valid_address_ereg, trim($this->value))) {
			$msg = _S("The email address you have entered ".
				"is not properly formatted.");
			$this->addMessage(new SwatMessage($msg, SwatMessage::USER_ERROR));
		}
	}
}

?>
