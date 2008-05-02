<?php

/**
 * Number tools
 *
 * @package   Swat
 * @copyright 2008 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatNumber extends SwatObject
{
	// {{{ public static function roundToEven()

	/**
	 * Rounds a number to the specified number of fractional digits using the
	 * round-to-even rounding method
	 *
	 * Round-to-even is primarily used for monetary values. See
	 * {@link http://en.wikipedia.org/wiki/Rounding#Round-to-even_method}.
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
		$fracional_part = abs(fmod($value, 1)) * $power;
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
