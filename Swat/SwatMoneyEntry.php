<?php

require_once 'Swat/SwatEntry.php';

/**
 * A money entry widget
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatMoneyEntry extends SwatEntry
{
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

	/**
	 * Initializes this widget
	 *
	 * Sets the input size to 15 by default.
	 */
	public function init()
	{
		$this->size = 15;
	}


	/**
	 * Displays this money entry widget
	 *
	 * The widget is displayed as an input box and an optional monetary unit.
	 */
	public function display()
	{
		if (!$this->visible)
			return

		$locale = $this->setLocale($this->locale);
		$lc = localeconv();

		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'text';
		$input_tag->name = $this->id;
		$input_tag->id = $this->id;
		$input_tag->onfocus = 'this.select();';

		if ($this->value !== null) {
			if (is_float($this->value))
				$input_tag->value = money_format('%n', $this->value);
			else
				$input_tag->value = $this->value;
		}

		if ($this->size !== null)
			$input_tag->size = $this->size;

		if ($this->maxlength !== null)
			$input_tag->maxlength = $this->maxlength;

		$input_tag->display();

		if ($this->display_currency)
			echo ' '.$lc['int_curr_symbol'];

		$this->setLocale($locale);
	}

	/**
	 * Checks to make sure value is a monetary value
	 *
	 * If the value of this widget is not a monetary value then an error
	 * message is attached to this widget.
	 */
	public function process()
	{
		parent::process();

		$locale = $this->setLocale($this->locale);
		$lc = localeconv();
		$lc_frac = $lc['int_frac_digits'];

		// change all locale formatting to numeric formatting
		$remove_parts = array($lc['int_curr_symbol'] => '',
			$lc['currency_symbol'] => '',
			$lc['mon_thousands_sep'] => '',
			$lc['mon_decimal_point'] => '.');

		$value = str_replace(array_keys($remove_parts),
			array_values($remove_parts), $this->value);

		if (is_numeric($value)) {
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

		} else {
			$msg = Swat::_('The %s field must be a monetary value '.
				'formatted for %s (i.e. %s).');

			// substitute in '%s' because a second substitution is done
			// with the form field title
			$msg = sprintf($msg,
				'%s',
				$lc['int_curr_symbol'],
				money_format('%n', 1000.95));

			$this->addMessage(new SwatMessage($msg, SwatMessage::ERROR));
		}

		// reset locale for this request
		$this->setLocale($locale);
	}

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
}

?>
