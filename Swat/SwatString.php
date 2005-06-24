<?php
// vim: set fdm=marker:

/**
 * String Tools
 *
 * @package   Swat
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatString
{
	/**
	 * replaces hard-returns with br and doubles with p
	 */
	const FILTER_BODY = 0;

	/**
	 * same as body, but doesn't ignore lines with tags on them
	 */
	const FILTER_B2 = 0;

	/**
	 * replaces all hard returns with a bullet character
	 */
	const FILTER_BLURB = 0;

    // {{{ public static function filter()

	/**
	 * Filter text block
	 *
	 * Formatting function to control the display of a block of text
	 *
	 * @param integer $type Type of formatting to apply. One of the FILTER
	 *        {@link SwatString} constants.
	 * @param string $text Text to format
	 *
	 * @return string the formatted string.
	 */
	public static function filter($type, $text)
	{
		if ($type == self::FILTER_BODY) {
			$text = ereg_replace(chr(13).chr(10).chr(13).chr(10).'([^<])', '<p>\1', $text);
			$text = ereg_replace('([^>])'.chr(13).chr(10).'([^<])', '\1<br>\2', $text);
			return $text;

		} elseif ($type == self::FILTER_B2) {
			$text = ereg_replace(chr(13).chr(10).chr(13).chr(10), '<p>', $text);
			$text = ereg_replace(chr(13).chr(10), '<br />', $text);
			return $text;

		} elseif ($type == self::FILTER_BLURB) {
			$text = ereg_replace('('.chr(13).chr(10).')+', ' &nbsp;&#8226;&nbsp; ', $text);
			return $text;
		}
	}
	// }}}
	// {{{ public static function ellipsizeRight()

	/**
	 * Ellipsizes a string to the right
	 *
	 * example:
	 *
	 *    $string = 'The quick brown fox jumped over the lazy dogs.';
	 *    // displays 'The quick brown ...'
	 *    echo SwatString::ellipsizeRight($string, 18, ' ...');
	 *
	 * @param string $string the string to ellipsize.
	 * @param integer $max_length the maximum length of the returned string.
	 *                             This length does not account for any ellipse
	 *                             characters that may be appended. If the
	 *                             returned value must be below a certain
	 *                             number of characters, pass a blank string in
	 *                             the ellipses parameter.
	 * @param string $ellipses the ellipses characters to append if the string
	 *                          is shortened.
	 *
	 * @return string the ellipsized string. The ellipsized string may be
	 *                 appended with ellipses characters if it was longer than
	 *                 max_length.
	 */
	public static function ellipsizeRight($string, $max_length,
		$ellipses = '&nbsp;&#8230;')
	{
		// don't ellipsize if the string is short enough
		if (strlen($string) <= $max_length)
			return $string;

		$search_offset = -max(strlen($string) - $max_length, 0);
		
		// find the last space up to the max_length in the string
		$chop_pos = strrpos($string, ' ', $offset);
		if ($chop_pos === false) $chop_pos = $max_length

		$string = substr($string, 0, $chop_pos);
		$string = SwatString::removeTrailingPunctuation($string);
		$string .= $ellipses;

		return $string;
	}
	
	// }}}
    // {{{ public static function smartTrim()

	/**
	 * Smart Trim
	 *
	 * @param string $text Text to trim
	 * @param integer $max_len Length to trim string at
	 * @param string $trim_cars Text to append to the end of the trimmed string
	 *
	 * @return string the formatted string.
	 */
	public static function smartTrim($text, $max_len, $trim_chars='...')
	{
		$text = trim($text);

		if (strlen($text) < $max_len) {
			return $text;

		} else {
			$has_space = strpos($text,' ');
			
			if (!$has_space) {
				// the entire string is one word
				$first_half = substr($text, 0, $max_len/2);
				$last_half = substr($text, -($max_len - strlen($first_half)));

			} else {
				// Get last half first as it makes it more likely for the first
				// half to be of greater length. This is done because usually the
				// first half of a string is more recognizable. The last half can
				// be at most half of the maximum length and is potentially
				// shorter (the last word).

				$last_half = substr($text,-($max_len/2));
				$last_half = trim($last_half);
				$last_space = strrpos($last_half,' ');	// check if we chopped at a space
				if (!($last_space === false)) {
					$last_half = substr($last_half, $last_space + 1);
				}
				$first_half = substr($text, 0, $max_len-strlen($last_half));
				$first_half = trim($first_half);

				if (substr($text, $max_len - strlen($last_half), 1) == ' ')
					$first_space = $max_len-strlen($last_half);
				else
					$first_space=strrpos($first_half,' ');

				if (!($first_space === false))
					$first_half = substr($text, 0, $first_space);

			}

			return $first_half.$trim_chars.$last_half;

		}
	}

	// }}}
    // {{{ public static function removeTrailingPunctuation()

	/**
	 * Removes trailing punctuation from a string
	 *
	 * @param string $string the string to format remove punctuation from.
	 *
	 * @return string the string with trailing punctuation removed.
	 */
	public static function removeTrailingPunctuation($string)
	{
		return preg_replace('/\W+$/s', '', $string);
	}

	// }}}
    // {{{ public static function removeLeadingPunctuation()

	/**
	 * Removes leading punctuation from a string
	 *
	 * @param string $string the string to format remove punctuation from.
	 *
	 * @return string the string with leading punctuation removed.
	 */
	public static function removeLeadingPunctuation($string)
	{
		return preg_replace('/^\W+/s', '', $string);
	}

	// }}}
    // {{{ public static function removePunctuation()

	/**
	 * Removes both leading and trailing punctuation from a string
	 *
	 * @param string $string the string to format remove punctuation from.
	 *
	 * @return string the string with leading and trailing punctuation removed.
	 */
	public static function removePunctuation($string)
	{
		$string = SwatString::removeTrailingPunctuation($string);
		$string = SwatString::removeLeadingPunctuation($string);
		return $string;
	}
	
	// }}}
}

?>
