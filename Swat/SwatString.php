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
		'p',          'pre',     'dl',     'div',
		'blockquote', 'form',    'h[1-6]', 'table',
		'fieldset',   'address', 'ul',     'ol');

	// }}}
	// {{{ public function toXHTML()

	/**
	 * Intelligently converts a text block to XHTML
	 *
	 * The text is converted as follows:
	 *
	 * - text blocks delimited by double line breaks is wrapped in a paragraph
	 *   tag
	 * - unless it is already inside a blocklevel tag
	 *
	 * @param string $text the text block to convert to XHTML.
	 *
	 * @return string the text block converted to XHTML.
	 */
	public function toXHTML($text)
	{
		$blocklevel_elements = implode('|', self::$blocklevel_elements);

		// regular expressions to match blocklevel tags
		$starting_blocklevel = '/^<('.$blocklevel_elements.')[^<>]*?>/si';
		$ending_blocklevel = '/<\/('.$blocklevel_elements.')[^<>]*?>$/si';

		// convert input from windows and mac
		$text = str_replace("\r\n", "\n", $text);
		$text = str_replace("\r", "\n", $text);

		// replace continuous strings of whitespace containing a
		// double lf with two line breaks
		$text = preg_replace('/[\xa0\s]*\n\n[\xa0\s]*/s', "\n\n", $text);

		$paragraphs = explode("\n\n", $text);

		$in_blocklevel = false;
		foreach($paragraphs as &$paragraph) {
			$blocklevel_started =
				(preg_match($starting_blocklevel, $paragraph) == 1);
				
			$blocklevel_ended =
				(preg_match($ending_blocklevel, $paragraph) == 1);
				
			if ($blocklevel_started)
				$in_blocklevel = true;

			// don't wrap this paragraph in <p> tags if we are in a blocklevel
			// tag already
			if ($in_blocklevel) {
				$paragraph = $paragraph."\n\n";
			} else {
				$paragraph = '<p>'.$paragraph."</p>\n\n";
			}

			if ($blocklevel_ended)
				$in_blocklevel = false;
		}

		$text = implode('', $paragraphs);

		$text = preg_replace('/([^\n])\n([^\n])/s', '\1<br />\2', $text);

		$text = rtrim($text);

		return $text;
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
			'/[ \xa0\t]+/'
		);

		$replace = array(
			"\n&#8220;",
			"&#8221;\n",
			'',
			"\n",
			'',
			' '
		);

		$text = preg_replace($search, $replace, $text);

		$text = trim($text);

		$search =
			// replace continuous strings of whitespace containing either a
			// cr or lf with a non-breaking space padded bullet
			'/[\xa0\s]*[\n\r][\xa0\s]*/s';

		$replace = 
			' &nbsp;&#8226;&nbsp; ';

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

		self::insertEntities($string, $matches, strlen($string));

		$string .= $ellipses;

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

		$matches = array();
		self::stripEntities($string, $matches);

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

		$hole_start = strlen($first_piece);
		$hole_end = strlen($string) - strlen($last_piece);
		$hole_length = strlen($ellipses);

		$string = $first_piece.$ellipses.$last_piece;

		self::insertEntities($string, $matches,
			$hole_start, $hole_end, $hole_length);

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

		$string = preg_replace($reg_exp, 's', $string);
	}

	// }}}
	// {{{ private static function insertEntities()

	/**
	 * Re-inserts stripped entities into a string in the correct positions
	 *
	 * The first two parameters are passed by reference and nothing is returned
	 * by this function.
	 *
	 * @param string $string the string to re-insert entites into.
	 * @param array $matches the array of stored matches.
	 * @param integer $hole_start ignore inserting entities between here
	 *                             and hole_end.
	 * @param integer $hole_end ignore inserting entities between here
	 *                             and hole_start.
	 * @param integer $hole_length the length of the new contents of the hole.
	 */
	private static function insertEntities(&$string, &$matches,
		$hole_start = -1, $hole_end = -1, $hole_length = 0)
	{
		for ($i = 0; $i < count($matches[0]); $i++) {
			$entity = $matches[0][$i][0];
			$position = $matches[0][$i][1];

			if ($position < $hole_start) {
				// this entity falls before the hole
				$string = substr($string, 0, $position).
					$entity.
					substr($string, $position + 1);

				$hole_start += strlen($entity);
				$hole_end += strlen($entity);
			} elseif ($hole_end <= $hole_start) {
				// break here because all remaining entities fall in the hole
				// extending infinitely to the right
				break;
			} elseif ($position >= $hole_end) {
				// this entity falls after the hole
				$offset = -$hole_end + $hole_length + $hole_start + 1; 
				$string = substr($string, 0, $position + $offset).
					$entity.
					substr($string, $position + $offset + 1);

			} else {
				// this entity falls in the hole but we must account
				// for its unused size
				$hole_end += strlen($entity);
			}
		}
	}

	// }}}
	// {{{ public static function toInteger()

	/**
	 * Convert a locale-formatted number and return it as an integer.
	 *
	 * If the string is not an integer, the method returns null.
	 *
	 * @param string $string the string to convert.
	 *
	 * @return integer The converted value.
	 */
	public static function toInteger($string)
	{
		$lc = localeconv();

		$string = self::parseNegativeNotation($string);

		// change all locale formatting to numeric formatting
		$remove_parts = array(
			$lc['positive_sign'] => '',
			$lc['thousands_sep'] => '');

		$value = str_replace(array_keys($remove_parts),
			array_values($remove_parts), $string);

		// note: This might be done better with a regexp, though
		// checking too closely how well a number matches its locale
		// formatting could become annoying too. i.e. if 1000 was
		// rejected because it wasn't formatted 1,000

		return (is_int($value)) ? intval($value) : null;
	}
	// }}}
	// {{{ public static function toFloat()

	/**
	 * Convert a locale-formatted number and return it as an float.
	 *
	 * If the string is not an float, the method returns null.
	 *
	 * @param string $string the string to convert.
	 *
	 * @return float The converted value.
	 */
	public static function toFloat($string)
	{
		$lc = localeconv();

		$string = self::parseNegativeNotation($string);

		// change all locale formatting to numeric formatting
		$remove_parts = array(
			$lc['decimal_point'] => '.',
			$lc['positive_sign'] => '',
			$lc['thousands_sep'] => '');

		$value = str_replace(array_keys($remove_parts),
			array_values($remove_parts), $string);

		// note: This might be done better with a regexp, though
		// checking too closely how well a number matches its locale
		// formatting could become annoying too. i.e. if 1000 was
		// rejected because it wasn't formatted 1,000

		return (is_numeric($value)) ? floatval($value) : null;
	}
	// }}}
	//{{{ private static function parseNegativeNotation
	private static function parseNegativeNotation($string)
	{
		$lc = localeconv();

		switch ($lc['n_sign_posn']) {
			// negative sign shown as: (5.00)
			case 0:
				if (strpos($string, '(') !== false)
					return '-'.str_replace(
						array('(', ')'), array(), $string);
				break;

			// negative sign trails number: 5.00-
			case 2:
				if ($lc['negative_sign'] != '' &&
					strpos($string, $lc['negative_sign']) !== false)
					return '-'.str_replace(
						$lc['negative_sign'], '', $string);
				break;
			// negative sign prefixes number: -5.00
			default:
				if ($lc['negative_sign'] != '' &&
					strpos($string, $lc['negative_sign']) !== false)
					return str_replace(
						$lc['negative_sign'], '-', $string);
		}

		return $string;
	}
}

?>
