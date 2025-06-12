<?php

/**
 * A locale object.
 *
 * Locale objects are used to format and parse values according to locale-
 * specific rules.
 *
 * @copyright 2007-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatI18NLocale extends SwatObject
{
    // {{{ protected properties

    /**
     * The locale string or array specified in the constructor for this locale.
     *
     * @var array|string
     */
    protected $locale;

    /**
     * The locale info array of this locale as provided by localeconv().
     *
     * @var array
     */
    protected $locale_info;

    /**
     * The preferred locale as selected by the operating system if the
     * {@link SwatI18NLocale::$locale} property is an array.
     *
     * @var string
     */
    protected $preferred_locale;

    /**
     * The number format used by this locale.
     *
     * @var SwatI18NNumberFormat
     */
    protected $number_format;

    /**
     * The national currency format used by this locale.
     *
     * @var SwatI18NCurrencyFormat
     */
    protected $national_currency_format;

    /**
     * The international currency format used by this locale.
     *
     * @var SwatI18NCurrencyFormat
     */
    protected $international_currency_format;

    /**
     * The previous locales indexed by the lc-type constant used to set the
     * locale.
     *
     * This is used by the {@link SwatI18NLocale::set()} and
     * {@link SwatI18NLocale::reset()} methods to reset the locale back to the
     * previous value.
     *
     * @var array
     */
    protected $old_locale_by_category = [];

    // }}}
    // {{{ private properties

    /**
     * Cache of existing locale objects.
     *
     * This is an array of SwatI18NLocale objects indexed by the preferred
     * locale for this operating system.
     *
     * @var array
     *
     * @see SwatI18NLocale::get()
     */
    private static $locales = [];

    // }}}
    // {{{ public static function get()

    /**
     * Gets a locale object.
     *
     * @param array|string $locale the locale identifier of this locale object.
     *                             If the locale is not valid for the current
     *                             operating system, an exception is thrown.
     *                             If no locale is specified, the current
     *                             locale is used. Multiple locale identifiers
     *                             may be specified in an array. In this case,
     *                             the first valid locale is used.
     *
     * @return SwatI18NLocale a locale object for the requested <i>$locale</i>
     *
     * @throws SwatException if the specified <i>$locale</i> is not valid for
     *                       the current operating system
     */
    public static function get($locale = null)
    {
        $locale_object = null;

        if ($locale === null) {
            $locale_key = self::setlocale(LC_ALL, '0');
            if (array_key_exists($locale_key, self::$locales)) {
                $locale_object = self::$locales[$locale_key];
            }
        } elseif (is_array($locale)) {
            foreach ($locale as $locale_key) {
                if (array_key_exists($locale_key, self::$locales)) {
                    $locale_object = self::$locales[$locale_key];
                    break;
                }
            }
        } else {
            if (array_key_exists($locale, self::$locales)) {
                $locale_object = self::$locales[$locale];
            }
        }

        if ($locale_object === null) {
            $locale_object = new SwatI18NLocale($locale);
            if ($locale === null) {
                $locale_key = $locale_object->__toString();
                self::$locales[$locale_key] = $locale_object;
            } elseif (is_array($locale)) {
                foreach ($locale as $locale_key) {
                    self::$locales[$locale_key] = $locale_object;
                }
            } else {
                self::$locales[$locale] = $locale_object;
            }
        }

        return $locale_object;
    }

    // }}}
    // {{{ public static function setlocale()

    /**
     * Sets the current locale.
     *
     * This is a wrapper for the system setlocale() function that provides
     * extra compatibility.
     *
     * @param int          $category optional. The lc-type constant specifying the
     *                               category of functions affected by setting
     *                               the system locale.
     * @param array|string $locale   the locale identifier. Use '0' to return
     *                               the current system locale. Multiple locale
     *                               identifiers may be specified in an array.
     *                               In this case, the first valid locale is
     *                               used.
     *
     * @return bool|string the new or current locale, or false if an invalid
     *                     <i>$locale</i> is specified
     */
    public static function setlocale($category, $locale)
    {
        $return = false;

        static $categories = [
            'LC_COLLATE'  => LC_COLLATE,
            'LC_CTYPE'    => LC_CTYPE,
            'LC_MONETARY' => LC_MONETARY,
            'LC_NUMERIC'  => LC_NUMERIC,
            'LC_TIME'     => LC_TIME,
            'LC_MESSAGES' => LC_MESSAGES,
        ];

        $parts = explode(';', $locale);
        if ($category === LC_ALL && count($parts) > 1) {
            // Handle case when LC_ALL is undefined and we're passing a giant
            // string with all the separate lc-type values.
            foreach ($parts as $part) {
                $part_exp = explode('=', $part, 2);
                if (
                    count($part_exp) === 2
                    && array_key_exists($part_exp[0], $categories)
                ) {
                    $return = setlocale(
                        $categories[$part_exp[0]],
                        $part_exp[1],
                    );
                }
            }
        } else {
            $return = setlocale($category, $locale);
        }

        return $return;
    }

    // }}}
    // {{{ public function set()

    /**
     * Sets the system locale to this locale.
     *
     * @param int $category optional. The lc-type constant specifying the
     *                      category of functions affected by setting the
     *                      system locale. If not specified, defaults to
     *                      LC_ALL.
     */
    public function set($category = LC_ALL)
    {
        $this->old_locale_by_category[$category] = self::setlocale(
            $category,
            '0',
        );

        self::setlocale($category, $this->locale);
    }

    // }}}
    // {{{ public function reset()

    /**
     * Resets the system to the previous locale after a call to
     * {@link SwatI18NLocale::set()}.
     *
     * @param int $category optional. The lc-type constant specifying the
     *                      category of functions affected by resetting
     *                      the system locale. If not specified, defaults
     *                      to LC_ALL.
     */
    public function reset($category = LC_ALL)
    {
        self::setlocale($category, $this->old_locale_by_category[$category]);
    }

    // }}}
    // {{{ public function formatCurrency()

    /**
     * Formats a monetary value for this locale.
     *
     * This is similar to PHP's money_format() function except is is more
     * customizable because specific parts of the locale formatting may be
     * overridden. For example, it is possible using this method to format
     * numeric value as Canadian but have the currency symbol represent a
     * currency in another locale.
     *
     * This method also works on platforms where money_format() is not defined.
     * For example, this method works in Windows.
     *
     * This methods uses the POSIX.2 LC_MONETARY specification for formatting
     * monetary values.
     *
     * Numeric values are rounded to the specified number of fractional digits
     * using a round-to-even rounding method.
     *
     * @param float $value         the monetary value to format
     * @param bool  $international optional. Whether or not to format the
     *                             monetary value using the international
     *                             currency format. If not specified, the
     *                             monetary value is formatted using the
     *                             national currency format.
     * @param array $format        optional. An associative array of currency
     *                             formatting information that overrides the
     *                             formatting for this locale. The array is of the
     *                             form <i>'property' => value</i>. For example, use
     *                             the value <code>array('grouping' => 0)</code> to
     *                             turn off numeric groupings.
     *
     * @return string a UTF-8 encoded string containing the formatted monetary
     *                value
     *
     * @throws SwatException if a property name specified in the <i>$format</i>
     *                       parameter is invalid
     */
    public function formatCurrency(
        $value,
        $international = false,
        array $format = [],
    ) {
        $format = $international
            ? $this->getInternationalCurrencyFormat()->override($format)
            : $this->getNationalCurrencyFormat()->override($format);

        // default fractional digits to 2 if locale is missing value
        $fractional_digits
            = $format->fractional_digits === CHAR_MAX
                ? 2
                : $format->fractional_digits;

        $value = $this->roundToEven($value, $fractional_digits);

        $integer_part = $this->formatIntegerGroupings($value, $format);

        $fractional_part = $this->formatFractionalPart(
            $value,
            $fractional_digits,
            $format,
        );

        $formatted_value = $integer_part . $fractional_part;

        if ($value >= 0) {
            $sign = $format->p_sign;
            $sign_position = $format->p_sign_position;
            $cs_precedes = $format->p_cs_precedes;
            $separate_by_space = $format->p_separate_by_space;
        } else {
            $sign = $format->n_sign;
            $sign_position = $format->n_sign_position;
            $cs_precedes = $format->n_cs_precedes;
            $separate_by_space = $format->n_separate_by_space;

            // default negative sign if locale is missing value
            if ($sign == '') {
                $sign = '-';
            }
        }

        // default sign position if locale is missing value
        if ($sign_position === CHAR_MAX) {
            $sign_position = 1;
        }

        // default currency symbol position if locale is missing value
        if ($cs_precedes === CHAR_MAX) {
            $sign_position = true;
        }

        // default separate by space if locale is missing value
        if ($separate_by_space === CHAR_MAX) {
            $separate_by_space = false;
        }

        // trim spacing character off international currency symbol
        // TODO: this is not quite the same as money_format().
        $symbol
            = $separate_by_space && $international
                ? mb_substr($format->symbol, 0, 3)
                : $format->symbol;

        // now format the sign and symbol
        switch ($sign_position) {
            case 0:
                // parentheses surround the quantity and currency symbol
                if ($cs_precedes) {
                    if ($separate_by_space) {
                        $formatted_value = sprintf(
                            '(%s %s)',
                            $symbol,
                            $formatted_value,
                        );
                    } else {
                        $formatted_value = sprintf(
                            '(%s%s)',
                            $symbol,
                            $formatted_value,
                        );
                    }
                } else {
                    if ($separate_by_space) {
                        $formatted_value = sprintf(
                            '(%s %s)',
                            $formatted_value,
                            $symbol,
                        );
                    } else {
                        $formatted_value = sprintf(
                            '(%s%s)',
                            $formatted_value,
                            $symbol,
                        );
                    }
                }
                break;

            case 1:
                // the sign string precedes the quantity and currency symbol
                if ($cs_precedes) {
                    if ($separate_by_space) {
                        $formatted_value = sprintf(
                            '%s%s %s',
                            $sign,
                            $symbol,
                            $formatted_value,
                        );
                    } else {
                        $formatted_value = sprintf(
                            '%s%s%s',
                            $sign,
                            $symbol,
                            $formatted_value,
                        );
                    }
                } else {
                    if ($separate_by_space) {
                        $formatted_value = sprintf(
                            '%s%s %s',
                            $sign,
                            $formatted_value,
                            $symbol,
                        );
                    } else {
                        $formatted_value = sprintf(
                            '%s%s%s',
                            $sign,
                            $formatted_value,
                            $symbol,
                        );
                    }
                }
                break;

            case 2:
                // the sign string succeeds the quantity and currency symbol
                if ($cs_precedes) {
                    if ($separate_by_space) {
                        $formatted_value = sprintf(
                            '%s %s%s',
                            $symbol,
                            $formatted_value,
                            $sign,
                        );
                    } else {
                        $formatted_value = sprintf(
                            '%s%s%s',
                            $symbol,
                            $formatted_value,
                            $sign,
                        );
                    }
                } else {
                    if ($separate_by_space) {
                        $formatted_value = sprintf(
                            '%s %s%s',
                            $formatted_value,
                            $symbol,
                            $sign,
                        );
                    } else {
                        $formatted_value = sprintf(
                            '%s%s%s',
                            $sign,
                            $formatted_value,
                            $symbol,
                        );
                    }
                }
                break;

            case 3:
                // the sign string immediately precedes the currency symbol
                if ($cs_precedes) {
                    if ($separate_by_space) {
                        $formatted_value = sprintf(
                            '%s%s %s',
                            $sign,
                            $symbol,
                            $formatted_value,
                        );
                    } else {
                        $formatted_value = sprintf(
                            '%s%s%s',
                            $sign,
                            $symbol,
                            $formatted_value,
                        );
                    }
                } else {
                    if ($separate_by_space) {
                        $formatted_value = sprintf(
                            '%s %s%s',
                            $formatted_value,
                            $sign,
                            $symbol,
                        );
                    } else {
                        $formatted_value = sprintf(
                            '%s%s%s',
                            $formatted_value,
                            $sign,
                            $symbol,
                        );
                    }
                }
                break;

            case 4:
                // the sign string immediately succeeds the currency symbol
                if ($cs_precedes) {
                    if ($separate_by_space) {
                        $formatted_value = sprintf(
                            '%s%s %s',
                            $symbol,
                            $sign,
                            $formatted_value,
                        );
                    } else {
                        $formatted_value = sprintf(
                            '%s%s%s',
                            $symbol,
                            $sign,
                            $formatted_value,
                        );
                    }
                } else {
                    if ($separate_by_space) {
                        $formatted_value = sprintf(
                            '%s %s%s',
                            $formatted_value,
                            $symbol,
                            $sign,
                        );
                    } else {
                        $formatted_value = sprintf(
                            '%s%s%s',
                            $formatted_value,
                            $symbol,
                            $sign,
                        );
                    }
                }
                break;
        }

        return $formatted_value;
    }

    // }}}
    // {{{ public function formatNumber()

    /**
     * Formats a numeric value for this locale.
     *
     * This methods uses the POSIX.2 LC_NUMERIC specification for formatting
     * numeric values.
     *
     * Numeric values are rounded to the specified number of fractional digits
     * using a round-half-up rounding method (PHP's default round).
     *
     * @param float $value    the numeric value to format
     * @param int   $decimals optional. The number of fractional digits to
     *                        include in the returned string. If not
     *                        specified, all fractional digits are included.
     * @param array $format   optional. An associative array of number formatting
     *                        information that overrides the formatting for this
     *                        locale. The array is of the form
     *                        <i>'property' => value</i>. For example, use the
     *                        value <code>array('grouping' => 0)</code> to turn
     *                        off numeric groupings.
     *
     * @return string a UTF-8 encoded string containing the formatted numeric
     *                value
     *
     * @throws SwatException if a property name specified in the <i>$format</i>
     *                       parameter is invalid
     */
    public function formatNumber($value, $decimals = null, array $format = [])
    {
        $value = (float) $value;

        $format = $this->getNumberFormat()->override($format);

        if ($decimals === null) {
            $decimals = $this->getFractionalPrecision($value);
        }

        $value = round($value, $decimals);

        $integer_part = $this->formatIntegerGroupings($value, $format);
        $fractional_part = $this->formatFractionalPart(
            $value,
            $decimals,
            $format,
        );

        $sign = $value < 0 ? '-' : '';

        return $sign . $integer_part . $fractional_part;
    }

    // }}}
    // {{{ public function parseCurrency()

    /**
     * Parses a currency string formatted for this locale into a floating-point
     * number.
     *
     * @param string $string the formatted currency string
     *
     * @return float the numeric value of the parsed currency. If the given
     *               value could not be parsed, null is returned.
     */
    public function parseCurrency($string)
    {
        $value = null;

        $lc = $this->getLocaleInfo();

        $decimal_point
            = $lc['mon_decimal_point'] == ''
                ? $lc['decimal_point']
                : $lc['mon_decimal_point'];

        $string = $this->parseNegativeNotation(
            $string,
            $lc['negative_sign'],
            $lc['n_sign_posn'],
        );

        $search = [
            $lc['currency_symbol'],
            $lc['int_curr_symbol'],
            $lc['mon_thousands_sep'],
            $decimal_point,
            $lc['positive_sign'],
            ' ',
        ];

        $replace = ['', '', '', '.', '', ''];

        $string = str_replace($search, $replace, $string);

        if (is_numeric($string)) {
            $value = floatval($string);
        }

        return $value;
    }

    // }}}
    // {{{ public function parseFloat()

    /**
     * Parses a numeric string formatted for this locale into a floating-point
     * number.
     *
     * Note: The number does not have to be formatted exactly correctly to be
     * parsed. Checking too closely how well a formatted number matches its
     * locale would be annoying for users. For example, '1000' should not be
     * rejected because it wasn't formatted as '1,000'.
     *
     * @param string $string the formatted string
     *
     * @return float the numeric value of the parsed string. If the given
     *               value could not be parsed, null is returned.
     */
    public function parseFloat($string)
    {
        $value = null;

        $lc = $this->getLocaleInfo();

        $string = $this->parseNegativeNotation($string);

        $search = [
            $lc['thousands_sep'],
            $lc['decimal_point'],
            $lc['positive_sign'],
            ' ',
        ];

        $replace = ['', '.', '', ''];

        $string = str_replace($search, $replace, $string);

        if (is_numeric($string)) {
            $value = floatval($string);
        }

        return $value;
    }

    // }}}
    // {{{ public function parseInteger()

    /**
     * Parses a numeric string formatted for this locale into an integer number.
     *
     * If the string has fractional digits, the returned integer value is
     * rounded according to the rounding rules for
     * {@link http://php.net/manual/en/function.intval.php intval()}.
     *
     * Note: The number does not have to be formatted exactly correctly to be
     * parsed. Checking too closely how well a formatted number matches its
     * locale would be annoying for users. For example, '1000' should not be
     * rejected because it wasn't formatted as '1,000'.
     *
     * If the number is too large to fit in PHP's integer range (depends on
     * system architecture), an exception is thrown.
     *
     * @param string $string the formatted string
     *
     * @return int the numeric value of the parsed string. If the given
     *             value could not be parsed, null is returned.
     *
     * @throws SwatIntegerOverflowException if the converted number is too large
     *                                      to fit in an integer or if the converted number is too
     *                                      small to fit in an integer
     */
    public function parseInteger($string)
    {
        $value = null;

        $lc = $this->getLocaleInfo();

        $string = $this->parseNegativeNotation($string);

        $search = [$lc['thousands_sep'], $lc['positive_sign'], ' '];

        $replace = ['', '', ''];

        $string = str_replace($search, $replace, $string);

        if (is_numeric($string)) {
            if ($string > (float) PHP_INT_MAX) {
                throw new SwatIntegerOverflowException(
                    'Floating point value is too big to be an integer',
                    null,
                    1,
                );
            }

            if ($string < (float) (-PHP_INT_MAX - 1)) {
                throw new SwatIntegerOverflowException(
                    'Floating point value is too small to be an integer',
                    null,
                    -1,
                );
            }

            $value = intval($string);
        }

        return $value;
    }

    // }}}
    // {{{ public function getNumberFormat()

    /**
     * Gets the number format for this locale.
     *
     * @return SwatI18NNumberFormat the number format object for this locale.
     *                              All string properties of the object are
     *                              UTF-8 encoded.
     */
    public function getNumberFormat()
    {
        return clone $this->number_format;
    }

    // }}}
    // {{{ public function getNationalCurrencyFormat()

    /**
     * Gets the national currency format for this locale.
     *
     * @return SwatI18NCurrencyFormat the national currency format for this
     *                                locale. All string properties of the
     *                                object are UTF-8 encoded.
     */
    public function getNationalCurrencyFormat()
    {
        return clone $this->national_currency_format;
    }

    // }}}
    // {{{ public function getInternationalCurrencyFormat()

    /**
     * Gets the international currency format for this locale.
     *
     * @return SwatI18NCurrencyFormat the international currency format for this
     *                                locale. All string properties of the
     *                                object are UTF-8 encoded.
     */
    public function getInternationalCurrencyFormat()
    {
        return clone $this->international_currency_format;
    }

    // }}}
    // {{{ public function getInternationalCurrencySymbol()

    /**
     * Gets the international currency symbol of this locale.
     *
     * @return string the international currency symbol for this locale. The
     *                symbol is UTF-8 encoded and does not include the spacing
     *                character specified in the C99 standard.
     */
    public function getInternationalCurrencySymbol()
    {
        $lc = $this->getLocaleInfo();

        // strip C99-defined spacing character
        return mb_substr($lc['int_curr_symbol'], 0, 3);
    }

    // }}}
    // {{{ public function getLocaleInfo()

    /**
     * Gets numeric formatting information for this locale.
     *
     * This returns the same information that the PHP localeconv() function
     * returns with two differences. This method always returns strings in
     * UTF-8 and the system locale does not need to be set to this locale to
     * get the information.
     *
     * @return array the numeric formatting information for this locale
     */
    public function getLocaleInfo()
    {
        return $this->locale_info;
    }

    // }}}
    // {{{ public function __toString()

    /**
     * Gets a string representation of this locale.
     *
     * This returns the preferred locale identifier of this locale.
     *
     * @return string a string representation of this locale
     */
    public function __toString(): string
    {
        return $this->preferred_locale;
    }

    // }}}
    // {{{ protected function detectCharacterEncoding()

    /**
     * Detects the character encoding used by this locale.
     *
     * @return string the character encoding used by this locale. If the
     *                encoding could not be detected, null is returned.
     */
    protected function detectCharacterEncoding()
    {
        $encoding = null;

        if (function_exists('nl_langinfo') && is_callable('nl_langinfo')) {
            $encoding = nl_langinfo(CODESET);
        } else {
            // try to detect encoding from locale identifier
            $lc_ctype = null;
            $lc_all = self::setlocale(LC_ALL, '0');
            $lc_all_exp = explode(';', $lc_all);
            if (count($lc_all_exp) === 1) {
                $lc_ctype = reset($lc_all_exp);
            } else {
                foreach ($lc_all_exp as $lc) {
                    if (strncmp($lc, 'LC_CTYPE', 8) === 0) {
                        $lc_ctype = $lc;
                        break;
                    }
                }
            }

            if ($lc_ctype !== null) {
                $lc_ctype_exp = explode('.', $lc_ctype, 2);
                if (count($lc_ctype_exp) === 2) {
                    $encoding = $lc_ctype_exp[1];
                }
            }
        }

        // assume encoding is a code-page if encoding is numeric
        if ($encoding !== null && ctype_digit($encoding)) {
            $encoding = 'CP' . $encoding;
        }

        return $encoding;
    }

    // }}}
    // {{{ protected function buildLocaleInfo()

    /**
     * Builds the locale info array for this locale.
     */
    protected function buildLocaleInfo()
    {
        $this->locale_info = localeconv();

        // convert locale info to UTF-8
        $character_encoding = $this->detectCharacterEncoding();
        if ($character_encoding !== null && $character_encoding !== 'UTF-8') {
            $this->locale_info = $this->iconvArray(
                $character_encoding,
                'UTF-8',
                $this->locale_info,
            );
        }

        // special-cases and workarounds
        switch ($this->preferred_locale) {
            // Hebrew-Israeli
            case 'he_IL':
            case 'he_IL.utf8':
                $this->locale_info['currency_symbol'] = '₪';
                break;

                // Japanese and Chinese
            case 'ja_JP':
            case 'ja_JP.utf8':
            case 'zh_CN':
            case 'zh_CN.utf8':
                // use single-width Yen character, renders by default in Windows
                $this->locale_info['currency_symbol'] = '¥';
                break;
        }
    }

    // }}}
    // {{{ protected function buildNumberFormat()

    /**
     * Builds the number format of this locale.
     */
    protected function buildNumberFormat()
    {
        $lc = $this->getLocaleInfo();

        $format = new SwatI18NNumberFormat();

        $format->decimal_separator = $lc['decimal_point'];
        $format->thousands_separator = $lc['thousands_sep'];
        $format->grouping = $lc['grouping'];

        $this->number_format = $format;
    }

    // }}}
    // {{{ protected function buildNationalCurrencyFormat()

    /**
     * Builds the national currency format of this locale.
     */
    protected function buildNationalCurrencyFormat()
    {
        $lc = $this->getLocaleInfo();

        $format = new SwatI18NCurrencyFormat();

        $format->fractional_digits = $lc['frac_digits'];
        $format->p_cs_precedes = $lc['p_cs_precedes'];
        $format->n_cs_precedes = $lc['n_cs_precedes'];
        $format->p_separate_by_space = $lc['p_sep_by_space'];
        $format->n_separate_by_space = $lc['n_sep_by_space'];
        $format->p_sign_position = $lc['p_sign_posn'];
        $format->n_sign_position = $lc['n_sign_posn'];
        $format->decimal_separator
            = $lc['mon_decimal_point'] == ''
                ? $lc['decimal_point']
                : $lc['mon_decimal_point'];

        $format->thousands_separator = $lc['mon_thousands_sep'];
        $format->symbol = $lc['currency_symbol'];
        $format->grouping = $lc['mon_grouping'];
        $format->p_sign = $lc['positive_sign'];
        $format->n_sign = $lc['negative_sign'];

        // special-cases and workarounds
        switch ($this->preferred_locale) {
            // Hebrew-Israeli
            case 'he_IL':
            case 'he_IL.utf8':
                $format->p_sign_position = 1;
                $format->n_sign_position = 1;
                $format->p_cs_precedes = false;
                $format->n_cs_precedes = false;
                break;
        }

        $this->national_currency_format = $format;
    }

    // }}}
    // {{{ protected function buildInternationalCurrencyFormat()

    /**
     * Builds the internatiobal currency format for this locale.
     */
    protected function buildInternationalCurrencyFormat()
    {
        $lc = $this->getLocaleInfo();

        $format = new SwatI18NCurrencyFormat();

        $format->fractional_digits = $lc['int_frac_digits'];
        $format->p_cs_precedes = $lc['p_cs_precedes'];
        $format->n_cs_precedes = $lc['n_cs_precedes'];
        $format->p_separate_by_space = $lc['p_sep_by_space'];
        $format->n_separate_by_space = $lc['n_sep_by_space'];
        $format->p_sign_position = $lc['p_sign_posn'];
        $format->n_sign_position = $lc['n_sign_posn'];
        $format->decimal_separator
            = $lc['mon_decimal_point'] == ''
                ? $lc['decimal_point']
                : $lc['mon_decimal_point'];

        $format->thousands_separator = $lc['mon_thousands_sep'];
        $format->symbol = $lc['int_curr_symbol'];
        $format->grouping = $lc['mon_grouping'];
        $format->p_sign = $lc['positive_sign'];
        $format->n_sign = $lc['negative_sign'];

        $this->international_currency_format = $format;
    }

    // }}}
    // {{{ protected function formatIntegerGroupings()

    /**
     * Formats the integer part of a value according to format-specific numeric
     * groupings.
     *
     * This is a number formatting helper method. It is responsible for
     * grouping integer-part digits. Grouped digits are separated using the
     * thousands separator character specified by the format object.
     *
     * @param float $value the value to format
     * @param SwatI18NNumberFormat the number format to use
     *
     * @return string the grouped integer part of the value
     */
    protected function formatIntegerGroupings(
        $value,
        SwatI18NNumberFormat $format,
    ) {
        // group integer part with thousands separators
        $grouping_values = [];
        $groupings = $format->grouping;
        $grouping_total = intval(floor(abs($value)));
        if (
            count($groupings) === 0
            || $grouping_total === 0
            || $format->thousands_separator == ''
        ) {
            array_push($grouping_values, $grouping_total);
        } else {
            $grouping_previous = 0;
            while (count($groupings) > 1 && $grouping_total > 0) {
                $grouping = array_shift($groupings);

                if ($grouping === 0) {
                    // a grouping of 0 means use previous grouping
                    $grouping = $grouping_previous;
                } elseif ($grouping === CHAR_MAX) {
                    // a grouping of CHAR_MAX means no more grouping
                    array_push($grouping_values, $grouping_total);
                    break;
                } else {
                    $grouping_previous = $grouping;
                }

                $grouping_value = floor(
                    fmod($grouping_total, pow(10, $grouping)),
                );

                $grouping_total = floor($grouping_total / pow(10, $grouping));
                if ($grouping_total > 0) {
                    $grouping_value = str_pad(
                        $grouping_value,
                        $grouping,
                        '0',
                        STR_PAD_LEFT,
                    );
                }

                array_push($grouping_values, $grouping_value);
            }

            // last grouping repeats until integer part is finished
            $grouping = array_shift($groupings);

            // a grouping of CHAR_MAX means no more grouping
            if ($grouping === CHAR_MAX) {
                array_push($grouping_values, $grouping_total);
            } else {
                // a grouping of 0 means use previous grouping
                if ($grouping === 0) {
                    $grouping = $grouping_previous;
                }

                // a grouping of 0 as the last grouping means no more grouping
                if ($grouping === 0) {
                    array_push($grouping_values, $grouping_total);
                } else {
                    while ($grouping_total > 0) {
                        $grouping_value = floor(
                            fmod($grouping_total, pow(10, $grouping)),
                        );

                        $grouping_total = floor(
                            $grouping_total / pow(10, $grouping),
                        );

                        if ($grouping_total > 0) {
                            $grouping_value = str_pad(
                                $grouping_value,
                                $grouping,
                                '0',
                                STR_PAD_LEFT,
                            );
                        }

                        array_push($grouping_values, $grouping_value);
                    }
                }
            }
        }

        $grouping_values = array_reverse($grouping_values);

        // join groupings using thousands separator
        return implode(
            $format->thousands_separator,
            $grouping_values,
        );
    }

    // }}}
    // {{{ protected function formatFractionalPart()

    /**
     * Formats the fractional  part of a value.
     *
     * @param float                $value             the value to format
     * @param int                  $fractional_digits the number of fractional digits to
     *                                                include in the returned string
     * @param SwatI18NNumberFormat $format            the number formatting object to use
     *                                                to format the fractional digits
     *
     * @return string the formatted fractional digits. If the number of
     *                displayed fractional digits is greater than zero, the
     *                string is prepended with the decimal separator character
     *                of the format object.
     */
    protected function formatFractionalPart(
        $value,
        $fractional_digits,
        SwatI18NNumberFormat $format,
    ) {
        if ($fractional_digits === 0) {
            $formatted_value = '';
        } else {
            $frac_part = abs(fmod($value, 1));
            $frac_part = round($frac_part * pow(10, $fractional_digits));
            $frac_part = str_pad(
                $frac_part,
                $fractional_digits,
                '0',
                STR_PAD_LEFT,
            );

            $formatted_value = $format->decimal_separator . $frac_part;
        }

        return $formatted_value;
    }

    // }}}
    // {{{ protected function parseNegativeNotation

    /**
     * Parses the negative notation for a numeric string formatted in this
     * locale.
     *
     * @param string $string          the formatted string
     * @param string $n_sign          optional. The negative sign to parse. If not
     *                                specified, the negative sign for this locale is
     *                                used.
     * @param int    $n_sign_position optional. The position of the negative
     *                                sign in the formatted string. If not
     *                                specified, the value 1 is assumed. This
     *                                may be used to allow parsing
     *                                parenthetical formatted negative values
     *                                as used by some currencies.
     *
     * @return string the formatted string with the negative notation parsed
     *                and normalized into a form readable by intval() and
     *                floatval()
     */
    protected function parseNegativeNotation(
        $string,
        $n_sign = null,
        $n_sign_position = 1,
    ) {
        $lc = $this->getLocaleInfo();

        $negative = false;

        if ($n_sign == '') {
            if ($lc['negative_sign'] == '') {
                $negative_sign = '-';
            } else {
                $negative_sign = $lc['negative_sign'];
            }
        } else {
            $negative_sign = $n_sign;
        }

        // filter out all chars except for digits and negative formatting chars
        $char_class = '0-9' . preg_quote($negative_sign, '/');
        if ($n_sign_position === 0) {
            $char_class = '()' . $char_class;
        }
        $exp = '/[^' . $char_class . ']/u';
        $filtered = preg_replace($exp, '', $string);

        if ($filtered != '') {
            if ($filtered[0] === '-' || mb_substr($filtered, -1) === '-') {
                // always allow parsing by negative sign
                $negative = true;
                $string = str_replace($negative_sign, '', $string);
            } elseif (
                $n_sign_position === 0
                && $filtered[0] === '('
                && mb_substr($filtered, -1) === ')'
            ) {
                // parse parenthetical negative shown as: (5.00)
                $negative = true;
                $string = str_replace(['(', ')'], '', $string);
            }
        }

        if ($negative) {
            $string = '-' . $string;
        }

        return $string;
    }

    // }}}
    // {{{ protected function getFractionalPrecision()

    /**
     * Gets the fractional precision of a floating point number.
     *
     * This gets the number of digits after the decimal point.
     *
     * @param float $value the value for which to get the fractional precision
     *
     * @return int the fractional precision of the value
     */
    protected function getFractionalPrecision($value)
    {
        /*
         * This is a bit hacky (and probably slow). We get the string
         * representation and then count the number of digits after the decimal
         * separator. This may or may not be faster than the equivalent
         * IEEE-754 decomposition (written in PHP). The string-based code has
         * not been profiled against the equivalent IEEE-754 code.
         */

        $value = (float) $value;

        // get current locale
        $locale = self::get();

        $precision = 0;
        $lc = $locale->getLocaleInfo();
        $str_value = (string) $value;

        $e_pos = mb_stripos($str_value, 'E-');
        if ($e_pos !== false) {
            $precision += (int) mb_substr($str_value, $e_pos + 2);
            $str_value = mb_substr($str_value, 0, $e_pos);
        }

        $decimal_pos = mb_strpos($str_value, $lc['decimal_point']);
        if ($decimal_pos !== false) {
            $precision
                += mb_strlen($str_value)
                - $decimal_pos
                - mb_strlen($lc['decimal_point']);
        }

        return $precision;
    }

    // }}}
    // {{{ protected function roundToEven()

    /**
     * Rounds a number to the specified number of fractional digits using the
     * round-to-even rounding method.
     *
     * Round-to-even is primarily used for monetary values. See
     * {@link http://en.wikipedia.org/wiki/Rounding#Round-to-even_method}.
     *
     * @param float $value             the value to round
     * @param int   $fractional_digits the number of fractional digits in the
     *                                 rounded result
     *
     * @return float the rounded value
     */
    protected function roundToEven($value, $fractional_digits)
    {
        $exp = pow(10, $fractional_digits);
        $frac_part = abs(fmod($value, 1)) * $exp;
        $ends_in_five = intval($frac_part * 10) % 10 === 5;
        if ($ends_in_five) {
            // check if fractional part is odd
            if ((intval($frac_part) & 0x01) === 0x01) {
                // round up on odd
                $value = ceil($value * $exp) / $exp;
            } else {
                // round down on even
                $value = floor($value * $exp) / $exp;
            }
        } else {
            // use normal rounding
            $value = round($value, $fractional_digits);
        }

        return $value;
    }

    // }}}
    // {{{ private function __construct()

    /**
     * Creates a new locale object.
     *
     * This constructor is private. Locale objects should be instantiated using
     * the static {@link SwatI18NLocale::get()} method.
     *
     * @param array|string $locale the locale identifier of this locale object.
     *                             If the locale is not valid for the current
     *                             operating system, an exception is thrown.
     *                             If no locale is specified, the current
     *                             locale is used. Multiple locale identifiers
     *                             may be specified in an array. In this case,
     *                             the first valid locale is used.
     *
     * @throws SwatException if the specified <i>$locale</i> is not valid for
     *                       the current operating system
     *
     * @see SwatI18NLocale::get()
     */
    private function __construct($locale = null)
    {
        $this->locale = $locale;

        if ($this->locale === null) {
            $this->preferred_locale = self::setlocale(LC_ALL, '0');
        } else {
            $old_locale = self::setlocale(LC_ALL, '0');
            $this->preferred_locale = self::setlocale(LC_ALL, $this->locale);
            if ($this->preferred_locale === false) {
                throw new SwatException(
                    "The locale {$this->locale} is not "
                        . 'valid for this operating system.',
                );
            }
        }

        $this->buildLocaleInfo();
        $this->buildNumberFormat();
        $this->buildNationalCurrencyFormat();
        $this->buildInternationalCurrencyFormat();

        if ($this->locale !== null) {
            self::setlocale(LC_ALL, $old_locale);
        }
    }

    // }}}
    // {{{ private function iconvArray()

    /**
     * Recursivly converts the character encoding of all strings in an array.
     *
     * @param string $from  the character encoding to convert from
     * @param string $to    the character encoding to convert to
     * @param array  $array the array to convert
     *
     * @return array a new array with all strings converted to the given
     *               character encoding
     *
     * @throws SwatException if any component of the array can not be converted
     *                       from the <i>$from</i> character encoding to the
     *                       <i>$to</i> character encoding
     */
    private function iconvArray($from, $to, array $array)
    {
        if ($from != $to) {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $array[$key] = $this->iconvArray($from, $to, $value);
                } elseif (is_string($value)) {
                    $output = iconv($from, $to, $value);
                    if ($output === false) {
                        throw new SwatException(
                            sprintf(
                                'Could not convert %s output to %s',
                                $from,
                                $to,
                            ),
                        );
                    }

                    $array[$key] = $output;
                }
            }
        }

        return $array;
    }

    // }}}
}
