<?php

/**
 * Information for formatting currency values.
 *
 * @copyright 2007-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see       SwatLocale::formatCurrency()
 * @see       SwatLocale::getCurrencyFormat()
 *
 * @phpstan-import-type LocaleConvArray from SwatI18NLocale
 * @phpstan-import-type TOverrideableNumberProperties from SwatI18NNumberFormat
 *
 * @phpstan-type TOverrideableCurrencyProperties array{
 *     fractional_digits?: int,
 *     p_cs_precedes?: bool,
 *     n_cs_precedes?: bool,
 *     p_separate_by_space?: bool,
 *     n_separate_by_space?: bool,
 *     p_sign_position?: self::SIGN_POSITION_*,
 *     n_sign_position?: self::SIGN_POSITION_*,
 *     p_sign?: string,
 *     n_sign?: string,
 *     symbol?: string,
 * }
 */
class SwatI18NCurrencyFormat extends SwatI18NNumberFormat
{
    /**
     * Sign position constants.
     *
     * @see https://www.php.net/manual/en/function.localeconv.php
     */

    // Parentheses surround the quantity and currency_symbol.
    final public const SIGN_POSITION_PARENTHESES = 0;

    // The sign string precedes the quantity and currency_symbol.
    final public const SIGN_POSITION_BEFORE = 1;

    // The sign string succeeds the quantity and currency_symbol.
    final public const SIGN_POSITION_AFTER = 2;

    // The sign string immediately precedes the currency_symbol.
    final public const SIGN_POSITION_BEFORE_SYMBOL = 3;

    // The sign string immediately succeeds the currency_symbol.
    final public const SIGN_POSITION_AFTER_SYMBOL = 4;

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
     */
    public bool $p_separate_by_space = false;

    /**
     * Whether the currency symbol is separated by space for negative values.
     *
     * True if a space separates `$symbol` from a negative value, false otherwise.
     */
    public bool $n_separate_by_space = false;

    /**
     * Positive sign position.
     *
     * @var self::SIGN_POSITION_*
     */
    public int $p_sign_position = 1;

    /**
     * Negative sign position.
     *
     * @var self::SIGN_POSITION_*
     */
    public int $n_sign_position = 1;

    /**
     * Positive sign.
     */
    public string $p_sign = '';

    /**
     * Negative sign. This defaults to the Unicode Minus Sign (U+2212).
     */
    public string $n_sign = '−';

    /**
     * Currency symbol.
     */
    public string $symbol = '';

    /**
     * @param LocaleConvArray $lc
     */
    public static function buildFromLocale(array $lc, bool $international = false): static
    {
        // build the number format first
        $format = parent::buildFromLocale($lc);

        // then set the currency properties
        $format->fractional_digits = $international
            ? $lc['int_frac_digits']
            : $lc['frac_digits'];

        $format->p_cs_precedes = $lc['p_cs_precedes'] === CHAR_MAX
            ? true
            : boolval($lc['p_cs_precedes']);

        $format->n_cs_precedes = $lc['n_cs_precedes'] === CHAR_MAX
            ? true
            : boolval($lc['n_cs_precedes']);

        $format->p_separate_by_space = $lc['p_sep_by_space'] === CHAR_MAX
            ? false
            : boolval($lc['p_sep_by_space']);

        $format->n_separate_by_space = $lc['n_sep_by_space'] === CHAR_MAX
            ? false
            : boolval($lc['n_sep_by_space']);

        $format->p_sign_position = $lc['p_sign_posn'] === CHAR_MAX
            ? 1
            : $lc['p_sign_posn'];

        $format->n_sign_position = $lc['n_sign_posn'] === CHAR_MAX
            ? 1
            : $lc['n_sign_posn'];

        $format->p_sign = $lc['positive_sign'];

        $format->n_sign = $lc['negative_sign'];

        $format->symbol = $international
            ? $lc['int_curr_symbol']
            : $lc['currency_symbol'];

        return $format;
    }

    /**
     * Overrides the default format only to be able to specify the extended
     * set of allowable keys in the array.
     *
     * @param TOverrideableCurrencyProperties&TOverrideableNumberProperties $format
     */
    public function override(array $format): static
    {
        return parent::override($format);
    }
}
