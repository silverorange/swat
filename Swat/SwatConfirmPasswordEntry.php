<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatPasswordEntry.php';

/**
 * A password confirmation entry widget
 *
 * Automatically compares the value of the confirmation with the matching
 * password widget to see if they match.
 *
 * @package   Swat
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatConfirmPasswordEntry extends SwatPasswordEntry
{
	// {{{ public properties

	/**
	 * A reference to the matching password widget
	 *
	 * @var SwatPasswordEntry
	 */
	public $password_widget = null;

	// }}}
	// {{{ public function process()

	/**
	 * Checks to make sure passwords match
	 *
	 * Checks to make sure the values of the two password fields are the same.
	 * If an associated password widget is not set, an exception is thrown. If
	 * the passwords do not match, an error is added to this widget.
	 *
	 * @throws SwatException
	 */
	public function process()
	{
		parent::process();

		if ($this->password_widget === null)
			throw new SwatException("Property 'password_widget' is null. ".
				'Expected a reference to a SwatPasswordEntry.');

		if ($this->password_widget->value !== null) {
			if (strcmp($this->password_widget->value, $this->value) != 0) {
				$message = Swat::_('Password and confirmation password do not '.
					'match.');

				$this->addMessage(new SwatMessage($message, 'error'));
			}
		}
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
		$classes = array('swat-password-entry');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
}

?>
