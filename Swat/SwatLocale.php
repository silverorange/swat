<?php

require_once 'Swat/exceptions/SwatException.php';
require_once 'Swat/SwatObject.php';
require_once 'Swat/SwatCurrencyFormat.php';

/**
 * Internationalization and localization methods
 *
 * This class contains static methods related for formatting localized values.
 *
 * @package   Swat
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatLocale extends SwatObject
{
	// {{{ public static formatCurrency()

	/**
	 * Formats a monetary value using a localized format
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
	 * @param float $value the monetary value to format.
	 * @param boolean $international optional. Whether or not to format the
	 *                                monetary value using the international
	 *                                currency format. If not specified, the
	 *                                monetary value is formatted using the
	 *                                national currency format of the specified
	 *                                locale.
	 * @param string $locale optional. The locale in which to format the
	 *                        monetary value. If not specified, the current
	 *                        locale is used.
	 * @param SwatCurrencyFormat $format optional. Currency formatting
	 *                                    information that overrides the
	 *                                    formatting for the specified locale.
	 *
	 * @return string a UTF-8 encoded string containing the formatted monetary
	 *                 value.
	 *
	 * @throws SwatException if the specified locale is not valid for the
	 *                       current operating system.
	 */
	public static function formatCurrency($value, $international = false,
		$locale = null, SwatCurrencyFormat $format = null)
	{
		$currency_format = ($international) ?
			self::getInternationalCurrencyFormat($locale) :
			self::getCurrencyFormat($locale);

		if ($format !== null)
			$currency_format->override($format);

		$format = $currency_format;

		// default fractional digits to 2 if locale is missing value
		$fractional_digits = ($format->fractional_digits == CHAR_MAX) ?
			2 : $format->fractional_digits;

		$positive = ($value > 0);
		$integer_part = floor(abs($value));
		$frac_part = abs(fmod($value, 1));
		$frac_part = round($frac_part * pow(10, $fractional_digits));

		// group integer part with thousands separators
		$grouping_values = array();
		$groupings = $format->grouping;
		$grouping_total = $integer_part;
		if (count($groupings) == 0) {
			array_push($grouping_values, $grouping_total);
		} else {
			$grouping_previous = 0;
			while (count($groupings) > 1 && $grouping_total > 0) {
				$grouping = array_shift($groupings);

				// a grouping of 0 means use previous grouping
				if ($grouping == 0) {
					$grouping = $grouping_previous;
				// a grouping of CHAR_MAX means no more grouping
				} elseif ($grouping == CHAR_MAX) {
					array_push($grouping_values, $grouping_total);
					break;
				} else {
					$grouping_previous = $grouping;
				}

				array_push($grouping_values,
					$grouping_total % pow(10, $grouping));

				$grouping_total = floor($grouping_total / pow(10, $grouping));
			}

			// last grouping repeats until integer part is finished
			$grouping = array_shift($groupings);

			// a grouping of CHAR_MAX means no more grouping
			if ($grouping == CHAR_MAX) {
				array_push($grouping_values, $grouping_total);
			} else {
				// a grouping of 0 means use previous grouping
				if ($grouping == 0) {
					$grouping = $grouping_previous;
				}

				// a grouping of 0 as the last grouping means no more grouping
				if ($grouping == 0) {
					array_push($grouping_values, $grouping_total);
				} else {
					while ($grouping_total > 0) {
						array_push($grouping_values,
							$grouping_total % pow(10, $grouping));

						$grouping_total =
							floor($grouping_total / pow(10, $grouping));
					}
				}
			}
		}

		$grouping_values = array_reverse($grouping_values);

		// we now have a formatted number
		$formatted_value =
			implode($format->thousands_separator, $grouping_values).
			$format->decimal_separator.$frac_part;

		if ($positive) {
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
			if ($sign == '')
				$sign = '-';
		}

		// default sign position if locale is missing value
		if ($sign_position == CHAR_MAX)
			$sign_position = 1;

		// default currency symbol position if locale is missing value
		if ($cs_precedes == CHAR_MAX)
			$sign_position = true;

		// default separate by space if locale is missing value
		if ($separate_by_space == CHAR_MAX)
			$separate_by_space = false;

		// trim spacing character off international currency symbol
		// TODO: this is not quite the same as money_format().
		$symbol = ($separate_by_space && $international) ?
			substr($format->symbol, 0, 3) : $format->symbol;

		// now format the sign and symbol
		switch ($sign_position) {
		case 0:
			// parentheses surround the quantity and currency symbol
			if ($cs_precedes) {
				if ($separate_by_space) {
					$formatted_value = sprintf('(%s %s)',
						$symbol, $formatted_value);
				} else {
					$formatted_value = sprintf('(%s%s)',
						$symbol, $formatted_value);
				}
			} else {
				if ($separate_by_space) {
					$formatted_value = sprintf('(%s %s)',
						$formatted_value, $symbol);
				} else {
					$formatted_value = sprintf('(%s%s)',
						$formatted_value, $symbol);
				}
			}
			break;

		case 1:
			// the sign string precedes the quantity and currency symbol
			if ($cs_precedes) {
				if ($separate_by_space) {
					$formatted_value = sprintf('%s%s %s',
						$sign, $symbol, $formatted_value);
				} else {
					$formatted_value = sprintf('%s%s%s',
						$sign, $symbol, $formatted_value);
				}
			} else {
				if ($separate_by_space) {
					$formatted_value = sprintf('%s%s %s',
						$sign, $formatted_value, $symbol);
				} else {
					$formatted_value = sprintf('%s%s%s',
						$sign, $formatted_value, $symbol);
				}
			}
			break;

		case 2:
			// the sign string succeeds the quantity and currency symbol
			if ($cs_precedes) {
				if ($separate_by_space) {
					$formatted_value = sprintf('%s %s%s',
						$symbol, $formatted_value, $sign);
				} else {
					$formatted_value = sprintf('%s%s%s',
						$symbol, $formatted_value, $sign);
				}
			} else {
				if ($separate_by_space) {
					$formatted_value = sprintf('%s %s%s',
						$formatted_value, $symbol, $sign);
				} else {
					$formatted_value = sprintf('%s%s%s',
						$sign, $formatted_value, $symbol);
				}
			}
			break;

		case 3:
			// the sign string immediately precedes the currency symbol
			if ($cs_precedes) {
				if ($separate_by_space) {
					$formatted_value = sprintf('%s%s %s',
						$sign, $symbol, $formatted_value);
				} else {
					$formatted_value = sprintf('%s%s%s',
						$sign, $symbol, $formatted_value);
				}
			} else {
				if ($separate_by_space) {
					$formatted_value = sprintf('%s %s%s',
						$formatted_value, $sign, $symbol);
				} else {
					$formatted_value = sprintf('%s%s%s',
						$formatted_value, $sign, $symbol);
				}
			}
			break;

		case 4:
			// the sign string immediately succeeds the currency symbol
			if ($cs_precedes) {
				if ($separate_by_space) {
					$formatted_value = sprintf('%s%s %s',
						$symbol, $sign, $formatted_value);
				} else {
					$formatted_value = sprintf('%s%s%s',
						$symbol, $sign, $formatted_value);
				}
			} else {
				if ($separate_by_space) {
					$formatted_value = sprintf('%s %s%s',
						$formatted_value, $symbol, $sign);
				} else {
					$formatted_value = sprintf('%s%s%s',
						$formatted_value, $symbol, $sign);
				}
			}
			break;

		}

		return $formatted_value;
	}

	// }}}
	// {{{ public static function getCurrencyFormat()

	/**
	 * Gets a currency format object for a given locale
	 *
	 * @param string $locale optional. The locale to get the currency format
	 *                        for. If the locale is not valid for the current
	 *                        operating system, an exception is thrown. If no
	 *                        locale is specified, the current locale is used.
	 *
	 * @return SwatCurrencyFormat a currency format object for the specified
	 *                             locale. All string properties of the object
	 *                             are UTF-8 encoded.
	 *
	 * @throws SwatException if the specified locale is not valid for the
	 *                        current operating system.
	 */
	public static function getCurrencyFormat($locale = null)
	{
		if ($locale !== null) {
			$monetary_locale = setlocale(LC_MONETARY, '0');
			$ctype_locale = setlocale(LC_CTYPE, '0');
			if (setlocale(LC_MONETARY, $locale) === false) {
				throw new SwatException(sprintf('Locale %s passed to the '.
					'getCurrencyFormat() method is not valid for this '.
					'operating system.', $locale));
			}
			if (setlocale(LC_CTYPE, $locale) === false) {
				throw new SwatException(sprintf('Locale %s passed to the '.
					'getCurrencyFormat() method is not valid for this '.
					'operating system.', $locale));
			}
		}

		$lc = localeconv();

		// convert locale info to UTF-8
		$encoding = self::getEncoding();
		if ($encoding !== null && $encoding !== 'UTF-8')
			$lc = self::iconvArray($encoding, 'UTF-8', $lc);

		$format = new SwatCurrencyFormat();
		$format->fractional_digits     = $lc['frac_digits'];
		$format->p_cs_precedes         = $lc['p_cs_precedes'];
		$format->n_cs_precedes         = $lc['n_cs_precedes'];
		$format->p_separate_by_space   = $lc['p_sep_by_space'];
		$format->n_separate_by_space   = $lc['n_sep_by_space'];
		$format->p_sign_position       = $lc['p_sign_posn'];
		$format->n_sign_position       = $lc['n_sign_posn'];
		$format->decimal_separator     = ($lc['mon_decimal_point'] == '') ?
			$lc['decimal_point'] : $lc['mon_decimal_point'];

		$format->thousands_separator   = $lc['mon_thousands_sep'];
		$format->symbol                = $lc['currency_symbol'];
		$format->grouping              = $lc['mon_grouping'];
		$format->p_sign                = $lc['positive_sign'];
		$format->n_sign                = $lc['negative_sign'];

		if ($locale !== null) {
			setlocale(LC_MONETARY, $monetary_locale);
			setlocale(LC_CTYPE, $ctype_locale);
		}

		return $format;
	}

	// }}}
	// {{{ public static function getInternationalCurrencyFormat()

	/**
	 * Gets an international currency format object for a given locale
	 *
	 * @param string $locale optional. The locale to get the currency format
	 *                        for. If the locale is not valid for the current
	 *                        operating system, an exception is thrown. If no
	 *                        locale is specified, the current locale is used.
	 *
	 * @return SwatCurrencyFormat a currency format object for the specified
	 *                             locale. All string properties of the object
	 *                             are UTF-8 encoded.
	 *
	 * @throws SwatException if the specified locale is not valid for the
	 *                        current operating system.
	 *
	 * @todo return string properties as UTF-8 even for non-UTF-8 locales.
	 */
	public static function getInternationalCurrencyFormat($locale = null)
	{
		if ($locale !== null) {
			$monetary_locale = setlocale(LC_MONETARY, '0');
			$ctype_locale = setlocale(LC_CTYPE, '0');
			if (setlocale(LC_MONETARY, $locale) === false) {
				throw new SwatException(sprintf('Locale %s passed to the '.
					'getCurrencyFormat() method is not valid for this '.
					'operating system.', $locale));
			}
			if (setlocale(LC_CTYPE, $locale) === false) {
				throw new SwatException(sprintf('Locale %s passed to the '.
					'getCurrencyFormat() method is not valid for this '.
					'operating system.', $locale));
			}
		}

		$lc = localeconv();

		// convert locale info to UTF-8
		$encoding = self::getEncoding();
		if ($encoding !== null && $encoding !== 'UTF-8')
			$lc = self::iconvArray($encoding, 'UTF-8', $lc);

		$format = new SwatCurrencyFormat();
		$format->fractional_digits     = $lc['int_frac_digits'];
		$format->p_cs_precedes         = $lc['p_cs_precedes'];
		$format->n_cs_precedes         = $lc['n_cs_precedes'];
		$format->p_separate_by_space   = $lc['p_sep_by_space'];
		$format->n_separate_by_space   = $lc['n_sep_by_space'];
		$format->p_sign_position       = $lc['p_sign_posn'];
		$format->n_sign_position       = $lc['n_sign_posn'];
		$format->decimal_separator     = ($lc['mon_decimal_point'] == '') ?
			$lc['decimal_point'] : $lc['mon_decimal_point'];

		$format->thousands_separator   = $lc['mon_thousands_sep'];
		$format->symbol                = $lc['int_curr_symbol'];
		$format->grouping              = $lc['mon_grouping'];
		$format->p_sign                = $lc['positive_sign'];
		$format->n_sign                = $lc['negative_sign'];

		if ($locale !== null) {
			setlocale(LC_MONETARY, $monetary_locale);
			setlocale(LC_CTYPE, $ctype_locale);
		}

		return $format;
	}

	// }}}
	// {{{ public static function getEncoding()

	/**
	 * Gets the character encoding used by a locale
	 *
	 * @param string $locale optional. The locale for which to get the
	 *                        character encoding. If not specified, the current
	 *                        locale is used.
	 *
	 * @return string the character encoding for the specified locale. If the
	 *                 character encoding could not be detected, null is
	 *                 returned.
	 */
	public static function getEncoding($locale = null)
	{
		$encoding = null;

		if ($locale !== null) {
			$old_locale = setlocale(LC_CTYPE, '0');
			if (setlocale(LC_CTYPE, $locale) === false) {
				throw new SwatException(sprintf('Locale %s passed to the '.
					'getEncoding() method is not valid for this '.
					'operating system.', $locale));
			}
		}

		if (function_exists('nl_langinfo') && is_callable('nl_langinfo')) {

			$encoding = nl_langinfo(CODESET);

		} else {

			// try to detect encoding from locale identifier
			$lc_ctype = null;
			$lc_all = setlocale(LC_ALL, '0');
			$lc_all_exp = explode(';', $lc_all);
			if (count($lc_all_exp) == 1) {
				$lc_ctype = reset($lc_all_exp);
			} else {
				foreach ($lc_all_exp as $lc) {
					if (strncmp($lc, 'LC_CTYPE', 8) == 0) {
						$lc_ctype = $lc;
						break;
					}
				}
			}

			if ($lc_ctype !== null) {
				$lc_ctype_exp = explode('.', $lc_ctype, 2);
				if (count($lc_ctype_exp) == 2) {
					$encoding = $lc_ctype_exp[1];
				}
			}

		}

		if ($locale !== null) {
			setlocale(LC_CTYPE, $old_locale);
		}

		// assume encoding is a code-page if encoding is numeric
		if ($encoding !== null && ctype_digit($encoding)) {
			$encoding = 'CP'.$encoding;
		}

		return $encoding;
	}

	// }}}
	// {{{ private function iconvArray()

	/**
	 * Recursivly converts the character encoding of all strings in an array
	 *
	 * @param string $from the character encoding to convert from.
	 * @param string $to the character encoding to convert to.
	 * @param array $array the array to convert.
	 *
	 * @return array a new array with all strings converted to the given
	 *                character encoding.
	 *
	 * @throws SwatException if any component of the array can not be converted
	 *                       from the <i>$from</i> character encoding to the
	 *                       <i>$to</i> character encoding.
	 */
	private static function iconvArray($from, $to, array $array)
	{
		if ($from != $to) {
			foreach ($array as $key => $value) {
				if (is_array($value)) {
					$array[$key] = self::iconvArray($from, $to, $value);
				} elseif (is_string($value)) {
					$output = iconv($from, $to, $value);
					if ($output === false)
						throw new SwatException(sprintf('Could not convert '.
							'%s output to %s', $from, $to));

					$array[$key] = $output;
				}
			}
		}

		return $array;
	}

	// }}}
	// {{{ private function __construct()

	/**
	 * Don't allow instantiation of the SwatLocale object
	 *
	 * This class contains only static methods and should not be instantiated.
	 */
	private function __construct()
	{
	}

	// }}}
}

?>
