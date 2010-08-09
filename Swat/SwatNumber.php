<?php

/**
 * Number tools
 *
 * @package   Swat
 * @copyright 2008-2009 silverorange
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
	 * Simple method to return the ordinal value of a number.
	 *
	 * Only safe for use for english locales. Mostly taken from this the
	 * following comment on php.net
	 * {@link http://www.php.net/manual/en/function.number-format.php#89655}
	 *
	 * @param float $value the value to display as ordinal.
	 *
	 * @return string the ordinal value.
	 */
	public static function ordinal($value)
	{
		$ordinal_value = abs($value);

		switch ($ordinal_value % 100) {
		case 11:
		case 12:
		case 13:
			$ordinal_value.= Swat::_('th');
			break;

		default:
			// Handle 1st, 2nd, 3rd
			switch($value % 10) {
			case 1:
				$ordinal_value.= Swat::_('st');
				break;

			case 2:
				$ordinal_value.= Swat::_('nd');
				break;

			case 3:
				$ordinal_value.= Swat::_('rd');
				break;

			default:
				$ordinal_value.= Swat::_('th');
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
