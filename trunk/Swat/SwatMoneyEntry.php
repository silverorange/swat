<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatString.php';
require_once 'Swat/SwatEntry.php';

/**
 * A money entry widget
 *
 * @package   Swat
 * @copyright 2004-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatMoneyEntry extends SwatEntry
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
	// {{{ protected properties

	/**
	 * Locale-based formatting information
	 *
	 * @var array
	 * @see SwatMoneyEntry::getFormattingInformation()
	 */
	protected $formatting_information;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new money entry widget
	 *
	 * Sets the input size to 15 by default.
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->size = 10;
	}

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
			echo SwatString::minimizeEntities(' '.$lc['int_curr_symbol']);
		}
	}

	// }}}
	// {{{ public function process()

	/**
	 * Checks to make sure value is a monetary value
	 *
	 * If the value of this widget is not a monetary value then an error
	 * message is attached to this widget.
	 */
	public function process()
	{
		parent::process();

		if ($this->value === null)
			return;

		$lc = $this->getFormattingInformation();

		$replace = array(
			$lc['int_curr_symbol']   => '',
			$lc['currency_symbol']   => '',
			$lc['mon_decimal_point'] => '.',
			$lc['mon_thousands_sep'] => '',
		);

		$value = str_replace(
			array_keys($replace), array_values($replace), $this->value);

		$value = SwatString::toFloat($value);

		if ($value === null) {
			$message = Swat::_('The %%s field must be a monetary value '.
				'formatted for %s (i.e. %s).');

			$message = sprintf($message,
				$lc['int_curr_symbol'],
				SwatString::moneyFormat(1000.95, $this->locale));

			$this->addMessage(new SwatMessage($message, SwatMessage::ERROR));
		} else {
			$max_decimal_places = ($this->decimal_places === null) ?
				$lc['int_frac_digits'] : $this->decimal_places;

			// get the number of fractional decimal places
			$decimal_position = strpos((string)$value, '.');
			$decimal_places = ($decimal_position === false) ?
				0 : strlen((string)$value) - $decimal_position - 1;

			// check if length of the given fractional part is more than the
			// allowed length
			if ($decimal_places > $max_decimal_places) {
				if ($this->decimal_places === null) {
					$message = Swat::_(
						'The %%s field has too many decimal places. The '.
						'currency %s only allows %s.');

					$message = sprintf($message,
						// TODO: why is this rtrim here?
						rtrim($lc['int_curr_symbol']),
						$max_decimal_places);
				} else {
					if ($max_decimal_places === 0) {
						$message = Swat::_(
							'The %s field must not have any decimal places.');
					} else {
						$message = Swat::ngettext(
							'The %%s field has too many decimal places. There '.
							'can be at most one decimal place.',
							'The %%s field has too many decimal places. There '.
							'can be at most %s decimal places.',
							$max_decimal_places);

						$message = sprintf($message, $max_decimal_places);
					}
				}

				$this->addMessage(
					new SwatMessage($message, SwatMessage::ERROR));
			} else {
				$this->value = $value; 
			}
		}
	}

	// }}}
	// {{{ public function getDisplayValue()

	protected function getDisplayValue()
	{
		// show what the user entered if it does not validate
		if (!$this->hasMessage() && is_numeric($this->value))
			$value = SwatString::moneyFormat($this->value, $this->locale,
				false, $this->decimal_places);
		else
			$value =  $this->value;

		return $value;
	}

	// }}}
	// {{{ protected function getFormattingInformation()

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
	protected function getFormattingInformation()
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
