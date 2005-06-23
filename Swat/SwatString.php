<?php
// vim: set fdm=marker:

/**
 * String Tools
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
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
    // {{{ public static function smartTrim()

	/**
	 * Smart Trim
	 *
	 * @param string $text Text to trim
	 * @param integer $max_len Length to trim string at
	 * @param boolean $trim_middle Whether to trim the middle out of the string 
	 * @param string $trim_cars Text to append to the end of the trimmed string
	 *
	 * @return string the formatted string.
	 */
	public static function smartTrim($text, $max_len, $trim_middle=false, $trim_chars='...')
	{
		$text = trim($text);

		if (strlen($text) < $max_len) {
			return $text;

		} elseif($trim_middle) {
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

		} else {
			$trim_text = substr($text,0,$max_len);
			$trim_text = trim($trim_text);

			if (substr($text, $max_len, 1) == ' ')
				$last_space = $max_len;					// the string was chopped at a space.
			else
				$last_space = strrpos($trim_text, ' ');	// in PHP5, we can use 'offset' here -Mike

			if (!($last_space === false))
				$trim_text = substr($trim_text, 0, $last_space);
	
			return SwatString::removeTrailingPunctuation($trim_text).$trim_chars;
		}
	}
	// }}}
    // {{{ public static function removeTrailingPunctuation()

	/**
	 * Remove Trailing Punctuation
	 *
	 * @param string $text Text to format
	 *
	 * @return string the formatted string.
	 */
	public static function removeTrailingPunctuation($text)
	{
		return preg_replace("'[^a-zA-Z_0-9]+$'s",'',$text);
	}

	// }}}
    // {{{ public static function removeLeadingPunctuation()

	/**
	 * Remove Leading Punctuation
	 *
	 * @param string $text Text to format
	 *
	 * @return string the formatted string.
	 */
	public static function removeLeadingPunctuation($text)
	{
		return preg_replace("'^[^a-zA-Z_0-9]+'s",'',$text);
	}

	// }}}
    // {{{ public static function removePunctuation()

	/**
	 * Remove Punctuation (both leading and trailing)
	 *
	 * @param string $text Text to format
	 *
	 * @return string the formatted string.
	 */
	public static function removePunctuation($text)
	{
		$text = SwatString::removeTrailingPunctuation($text);
		$text = SwatString::removeLeadingPunctuation($text);
		return $text;
	}
	// }}}
}

?>
