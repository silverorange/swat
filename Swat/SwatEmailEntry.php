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
 * @copyright 2005-2009 silverorange
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

		if ($this->value == '') {
			$this->value = null;
			return;
		}

		if ($this->validateEmailAddress($this->value)) {
			$message = Swat::_('The email address you have entered is not '.
				'properly formatted.');

			$this->addMessage(new SwatMessage($message, 'error'));
		}
	}

	// }}}
	// {{{ protected function validateEmailAddress()

	/**
	 * Validates an email address
	 *
	 * This doesn't use the PHP 5.2.x filter_var() function since it allows
	 * addresses without TLD's since 5.2.9. If/when they add a flag to allow to
	 * validate with TLD's, we can start using it again.
	 *
	 * @param string $value the email address to validate.
	 *
	 * @return boolean true if <i>$value</i> is a valid email address and
	 *                  false if it is not.
	 */
	protected function validateEmailAddress($value)
	{
		$valid = false;

		$valid_name_word = '[-!#$%&\'*+.\\/0-9=?A-Z^_`{|}~]+';
		$valid_domain_word = '[-!#$%&\'*+\\/0-9=?A-Z^_`{|}~]+';
		$valid_address_regexp = '/^'.$valid_name_word.'@'.
			$valid_domain_word.'(\.'.$valid_domain_word.')+$/ui';

		$valid = (preg_match($valid_address_regexp, $this->value) === 0);

		return $valid;
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this entry
	 *
	 * @return array the array of CSS classes that are applied to this
	 *                entry.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-email-entry');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
}

?>
