<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatEntry.php';

/**
 * A URL entry widget
 *
 * Automatically verifies that the value of the widget is a valid
 * URL.
 *
 * @package   Swat
 * @copyright 2005-2008 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatUrlEntry extends SwatEntry
{
	// {{{ public function process()

	/**
	 * Processes this url entry
	 *
	 * Ensures this URL is formatted correctly. If the URL
	 * is not formatted correctly, adds an error message to this entry widget.
	 */
	public function process()
	{
		parent::process();

		if ($this->value === null)
			return;

		$this->value = trim($this->value);

		if ($this->value == '') {
			$this->value = null;
			return;
		}

		if ($this->validateUrl($this->value)) {
			$message = Swat::_('The URL you have entered is not '.
				'properly formatted.');

			$this->addMessage(new SwatMessage($message, SwatMessage::ERROR));
		}
	}

	// }}}
	// {{{ protected function validateUrl()

	/**
	 * Validates a URL
	 *
	 * This uses the PHP 5.2.x filter_var() function.
	 *
	 * @param string $value the URL to validate.
	 *
	 * @return boolean true if <i>$value</i> is a valid URL and
	 *                 false if it is not.
	 */
	protected function validateUrl($value)
	{
		$valid = (filter_var($this->value, FILTER_VALIDATE_URL) === false);
		return $valid;
	}

	// }}}
}

?>
