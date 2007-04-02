<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatNumericEntry.php';
require_once 'Swat/SwatString.php';

/**
 * An integer entry widget
 *
 * @package   Swat
 * @copyright 2004-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatIntegerEntry extends SwatNumericEntry
{
	// {{{ public function process()

	/**
	 * Checks to make sure value is an integer
	 *
	 * If the value of this widget is not an integer then an error message is
	 * attached to this widget.
	 */
	public function process()
	{
		parent::process();

		if ($this->value === null)
			return;

		$integer_value = $this->getNumericValue($this->value);

		if ($integer_value === null)
			$this->addMessage($this->getValidationMessage('integer'));
		else
			$this->value = $integer_value;
	}

	// }}}
	// {{{ protected function getDisplayValue()

	protected function getDisplayValue()
	{
		if (is_int($this->value))
			return SwatString::numberFormat($this->value, 0, null,
				$this->show_thousands_separator);
		else
			return $this->value;
	}

	// }}}
	// {{{  protected function getNumericValue()

	/**
	 * Gets the numeric value of this widget
	 *
	 * This allows each widget to parse raw values how they want to get numeric
	 * values.
	 *
	 * @param string $value the raw value to use to get the numeric value.
	 *
	 * @return mixed the numeric value of this entry widget or null if no
	 *                numeric value is available.
	 */
	protected function getNumericValue($value)
	{
		$value = trim($value);
		return SwatString::toInteger($value);
	}

	// }}}
	// {{{ protected function getValidationMessage()

	/**
	 * Gets a validation message for this integer entry
	 *
	 * @see SwatEntry::getValidationMessage()
	 * @param string $id the string identifier of the validation message.
	 *
	 * @return SwatMessage the validation message.
	 */
	protected function getValidationMessage($id)
	{
		switch ($id) {
		case 'integer':
			$message = new SwatMessage(
				Swat::_('The %s field must be an integer.'),
				SwatMessage::ERROR);

			break;

		default:
			$message = parent::getValidationMessage($id);
			break;
		}

		return $message;
	}

	// }}}
}

?>
