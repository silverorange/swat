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
	// {{{ public static properties

	/**
	 * Block level XHTML elements used when filtering strings
	 *
	 * @var array
	 */
	public static $blocklevel_elements = array(
		'p',        'pre',      'dl',       'div',
		'center',   'noscript', 'noframes', 'blockquote',
		'form',     'isindex',  'hr',       'table',
		'fieldset', 'address',  'ul',       'ol',
		'dir',      'menu',     'h1',       'h2',
		'h3',       'h4',       'h5',       'h6');
	
	// }}}
	// {{{ filter constants
	
	/**
	 * replaces hard-returns with br and doubles with p
	 */
	const FILTER_BODY = 0;

	/**
	 * same as body, but doesn't ignore lines with tags on them
	 */
	const FILTER_B2 = 0;

	/// }}}
    // {{{ public static function filter()

	/**
	 * Filters a block of text
	 *
	 * Formatting function to control the display of a block of text
	 *
	 * @param integer $type the type of formatting to apply. One of the FILTER
	 *                       {@link SwatString} constants.
	 * @param string $text the text to format.
	 *
	 * @return string the formatted block of text.
	 */
	public static function filter($type, $text)
	{
		if ($type == self::FILTER_BODY) {
			// replace double crlf's with paragraph tags
			$text = preg_replace('/\r\n\r\n([^<])/s', '<p>\1', $text);
			// replace single crlf's with linebreak tags
			$text = preg_replace('/([^>])\r\n([^<])/s', '\1<br />\2', $text);
			return $text;

		} elseif ($type == self::FILTER_B2) {
			// replace double crlf's with paragraph tags
			$text = str_replace("\r\n\r\n", '<p>', $text);
			// replace single crlf's with linebreak tags
			$text = str_replace("\r\n", '<br />', $text);
			return $text;

		}
	}

	// }}}
	// {{{ public static function condense()

	/**
	 * Takes a block of text and condenses it into a small fragment of XHTML.
	 *
	 * Condensing text involves removing line breaks and replacing them with
	 * special characters. Other things are done like removing and replacing
	 * certain block level XHTML elements as well.
	 *
	 * @param string $text the text to be condensed.
	 * @param integer $max_length the maximum length of the condensed text.
	 *
	 * @return string the condensed text. The condensed text is an XHTML
	 *                 formatted string.
	 */
	public static function condense($text, $max_length = 300)
	{
		$blocklevel_elements = implode('|', self::$blocklevel_elements);
		$search = array(
			// replace blockquote tags with quotation marks
			'/<blockquote[^<>]*?>/si',
			'/<\/blockquote[^<>]*?>/si',
			// remove style tags
			'/<style[^<>]*?>.*?<\/style[^<>]*?>/si',
			// replace blocklevel tags with line breaks.
			'/<\/?('.$blocklevel_elements.')[^<>]*?>/si',
			// remove inline tags
			// (only tags remaining after blocklevel tags removed)
			'/<[\/\!]*?[^<>]*?>/s',
			// replace whitespaces with single spaces. \xa0 is &#160; is &nbsp;
			'/[ \xa0\t]+/',
			// replace continuous strings of whitespace containing either a
			// cr or lf with a non-breaking space padded bullet
			'/[\xa0\s]*[\n\r][\xa0\s]*/s'
		);

		$replace = array(
			"\n&#8220;",
			"&#8221;\n",
			'',
			"\n",
			'',
			' ',
			' &nbsp;&#8226;&nbsp; '
		);

		$text = trim($text);

		//$text = preg_replace('/(\r\n)+/s', ' &nbsp;&#8226;&nbsp; ', $text);
		$text = preg_replace($search, $replace, $text);

		$text = SwatString::ellipsizeRight($text, $max_length);

		return $text;
	}

	// }}}
	// {{{ public static function ellipsizeRight()

	/**
	 * Ellipsizes a string to the right
	 *
	 * The length of a string is calculated as the number of visible characters
	 * This method will properly account for any XHTML entities that may be
	 * present in the given string.
	 *
	 * TODO: make this method account for entities properly
	 *
	 * example:
	 * <code>
	 * $string = 'The quick brown fox jumped over the lazy dogs.';
	 * // displays 'The quick brown ...'
	 * echo SwatString::ellipsizeRight($string, 18, ' ...');
	 * </code>
	 *
	 * XHTML example:
	 * <code>
	 * $string = 'The &#8220;quick&#8221 brown fox jumped over the lazy dogs.';
	 * // displays 'The &#8220;quick&#8221; brown ...'
	 * echo SwatString::ellipsizeRight($string, 18, ' ...');
	 * </code>
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
		$matches = array();
		self::stripEntities($string, $matches);
		
		$string = trim($string);

		// don't ellipsize if the string is short enough
		if (strlen($string) <= $max_length)
			return $string;

		$search_offset = -max(strlen($string) - $max_length, 0);

		// find the last space up to the max_length in the string
		$chop_pos = strrpos($string, ' ', $search_offset);
		if ($chop_pos === false) $chop_pos = $max_length;

		$string = substr($string, 0, $chop_pos);
		$string = SwatString::removeTrailingPunctuation($string);
		$string .= $ellipses;

		self::insertEntities($string, $matches);

		return $string;
	}

	// }}}
    // {{{ public static function ellipsizeMiddle()

	/**
	 * Ellipsizes a string in the middle
	 *
	 * The length of a string is calculated as the number of visible characters
	 * This method will properly account for any XHTML entities that may be
	 * present in the given string.
	 *
	 * TODO: make this method account for entities properly
	 *
	 * example:
	 * <code>
	 * $string = 'The quick brown fox jumped over the lazy dogs.';
	 * // displays 'The quick ... dogs.'
	 * echo SwatString::ellipsizeMiddle($string, 18, ' ... ');
	 * </code>
	 *
	 * XHTML example:
	 * <code>
	 * $string = 'The &#8220;quick&#8221 brown fox jumped over the lazy dogs.';
	 * // displays 'The &#8220;quick&#8221; ... dogs.'
	 * echo SwatString::ellipsizeMiddle($string, 18, ' ... ');
	 * </code>
	 *
	 * @param string $string the string to ellipsize.
	 * @param integer $max_length the maximum length of the returned string.
	 *                             This length does not account for any ellipse
	 *                             characters that may be appended.
	 * @param string $ellipses the ellipses characters to insert if the string
	 *                          is shortened.
	 *
	 * @return string the ellipsized string. The ellipsized string may include
	 *                 ellipses characters in roughly the middle if it was
	 *                 longer than max_length.
	 */
	public static function ellipsizeMiddle($string, $max_length,
		$ellipses = '&nbsp;&#8230;&nbsp;')
	{
		$string = trim($string);

		if (strlen($string) <= $max_length)
			return $string;

		// check if the string is all one giant word
		$has_space = strpos($string, ' ');

		// the entire string is one word
		if ($has_space === false) {

			// just take a piece of the string from both ends
			$first_piece = substr($string, 0, $max_length / 2);
			$last_piece = substr($string,
					-($max_length - strlen($first_piece)));

		} else {

			/*
			 * Implementation Note:
			 *
			 * Get last piece first as it makes it more likely for the first
			 * piece to be of greater length. This is done because usually the
			 * first piece of a string is more recognizable.
			 *
			 * The length of the last piece can be at most half of the maximum
			 * length and is potentially shorter. The last half can be as short
			 * as the last word.
			 */

			/*
			 * Get the last piece as half the max_length starting from
			 * the right.
			 */
			$last_piece = substr($string, -($max_length / 2));
			$last_piece = trim($last_piece);

			/*
			 * Find the last word in the last piece.
			 * TODO: We may want to change this to select more of the end of
			 *       the string than the last word.
			 */
			$last_space = strrpos($last_piece, ' ');
			if ($last_space !== false)
				$last_piece = substr($last_piece, $last_space + 1);

			/*
			 * Get the first piece by ellipsizing with a max_length of
			 * the max_length less the length of the last piece.
			 */
			$max_first_length = $max_length - strlen($last_piece);

			$first_piece =
				self::ellipsizeRight($string, $max_first_length, '');
		}

		$string = $first_piece.$ellipses.$last_piece;

		return $string;
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
    // {{{ public static function moneyFormat()

	/**
	 * Formats a number as a currency formatted string
	 *
	 * @param float $value the value to format.
	 * @param string $locale an optional locale to use to format the value. If
	 *                        no locale is specified, the default PHP locale is
	 *                        used.
	 *
	 * @return string the money formatted string.
	 */
	public static function moneyFormat($value, $locale = null)
	{
		$old_locale = setlocale(LC_ALL, $locale);
		$format = htmlentities(money_format('%.2n', $number), null, 'UTF-8');	
		setlocale(LC_ALL, $old_locale);
		return $format;
	}

	// }}}
	// {{{ private static function stripEntities()

	/**
	 * Strips entities from a string remembering their positions
	 *
	 * Stripped entities are replaces with a single special character. All
	 * parameters are passed by reference and nothing is returned by this
	 * function.
	 *
	 * @param string $string the string to strip entites from.
	 * @param array $matches the array to store matches in.
	 */
	private static function stripEntities(&$string, &$matches)
	{
		$reg_exp = '/&#?[a-z0-9]*?;/s';

		preg_match_all($reg_exp, $string, $matches, PREG_OFFSET_CAPTURE);

		$string = preg_replace($reg_exp, '*', $string);
	}

	// }}}
	// {{{ private static function insertEntities()

	/**
	 * Re-inserts stripped entities into a string in the correct positions
	 *
	 * All parameters are passed by reference and nothing is returned by this
	 * function.
	 *
	 * @param string $string the string to re-insert entites into.
	 * @param array $matches the array of stored matches.
	 */
	private static function insertEntities(&$string, &$matches)
	{
		for ($i = 0; $i < count($matches[0]); $i++) {
			$length = strlen($string);
			
			$entity = $matches[0][$i][0];
			$position = $matches[0][$i][1];

			if ($position < $length) {
				$string = substr($string, 0, $position).$entity.substr($string, $position + 1);
			}
		}
	}

	// }}}
}

?>
