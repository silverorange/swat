<?php

/**
 * Information for formatting currency values.
 *
 * @copyright 2007-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see       SwatLocale::formatCurrency()
 * @see       SwatLocale::getCurrencyFormat()
 */
class SwatI18NCurrencyFormat extends SwatI18NNumberFormat
{
    /**
     * Number of fractional digits.
     *
     * @var int
     */
    public $fractional_digits;

    /**
     * Whether or not currency symbol preceds a positive value.
     *
     * True if <code>$symbol</code> precedes a positive value, false if it
     * succeeds one.
     *
     * @var bool
     */
    public $p_cs_precedes;

    /**
     * Whether or not currency symbol preceds a negative value.
     *
     * True if <code>$symbol</code> precedes a negative value, false if it
     * succeeds one.
     *
     * @var bool
     */
    public $n_cs_precedes;

    /**
     * Whether or not currency symbol is separated by space for positive values.
     *
     * True if a space separates <code>$symbol</code> from a positive value,
     * false otherwise.
     *
     * @var bool
     */
    public $p_separate_by_space;

    /**
     * Whether or not currency symbol is separated by space for negative values.
     *
     * True if a space separates <code>$symbol</code> from a negative value,
     * false otherwise.
     *
     * @var bool
     */
    public $n_separate_by_space;

    /**
     * Positive sign position.
     *
     * <pre>
     * 0 - Parentheses surround the quantity and currency_symbol
     * 1 - The sign string precedes the quantity and currency_symbol
     * 2 - The sign string succeeds the quantity and currency_symbol
     * 3 - The sign string immediately precedes the currency_symbol
     * 4 - The sign string immediately succeeds the currency_symbol
     * </pre>
     *
     * @var int
     */
    public $p_sign_position;

    /**
     * Negative sign position.
     *
     * <pre>
     * 0 - Parentheses surround the quantity and currency_symbol
     * 1 - The sign string precedes the quantity and currency_symbol
     * 2 - The sign string succeeds the quantity and currency_symbol
     * 3 - The sign string immediately precedes the currency_symbol
     * 4 - The sign string immediately succeeds the currency_symbol
     * </pre>
     *
     * @var int
     */
    public $n_sign_position;

    /**
     * Positive sign.
     *
     * @var string
     */
    public $p_sign;

    /**
     * Negative sign.
     *
     * @var string
     */
    public $n_sign;

    /**
     * Currency symbol.
     *
     * @var string
     */
    public $symbol;

    /**
     * Gets a string representation of this format.
     *
     * @return string a string representation of this format
     */
    public function __toString(): string
    {
        $string = parent::__toString();

        $string .= 'fractional_digits => ' . $this->fractional_digits . "\n";

        $string .= 'p_cs_precedes => ';
        $string .= $this->p_cs_precedes ? 'true' : 'false';
        $string .= "\n";

        $string .= 'n_cs_precedes => ';
        $string .= $this->n_cs_precedes ? 'true' : 'false';
        $string .= "\n";

        $string .= 'p_separate_by_space => ';
        $string .= $this->p_separate_by_space ? 'true' : 'false';
        $string .= "\n";

        $string .= 'n_separate_by_space => ';
        $string .= $this->n_separate_by_space ? 'true' : 'false';
        $string .= "\n";

        $string .= 'p_sign_position => ' . $this->p_sign_position . "\n";

        $string .= 'n_sign_position => ' . $this->n_sign_position . "\n";

        $string .= 'p_sign => ' . $this->p_sign . "\n";

        $string .= 'n_sign => ' . $this->n_sign . "\n";

        $string .= 'symbol => ' . $this->symbol . "\n";

        return $string;
    }
}
