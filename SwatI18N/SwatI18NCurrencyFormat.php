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
     */
    public int $fractional_digits = 2;

    /**
     * Whether the currency symbol precedes a positive value.
     *
     * True if `$symbol` precedes a positive value, false if it succeeds one.
     */
    public bool $p_cs_precedes = true;

    /**
     * Whether the currency symbol precedes a negative value.
     *
     * True if `$symbol` precedes a negative value, false if it succeeds one.
     */
    public bool $n_cs_precedes = true;

    /**
     * Whether the currency symbol is separated by space for positive values.
     *
     * True if a space separates `$symbol` from a positive value, false otherwise.
     *
     * @var bool
     */
    public $p_separate_by_space = false;

    /**
     * Whether the currency symbol is separated by space for negative values.
     *
     * True if a space separates `$symbol` from a negative value, false otherwise.
     */
    public bool $n_separate_by_space = false;

    /**
     * Positive sign position.
     *
     * - `0` - Parentheses surround the quantity and currency_symbol
     * - `1` - The sign string precedes the quantity and currency_symbol
     * - `2` - The sign string succeeds the quantity and currency_symbol
     * - `3` - The sign string immediately precedes the currency_symbol
     * - `4` - The sign string immediately succeeds the currency_symbol
     *
     * @var int<0,4>
     */
    public int $p_sign_position = 1;

    /**
     * Negative sign position.
     *
     * - `0` - Parentheses surround the quantity and currency_symbol
     * - `1` - The sign string precedes the quantity and currency_symbol
     * - `2` - The sign string succeeds the quantity and currency_symbol
     * - `3` - The sign string immediately precedes the currency_symbol
     * - `4` - The sign string immediately succeeds the currency_symbol
     *
     * @var int<0,4>
     */
    public int $n_sign_position = 1;

    /**
     * Positive sign.
     */
    public string $p_sign = '';

    /**
     * Negative sign.
     */
    public string $n_sign = '-';

    /**
     * Currency symbol.
     */
    public string $symbol = '';
}
