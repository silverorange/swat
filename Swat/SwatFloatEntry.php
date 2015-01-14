<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatNumericEntry.php';
require_once 'SwatI18N/SwatI18NLocale.php';

/**
 * A float entry widget
 *
 * @package   Swat
 * @copyright 2004-2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatFloatEntry extends SwatNumericEntry
{
	// {{{ public function process()

	/**
	 * Checks to make sure value is a number
	 *
	 * If the value of this widget is not a number then an error message is
	 * attached to this widget.
	 */
	public function process()
	{
		parent::process();

		if ($this->value === null)
			return;

		$float_value = $this->getNumericValue($this->value);

		if ($float_value === null)
			$this->addMessage($this->getValidationMessage('float'));
		else
			$this->value = $float_value;
	}

	// }}}
	// {{{ protected function getDisplayValue()

	/**
	 * Formats a float value to display
	 *
	 * @param string $value the value to format for display.
	 *
	 * @return string the formatted value.
	 */
	protected function getDisplayValue($value)
	{
		if (is_numeric($value)) {
			$locale = SwatI18NLocale::get();
			$thousands_separator =
				($this->show_thousands_separator) ? null : '';

			$value = $locale->formatNumber($value, null,
				array('thousands_separator' => $thousands_separator));
		} else {
			$value = parent::getDisplayValue($value);
		}

		return $value;
	}

	// }}}
	// {{{ protected function getNumericValue()

	/**
	 * Gets the float value of this widget
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
		$locale = SwatI18NLocale::get();
		return $locale->parseFloat($value);
	}

	// }}}
	// {{{ protected function getValidationMessage()

	/**
	 * Gets a validation message for this float entry
	 *
	 * @see SwatEntry::getValidationMessage()
	 * @param string $id the string identifier of the validation message.
	 *
	 * @return SwatMessage the validation message.
	 */
	protected function getValidationMessage($id)
	{
		switch ($id) {
		case 'float':
			$text = $this->show_field_title_in_messages ?
				Swat::_('The %s field must be a number.') :
				Swat::_('This field must be a number.');

			$message = new SwatMessage($text, 'error');

			break;

		default:
			$message = parent::getValidationMessage($id);
			break;
		}

		return $message;
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
		$classes = array('swat-float-entry');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
}

?>
