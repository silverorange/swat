<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

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
	// {{{ public function process()

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

		$this->value = trim($this->value);

		if (strlen($this->value) == 0) {
			$this->value = null;
			return;
		}

		if (filter_var($this->value, FILTER_VALIDATE_EMAIL) === false) {
			$msg = Swat::_('The email address you have entered is not '.
				'properly formatted.');

			$this->addMessage(new SwatMessage($msg, SwatMessage::ERROR));
		}
	}

	// }}}
}

?>
