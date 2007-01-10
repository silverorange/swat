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
			$locale = $this->setLocale($this->locale);
			$lc = localeconv();
			echo SwatString::minimizeEntities(' '.$lc['int_curr_symbol']);
			$this->setLocale($locale);
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

		$locale = $this->setLocale($this->locale);
		$lc = localeconv();
		$this->setLocale($locale);

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
	// {{{ public function setLocale()

	/**
	 * Sets the locale
	 *
	 * This is used to get locale specific monetary information. After setting
	 * the locale, remember to set the locale back to the default.
	 *
	 * @param string $locale the locale to set.
	 *
	 * @return string the old locale.
	 */
	private function setLocale($locale)
	{
		if ($locale !== null) {
			if (strpos($locale, '.') === false)
				$locale .= '.UTF-8';

			$locale = setlocale(LC_MONETARY, $locale);
		}

		return $locale;
	}

	// }}}
}

?>
