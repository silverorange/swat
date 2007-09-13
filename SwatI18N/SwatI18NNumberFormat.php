<?php

require_once 'Swat/SwatObject.php';

/**
 * Information for formatting numeric values
 *
 * @package   SwatI18N
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       SwatLocale::formatNumber()
 * @see       SwatLocale::getNumberFormat()
 */
class SwatI18NNumberFormat extends SwatObject
{
	// {{{ public properties

	/**
	 * Number of fractional digits
	 *
	 * @var integer
	 */
	public $fractional_digits;

	/**
	 * Decimal point character
	 *
	 * @var string
	 */
	public $decimal_separator;

	/**
	 * Thousands separator
	 *
	 * @var string
	 */
	public $thousands_separator;

	/**
	 * Numeric groupings
	 *
	 * @var array
	 */
	public $grouping;

	/**
	 * Positive sign
	 *
	 * @var string
	 */
	public $p_sign;

	/**
	 * Negative sign
	 *
	 * @var string
	 */
	public $n_sign;

	// }}}
	// {{{ public function override()

	/**
	 * Overrides values of this number format with another number format
	 *
	 * Only non-null values of the new format are overridden on this format.
	 * For example, it is possible to override just the negative sign by
	 * creating a new number formatting object that contains only the sign
	 * and passing the new object to this format's override() method.
	 *
	 * @param SwatI18NNumberFormat $format the format with which to override
	 *                                      this format.
	 */
	public function override(SwatI18NNumberFormat $format)
	{
		if ($format->fractional_digits !== null)
			$this->fractional_digits = $format->fractional_digits;

		if ($format->decimal_separator !== null)
			$this->decimal_separator = $format->decimal_separator;

		if ($format->thousands_separator !== null)
			$this->thousands_separator = $format->thousands_separator;

		if ($format->grouping !== null)
			$this->grouping = $format->grouping;

		if ($format->p_sign !== null)
			$this->p_sign = $format->p_sign;

		if ($format->n_sign !== null)
			$this->n_sign = $format->n_sign;
	}

	// }}}
	// {{{ public function __toString()

	/**
	 * Gets a string representation of this format
	 *
	 * @return string a string representation of this format.
	 */
	public function __toString()
	{
		$string = '';

		$string.= 'fractional_digits => '.$this->fractional_digits."\n";

		$string.= 'decimal_separator => '.$this->decimal_separator."\n";

		$string.= 'thousands_separator => '.$this->thousands_separator."\n";

		$string.= 'grouping => ';
		$string.= (is_array($this->grouping)) ?
			implode(', ', $this->grouping) : $this->grouping;

		$string.= "\n";

		$string.= 'p_sign => '.$this->p_sign."\n";

		$string.= 'n_sign => '.$this->n_sign."\n";

		return $string;
	}

	// }}}
}

?>
