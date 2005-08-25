<?php

require_once 'Swat/SwatEntry.php';

/**
 * An email entry widget
 *
 * Automatically verifies that the value of the widget is a valid
 * email address.
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatEmailEntry extends SwatEntry
{
	/**
	 * Processes this email entry
	 *
	 * Ensures this email address is formatted correctly. If the email address
	 * is not formatted correctly, adds an error message to this entry widget.
	 */
	public function process()
	{
		parent::process();
		
		$valid_address_word = '[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+';
		$valid_address_ereg = '^'.$valid_address_word.'@'.
			$valid_address_word.'\.'.$valid_address_word.'$';
			
		if (($this->required || strlen($this->value)) &&
			!ereg($valid_address_ereg, trim($this->value))) {
			$msg = Swat::_('The email address you have entered is not properly formatted.');
			$this->addMessage(new SwatMessage($msg, SwatMessage::ERROR));
		}
	}
}

?>
