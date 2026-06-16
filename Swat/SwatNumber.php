<?php

/**
 * Number tools.
 *
 * @copyright 2008-2026 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatNumber extends SwatObject
{
    /**
     * Rounds a number to the specified number of fractional digits using the
     * round-half-up rounding method.
     *
     * See {@link http://en.wikipedia.org/wiki/Rounding#Round_half_up}.
     *
     * The home-grown implementation (while working) is replaced with the
     * native PHP {@link http://php.net/manual/en/function.round.php round()}
     * function, using the PHP_ROUND_HALF_UP constant.
     *
     * @param float|int $value             the value to round
     * @param int       $fractional_digits the number of fractional digits in the
     *                                     rounded result
     *
     * @return float the rounded value
     */
    public static function roundUp(
        float|int $value,
        int $fractional_digits
    ): float {
        return round($value, $fractional_digits, PHP_ROUND_HALF_UP);
    }

    /**
     * Rounds a number to the specified number of fractional digits using the
     * round-to-even rounding method.
     *
     * Round-to-even is primarily used for monetary values. See
     * {@link http://en.wikipedia.org/wiki/Rounding#Round_half_to_even}.
     *
     * Historically, our home-grown implementation of this method did not work,
     * especially in cases with zero fractional digits. e.g.:
     *
     * ```php
     * // outputs 1, but should be 2
     * echo SwatNumber::roundToEven(1.5, 0);
     * ```
     *
     * The home-grown implementation is replaced with the native PHP
     * {@link http://php.net/manual/en/function.round.php round()}
     * function using the PHP_ROUND_HALF_EVEN constant.
     *
     * @param float|int $value             the value to round
     * @param int       $fractional_digits the number of fractional digits in the
     *                                     rounded result
     *
     * @return float the rounded value
     */
    public static function roundToEven(
        float|int $value,
        int $fractional_digits
    ): float {
        return round($value, $fractional_digits, PHP_ROUND_HALF_EVEN);
    }

    /**
     * Formats an integer as an ordinal number (1st, 2nd, 3rd, etc.).
     *
     * The ICU number formatter and string normalizers from the `intl` extension
     * are used to get a correctly formatted ordinal for the current locale.
     *
     * @param int $value the numeric value to format
     *
     * @return string the ordinal-formatted value
     */
    public static function ordinal(int $value): string
    {
        // get current locale
        $locale = setlocale(LC_ALL, '0');

        static $formatters = [];

        if (!isset($formatters[$locale])) {
            $formatters[$locale] = new NumberFormatter(
                $locale,
                NumberFormatter::ORDINAL,
            );
        }

        // format ordinal
        $ordinal_value = $formatters[$locale]->format($value);

        // Decompose to latin-1 characters (removes superscripts)
        return Normalizer::normalize(
            $ordinal_value,
            Normalizer::FORM_KC
        );
    }

    /**
     * Don't allow instantiation of the SwatNumber object.
     *
     * This class contains only static methods and should not be instantiated.
     */
    private function __construct() {}
}
