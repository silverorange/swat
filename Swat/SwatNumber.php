<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */


/**
 * Number tools
 *
 * @package   Swat
 * @copyright 2008-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatNumber extends SwatObject
{
	// {{{ public static function roundUp()

	/**
	 * Rounds a number to the specified number of fractional digits using the
	 * round-half-up rounding method
	 *
	 * See {@link http://en.wikipedia.org/wiki/Rounding#Round_half_up}.
	 *
	 * @param float $value the value to round.
	 * @param integer $fractional_digits the number of fractional digits in the
	 *                                    rounded result.
	 *
	 * @return float the rounded value.
	 */
	public static function roundUp($value, $fractional_digits)
	{
		$power = pow(10, $fractional_digits);
		$value = ceil($value * $power) / $power;

		return $value;
	}

	// }}}
	// {{{ public static function roundToEven()

	/**
	 * Rounds a number to the specified number of fractional digits using the
	 * round-to-even rounding method
	 *
	 * Round-to-even is primarily used for monetary values. See
	 * {@link http://en.wikipedia.org/wiki/Rounding#Round_half_to_even}.
	 *
	 * @param float $value the value to round.
	 * @param integer $fractional_digits the number of fractional digits in the
	 *                                    rounded result.
	 *
	 * @return float the rounded value.
	 */
	public static function roundToEven($value, $fractional_digits)
	{
		$power = pow(10, $fractional_digits);
		$fractional_part = abs(fmod($value, 1)) * $power;
		$ends_in_five = (intval($fractional_part * 10) % 10 === 5);
		if ($ends_in_five) {
			// check if fractional part is odd
			if ((intval($fractional_part) & 0x01) === 0x01) {
				// round up on odd
				$value = ceil($value * $power) / $power;
			} else {
				// round down on even
				$value = floor($value * $power) / $power;
			}
		} else {
			// use normal rounding
			$value = round($value, $fractional_digits);
		}

		return $value;
	}

	// }}}
	// {{{ public static function ordinal()

	/**
	 * Formats an integer as an ordinal number (1st, 2nd, 3rd)
	 *
	 * If the 'intl' extension is available, the ICU number formatter and
	 * string normalizers are used to get a correctly formatted ordinal for
	 * the current locale.
	 *
	 * If the 'intl' extension is not available, this method is only safe for
	 * English locales. The fallback implementation is mostly taken from the
	 * following comment on php.net:
	 * {@link http://www.php.net/manual/en/function.number-format.php#89655}
	 *
	 * @param integer $value the numeric value to format.
	 *
	 * @return string the ordinal-formatted value.
	 */
	public static function ordinal($value)
	{
		$value = intval($value);

		if (extension_loaded('intl')) {

			// get current locale
			$locale = setlocale(LC_ALL, 0);

			static $formatters = array();
			if (!isset($formatter[$locale])) {
				$formatter[$locale] =
					new NumberFormatter($locale, NumberFormatter::ORDINAL);
			}

			// format ordinal
			$ordinal_value = $formatter[$locale]->format($value);

			// decompose to latin-1 characters (removes superscripts)
			$ordinal_value = Normalizer::normalize(
				$ordinal_value,
				Normalizer::FORM_KC);

		} else {

			// fallback implementation if icu is not available
			$ordinal_value = abs($value);

			switch ($ordinal_value % 100) {
			case 11:
			case 12:
			case 13:
				$ordinal_value = sprintf(Swat::_('%sth'), $ordinal_value);
				break;

			default:
				// Handle 1st, 2nd, 3rd
				switch($value % 10) {
				case 1:
					$ordinal_value = sprintf(Swat::_('%sst'), $ordinal_value);
					break;

				case 2:
					$ordinal_value = sprintf(Swat::_('%snd'), $ordinal_value);
					break;

				case 3:
					$ordinal_value = sprintf(Swat::_('%srd'), $ordinal_value);
					break;

				default:
					$ordinal_value = sprintf(Swat::_('%sth'), $ordinal_value);
				}
			}

		}

		return $ordinal_value;
	}

	// }}}
	// {{{ private function __construct()

	/**
	 * Don't allow instantiation of the SwatNumber object
	 *
	 * This class contains only static methods and should not be instantiated.
	 */
	private function __construct()
	{
	}

	// }}}
}

?>
