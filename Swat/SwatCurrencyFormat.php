<?php

require_once 'Swat/SwatObject.php';

/**
 * Information for formatting currency values
 *
 * @package   Swat
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       SwatLocale::formatCurrency()
 * @see       SwatLocale::getCurrencyFormat()
 */
class SwatCurrencyFormat extends SwatObject
{
	// {{{ public properties

	/**
	 * Number of fractional digits
	 *
	 * @var integer
	 */
	public $fractional_digits;

	/**
	 * Whether or not currency symbol preceds a positive value
	 *
	 * True if <code>$symbol</code> precedes a positive value, false if it
	 * succeeds one.
	 *
	 * @var boolean
	 */
	public $p_cs_precedes;

	/**
	 * Whether or not currency symbol preceds a negative value
	 *
	 * True if <code>$symbol</code> precedes a negative value, false if it
	 * succeeds one.
	 *
	 * @var boolean
	 */
	public $n_cs_precedes;

	/**
	 * Whether or not currency symbol is separated by space for positive values
	 *
	 * True if a space separates <code>$symbol</code> from a positive value,
	 * false otherwise.
	 *
	 * @var boolean
	 */
	public $p_separate_by_space;

	/**
	 * Whether or not currency symbol is separated by space for negative values
	 *
	 * True if a space separates <code>$symbol</code> from a negative value,
	 * false otherwise.
	 *
	 * @var boolean
	 */
	public $n_separate_by_space;

	/**
	 * Positive sign position
	 *
	 * <pre>
	 * 0 - Parentheses surround the quantity and currency_symbol
	 * 1 - The sign string precedes the quantity and currency_symbol
	 * 2 - The sign string succeeds the quantity and currency_symbol
	 * 3 - The sign string immediately precedes the currency_symbol
	 * 4 - The sign string immediately succeeds the currency_symbol
	 * </pre>
	 *
	 * @var integer
	 */
	public $p_sign_position;

	/**
	 * Negative sign position
	 *
	 * <pre>
	 * 0 - Parentheses surround the quantity and currency_symbol
	 * 1 - The sign string precedes the quantity and currency_symbol
	 * 2 - The sign string succeeds the quantity and currency_symbol
	 * 3 - The sign string immediately precedes the currency_symbol
	 * 4 - The sign string immediately succeeds the currency_symbol
	 * </pre>
	 *
	 * @var integer
	 */
	public $n_sign_position;

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
	 * Currency symbol
	 *
	 * @var string
	 */
	public $symbol;

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
	 * Overrides values of this currency format with another currency format
	 *
	 * Only non-null values of the new format are overridden on this format.
	 * For example, it is possible to override just the currency symbol by
	 * creating a new currency formatting object that contains only the symbol
	 * and passing the new object to this format's override() method.
	 *
	 * @param SwatCurrencyFormat $format the format with which to override this
	 *                                    format.
	 */
	public function override(SwatCurrencyFormat $format)
	{
		if ($format->fractional_digits !== null)
			$this->fractional_digits = $format->fractional_digits;

		if ($format->p_cs_precedes !== null)
			$this->p_cs_precedes = $format->p_cs_precedes;

		if ($format->n_cs_precedes !== null)
			$this->n_cs_precedes = $format->n_cs_precedes;

		if ($format->p_separate_by_space !== null)
			$this->p_separate_by_space = $format->p_separate_by_space;

		if ($format->n_separate_by_space !== null)
			$this->n_separate_by_space = $format->n_separate_by_space;

		if ($format->p_sign_position !== null)
			$this->p_sign_position = $format->p_sign_position;

		if ($format->n_sign_position !== null)
			$this->n_sign_position = $format->n_sign_position;

		if ($format->decimal_separator !== null)
			$this->decimal_separator = $format->decimal_separator;

		if ($format->thousands_separator !== null)
			$this->thousands_separator = $format->thousands_separator;

		if ($format->symbol !== null)
			$this->symbol = $format->symbol;

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

		$string.= 'p_cs_precedes => ';
		$string.= ($this->p_cs_precedes) ? 'true' : 'false';
		$string.= "\n";

		$string.= 'n_cs_precedes => ';
		$string.= ($this->n_cs_precedes) ? 'true' : 'false';
		$string.= "\n";

		$string.= 'p_separate_by_space => ';
		$string.= ($this->p_separate_by_space) ? 'true' : 'false';
		$string.= "\n";

		$string.= 'n_separate_by_space => ';
		$string.= ($this->n_separate_by_space) ? 'true' : 'false';
		$string.= "\n";

		$string.= 'p_sign_position => '.$this->p_sign_position."\n";

		$string.= 'n_sign_position => '.$this->n_sign_position."\n";

		$string.= 'decimal_separator => '.$this->decimal_separator."\n";

		$string.= 'thousands_separator => '.$this->thousands_separator."\n";

		$string.= 'symbol => '.$this->symbol."\n";

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
