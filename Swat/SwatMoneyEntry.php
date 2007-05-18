<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatString.php';
require_once 'Swat/SwatFloatEntry.php';

/**
 * A money entry widget
 *
 * @package   Swat
 * @copyright 2004-2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatMoneyEntry extends SwatFloatEntry
{
	// {{{ public properties

	/**
	 * Optional locale for currency format
	 *
	 * If no locale is specified, the default server locale is used.
	 *
	 * @var string
	 */
	public $locale = null;

	/**
	 * Whether to display international currency unit
	 *
	 * If true, displays the international currency unit
	 *
	 * @var boolean
	 */
	public $display_currency = false;

	/**
	 * Number of decimal places to accept
	 *
	 * This also controls how many decimal places are displayed when editing
	 * existing values.
	 *
	 * If set to null, the number of decimal places allowed by the locale is
	 * used.
	 *
	 * @var integer
	 */
	public $decimal_places = null;

	// }}}
	// {{{ private properties

	/**
	 * Locale-based formatting information
	 *
	 * @var array
	 * @see SwatMoneyEntry::getFormattingInformation()
	 */
	private $formatting_information;

	// }}}
	// {{{ public function display()

	/**
	 * Displays this money entry widget
	 *
	 * The widget is displayed as an input box and an optional monetary unit.
	 */
	public function display()
	{
		parent::display();

		if ($this->display_currency) {
			$lc = $this->getFormattingInformation();
			// C99 specification includes spacing character, remove it
			$currency = substr($lc['int_curr_symbol'], 0, 3);
			echo SwatString::minimizeEntities(' '.$currency);
		}
	}

	// }}}
	// {{{ public function process()

	/**
	 * Processes this money entry widget
	 *
	 * If the value of this widget is not a monetary value or the number of
	 * fractional decimal places is not within the allowed range, an error
	 * message is attached to this money entry widget.
	 */
	public function process()
	{
		parent::process();

		if ($this->value === null)
			return;

		$lc = $this->getFormattingInformation();

		$max_decimal_places = ($this->decimal_places === null) ?
			$lc['int_frac_digits'] : $this->decimal_places;

		// get the number of fractional decimal places
		$decimal_position = strpos((string)$this->value, '.');
		$decimal_places = ($decimal_position === false) ?
			0 : strlen((string)$this->value) - $decimal_position - 1;

		// check if length of the given fractional part is more than the
		// allowed length
		if ($decimal_places > $max_decimal_places) {
			if ($this->decimal_places === null) {
				$message =
					$this->getValidationMessage('currency-decimal-places');

				$max_decimal_places_formatted = str_replace('%', '%%',
					SwatString::numberFormat($max_decimal_places));

				// C99 specification includes spacing character, remove it
				$currency = str_replace('%', '%%',
					substr($lc['int_curr_symbol'], 0, 3));

				$message->primary_content = sprintf($message->primary_content,
					$currency_formatted,
					$max_decimal_places_formatted);
			} else {
				if ($max_decimal_places === 0) {
					$message = $this->getValidationMessage('no-decimal-places');
				} else {
					$max_decimal_places_formatted = str_replace('%', '%%',
						SwatString::numberFormat($max_decimal_places));

					// note: not using getValidationMessage() because of
					// ngettext. We may want to add this ability to that method
					$message = new SwatMessage(sprintf(Swat::ngettext(
						'The %%s field has too many decimal places. There '.
						'can be at most one decimal place.',
						'The %%s field has too many decimal places. There '.
						'can be at most %s decimal places.',
						$max_decimal_places), $max_decimal_places_formatted),
						SwatMessage::ERROR);
				}
			}

			$this->addMessage($message);
		}
	}

	// }}}
	// {{{ protected function getDisplayValue()

	/**
	 * Formats a monetary value to display
	 *
	 * @param string $value the value to format for display.
	 *
	 * @return string the formatted value.
	 */
	protected function getDisplayValue($value)
	{
		// if the value is valid, format accordingly
		if (!$this->hasMessage() && is_numeric($value))
			$value = SwatString::moneyFormat($value, $this->locale,
				false, $this->decimal_places);

		return $value;
	}

	// }}}
	// {{{ protected function getNumericValue()

	/**
	 * Gets the numeric value of this money entry
	 *
	 * @param string $value the raw value to use to get the numeric value.
	 *
	 * @return mixed the numeric value of this money entry widget or null if no
	 *                numeric value is available.
	 */
	protected function getNumericValue($value)
	{
		$lc = $this->getFormattingInformation();

		$replace = array(
			$lc['int_curr_symbol']   => '',
			$lc['currency_symbol']   => '',
			$lc['mon_decimal_point'] => '.',
			$lc['mon_thousands_sep'] => '',
		);

		$value = str_replace(
			array_keys($replace), array_values($replace), $value);

		return parent::getNumericValue($value);
	}

	// }}}
	// {{{ protected function getValidationMessage()

	/**
	 * Gets a validation message for this money entry widget
	 *
	 * @see SwatEntry::getValidationMessage()
	 * @param string $id the string identifier of the validation message.
	 *
	 * @return SwatMessage the validation message.
	 */
	protected function getValidationMessage()
	{
		$lc = $this->getFormattingInformation();

		switch ($id) {
		case 'float':
			// C99 specification includes spacing character, remove it
			$currency = substr($lc['int_curr_symbol'], 0, 3),
			$example = SwatString::moneyFormat(1036.95, $this->locale);
			$message = new SwatMessage(sprintf(Swat::_(
				'The %%s field must be a monetary value '.
				'formatted for %s (i.e. %s).'),
				str_replace('%', '%%', $currency),
				str_replace('%', '%%', $example)),
				SwatMessage::ERROR);

			break;
		case 'currency-decimal-places':
			$message = new SwatMessage(Swat::_(
				'The %%s field has too many decimal places. The '.
				'currency %s only allows %s.'),
				SwatMessage::ERROR);

			break;
		case 'no-decimal-places':
			$message = new SwatMessage(
				Swat::_('The %s field must not have any decimal places.'),
				SwatMessage::ERROR);

			break;
		default:
			$message = parent::getValidationMessage($id);
			break;
		}

		return $message;
	}

	// }}}
	// {{{ protected final function getFormattingInformation()

	/**
	 * Gets locale-based formatting information for the locale of this money
	 * entry widget
	 *
	 * Strings in lcoale information are returned in UTF-8 no matter what
	 * locale is used.
	 *
	 * @return array an array of locale-based formatting information for the
	 *                locale of this money entry widget.
	 *
	 * @throws SwatException if the locale specified for this money entry
	 *                       widget is not valid for the operating system.
	 */
	protected final function &getFormattingInformation()
	{
		if ($this->formatting_information === null) {
			if ($this->locale !== null) {
				$locale = setlocale(LC_ALL, 0);
				if (setlocale(LC_ALL, $this->locale) === false) {
					throw new SwatException(sprintf('Locale %s used in '.
						'SwatMoneyEntry is not valid for this operating '.
						'system.', $this->locale));
				}
			}

			$lc = localeconv();

			$character_set = nl_langinfo(CODESET);
			if ($this->locale !== null)
				setlocale(LC_ALL, $locale);

			// convert locale formatting information to UTF-8
			if ($character_set !== 'UTF-8')
				$lc = $this->iconvArray($character_set, 'UTF-8', $lc);

			$this->formatting_information = $lc;
		}

		return $this->formatting_information;
	}

	// }}}
	// {{{ private function iconvArray()

	/**
	 * Recursivly converts character set of strings in an array
	 *
	 * This is used to convert the formatting information array for a given
	 * locale into UFT-8.
	 *
	 * @param string $from the character set to convert from.
	 * @param string $to the character set to convert to.
	 * @param array $array the array to convert.
	 *
	 * @return array a new array with all strings recursivly converted to the
	 *                given character set.
	 *
	 * @throws SwatException if any component of the array can not be converted
	 *                       from the <i>$from</i> character set to the
	 *                       <i>$to</i> character set.
	 */
	private function iconvArray($from, $to, array $array)
	{
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$array[$key] = $this->iconvArray($from, $to, $value);
			} elseif (is_string($value)) {
				$output = iconv($from, $to, $value);
				if ($output === false)
					throw new SwatException(sprintf('Could not convert '.
						'%s output to %s', $from, $to));

				$array[$key] = $output;
			}
		}

		return $array;
	}

	// }}}
}

?>
