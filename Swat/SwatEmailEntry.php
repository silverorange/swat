<?php

require_once 'Swat/SwatEntry.php';

/**
 * An email entry widget
 *
 * Automatically verifies that the value of the widget is a valid
 * email address.
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
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

		if ($this->value === null)
			return;

		$valid_address_word = '[-!#$%&\'*+\\.\/0-9=?A-Z^_`{|}~]+';
		$valid_address_regexp = '/^'.$valid_address_word.'@'.
			$valid_address_word.'\.'.$valid_address_word.'$/ui';

		if (strlen($this->value) &&
			preg_match($valid_address_regexp, $this->value) === 0) {
			$msg = Swat::_('The email address you have entered is not '.
				'properly formatted.');

			$this->addMessage(new SwatMessage($msg, SwatMessage::ERROR));
		}
	}
}

?>
