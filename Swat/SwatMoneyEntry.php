<?php

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
	 * Whether to display currency unit
	 *
	 * If true, displays the international currency unit
	 *
	 * @var boolean
	 */
	public $display_currency = false;

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
		$locale = $this->setLocale($this->locale);
		$lc = localeconv();

		parent::display();

		if ($this->display_currency)
			echo SwatString::minimizeEntities(' '.$lc['int_curr_symbol']);

		$this->setLocale($locale);
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
		$lc_frac = $lc['int_frac_digits'];

		$replace = array($lc['int_curr_symbol'] => '',
				$lc['currency_symbol'] => '',
				$lc['mon_decimal_point'] => '.',
				$lc['mon_thousands_sep'] => '');

		$value = str_replace(array_keys($replace), array_values($replace), $this->value);

		$value = SwatString::toFloat($value);

		if ($value === null) {
			$msg = Swat::_('The %s field must be a monetary value '.
				'formatted for %s (i.e. %s).');

			// substitute in '%s' because a second substitution is done
			// with the form field title
			$msg = sprintf($msg,
				'%s',
				$lc['int_curr_symbol'],
				money_format('%n', 1000.95));

			$this->addMessage(new SwatMessage($msg, SwatMessage::ERROR));

		} else {

			$frac_pos = strpos($value, '.');

			// check if length of the given fractional part is more than the
			// allowed length
			if ($frac_pos !== false &&
				strlen(substr($value, $frac_pos + 1)) > $lc_frac) {

				$msg = Swat::_('The %s field has too many decimal values. '.
					'The currency (%s) only allows %s.');

				// substitute in '%s' because a second substitution is done
				// with the form field title
				$msg = sprintf($msg,
					'%s',
					rtrim($lc['int_curr_symbol']),
					$lc['int_frac_digits']);

				$this->addMessage(
					new SwatMessage($msg, SwatMessage::ERROR));
			} else {
				$this->value = (float) $value; 
			}
		}

		// reset locale for this request
		$this->setLocale($locale);
	}

	// }}}
	// {{{ public function getDisplayValue()

	protected function getDisplayValue()
	{
		$locale = $this->setLocale($this->locale);
		$lc = localeconv();

		if (is_numeric($this->value))
			$value = money_format('%n', $this->value);
		else
			$value =  $this->value;

		$this->setLocale($locale);

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
		if ($locale !== null)
			$locale = setlocale(LC_MONETARY, $locale);

		return $locale;
	}

	// }}}
}

?>
