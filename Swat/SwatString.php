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
	// {{{ public static function toXHTML()

	/**
	 * Intelligently converts a text block to XHTML
	 *
	 * The text is converted as follows:
	 *
	 * - text blocks delimited by double line breaks is wrapped in a paragraph
	 *   tag
	 * - unless it is already inside a blocklevel tag
	 * - single line breaks are converted to break tags
	 *
	 * @param string $text the text block to convert to XHTML.
	 *
	 * @return string the text block converted to XHTML.
	 */
	public static function toXHTML($text)
	{
		$blocklevel_elements = implode('|', self::$blocklevel_elements);

		// regular expressions to match blocklevel tags
		$starting_blocklevel = '/^<('.$blocklevel_elements.')[^<>]*?>/siu';
		$ending_blocklevel = '/<\/('.$blocklevel_elements.')[^<>]*?>$/siu';

		// convert input from windows and mac
		$text = str_replace("\r\n", "\n", $text);
		$text = str_replace("\r", "\n", $text);

		// replace continuous strings of whitespace containing a
		// double lf with two line breaks
		$text = preg_replace('/[\xa0\s]*\n\n[\xa0\s]*/su', "\n\n", $text);

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

		$text = preg_replace('/([^\n])\n([^\n])/su', '\1<br />\2', $text);

		$text = rtrim($text);

		return $text;
	}

	// }}}
	// {{{ public static function minimizeEntities()

	/**
	 * Converts a UTF-8 text string to have the minimal number of entities
	 * necessary to output it as valid UTF-8 XHTML without ever double-escaping.
	 *
	 * The text is converted as follows:
	 *
	 * - any exisiting entities are decoded to their UTF-8 characaters
	 * - the minimal number of characters necessary are then escaped as entities:
	 *         ampersands (&) => &amp;
	 *          less than (<) => &lt;
	 *       greater than (>) => &gt; 
	 *	     double quote (") => &quot;
	 *
	 * @param string $text the UTF-8 text string to convert.
	 *
	 * @return string the UTF-8 text string with minimal entities.
	 */
	public static function minimizeEntities($text)
	{
		// decode any entities that might already exist
		$text= html_entity_decode($text, ENT_COMPAT, 'UTF-8');

		// encode all ampersands (&), less than (<), greater than (>), 
		// and double quote (") characters as their XML entities 
		$text = htmlspecialchars($text, ENT_COMPAT, 'UTF-8');

		return $text;
	}

	// }}}
	// {{{ public static function minimizeEntitiesWithTags()

	/**
	 * Same as SwatString::minimizeEntities() but also accepts a list of tags
	 * to preserve.
	 *
	 * @param string $text the UTF-8 text string to convert.
	 * @param array $tags names of tags that should be preserved.
	 *
	 * @return string the UTF-8 text string with minimal entities.
	 */
	public static function minimizeEntitiesWithTags($text, $tags)
	{
		$pattern = sprintf('/(<\/?(%s) ?.*>)/uU', implode('|', $tags));

		$parts = preg_split($pattern, $text, -1, PREG_SPLIT_DELIM_CAPTURE);
		$output = '';

		foreach ($parts as $index => $part) {
		    switch ($index % 3) {
	        case 0:
    	        // the stuff in between
	            $output.= self::minimizeEntities($part);
    	        break;
	        case 1:
    	        // a valid tag
	            $output.= $part;
	            break;
		    }
		}

		return $output;
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
			'/<blockquote[^<>]*?>/siu',
			'/<\/blockquote[^<>]*?>/siu',
			// remove style tags
			'/<style[^<>]*?>.*?<\/style[^<>]*?>/siu',
			// replace blocklevel tags with line breaks.
			'/<\/?('.$blocklevel_elements.')[^<>]*?>/siu',
			// remove inline tags
			// (only tags remaining after blocklevel tags removed)
			'/<[\/\!]*?[^<>]*?>/su',
			// replace whitespaces with single spaces. \xa0 is &#160; is &nbsp;
			'/[ \xa0\t]+/u'
		);

		$replace = array(
			"\n“",
			"”\n",
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
			'/[\xa0\s]*[\n\r][\xa0\s]*/su';

		$replace = 
			// the spaces around the bullet are non-breaking spaces
			'  •  ';

		$text = preg_replace($search, $replace, $text);

		$text = SwatString::ellipsizeRight($text, $max_length);

		return $text;
	}

	// }}}
	// {{{ public static function condenseToName()
	
	/**
	 * Condenses a string to a name
	 *
	 * The generated name can be used for things like databsae identifiers and
	 * site URL fragments.
	 *
	 * example:
	 * <code>
	 * $string = 'The quick brown fox jumped over the lazy dogs.';
	 * // displays 'thequickbrown'
	 * echo SwatString::condenseToName($string);
	 * </code>
	 *
	 * @param string $string the string to condense to a name.
	 * @param integer $max_length the maximum length of the condensed name.
	 *
	 * @return string the string condensed into a name.
	 */
	public static function condenseToName($string, $max_length = 15)
	{
		if (strlen($string) == 0)
			return $string;

		// remove tags and make lowercase
		$string = strip_tags(strtolower($string));

		// remove html entities, convert non-alpha-numeric characters to spaces
		// and condense whitespace
		$search = array('/&#?\w+;/u', '/[^a-z0-9 ]/u', '/\s+/u');
		$replace = array('', ' ', ' ');

		$string = preg_replace($search, $replace, $string);

		$string_exp = explode(' ', $string);

		// first word too long, so forced to chop it
		if (strlen($string_exp[0]) >= $max_length)
			return substr($string_exp[0], 0 , $max_length);

		$string_out = '';

		foreach ($string_exp as $word) {
			// this word would push us over the limit
			if (strlen($string_out) + strlen($word) > $max_length)
				return $string_out;

			$string_out .= $word;
		}

		return $string_out;
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
		// the space is a non-breaking space
		$ellipses = ' …')
	{
		$matches = array();
		self::stripEntities($string, $matches);

		$string = trim($string);

		// don't ellipsize if the string is short enough
		if (strlen($string) <= $max_length)
			return $string;

		// note: if strrpos worked the same using mb_string overloading the
		// following code would be a lot simpler.

		// chop at max length
		$string = substr($string, 0, $max_length);
		
		// find the last space up to the max_length in the string
		$chop_pos = strrpos($string, ' ');

		if ($chop_pos !== false)
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
		// the spaces are non-breaking spaces
		$ellipses = ' … ')
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
		return preg_replace('/\W+$/su', '', $string);
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
		return preg_replace('/^\W+/su', '', $string);
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
		if ($locale !== null) {
			$old_locale = setlocale(LC_ALL, 0);
			setlocale(LC_ALL, $locale);
		}

		$format = htmlentities(money_format('%.2n', $value), null, 'UTF-8');

		if ($locale !== null)
			setlocale(LC_ALL, $old_locale);

		return $format;
	}

	// }}}
	// {{{ public static function numberFormat()

	/**
	 * Formats a number using locale seperators and in a UTF-8 safe way.
	 *
	 * @param float $value the value to format.
	 * @param int $decimals number of decimal places to display (default 0).
	 * @param string $locale an optional locale to use to format the value. If
	 *                        no locale is specified, the default PHP locale is
	 *                        used.
	 * @param boolean $show_thousands_seperator whether to display the 
	 *                        thousands seperator (default true).
	 *
	 * @return string the money formatted string.
	 */
	public static function numberFormat($value, $decimals = 0, $locale = null, 
		$show_thousands_seperator = true)
	{
		// number_format can't handle UTF-8 seperators, so insert placeholders
		$output = number_format($value, $decimals, '.', $show_thousands_seperator ? ',' : null);
		$output = htmlentities($output, null, 'UTF-8');

		if ($locale !== null) {
			$old_locale = setlocale(LC_ALL, 0);
			setlocale(LC_ALL, $locale);
		}

		// replace placeholder seperators with locale ones which might contain non-ASCII
		$lc = localeconv();
		$output = str_replace('.', $lc['decimal_point'], $output);
		$output = str_replace(',', $lc['thousands_sep'], $output);

		if ($locale !== null)
			setlocale(LC_ALL, $old_locale);

		return $output;
	}

	// }}}
	// {{{ public static function pad()

	/**
	 * Pads a string in a UTF-8 safe way.
	 *
	 * @param string $input the string to pad.
	 * @param int $pad_length length in characters to pad to.
	 * @param string $pad_string string to use for padding.
	 * @param int $pad_type type of padding to use: STR_PAD_LEFT, 
	 *                       STR_PAD_RIGHT, or STR_PAD_BOTH.
	 *
	 * @return string the padded string.
	 */
	public static function pad($input, $pad_length, $pad_string = ' ', 
		$pad_type = STR_PAD_RIGHT)
	{
		$output = '';
		$length = $pad_length - strlen($input);

		if ($pad_string === null || strlen($pad_string) == 0)
			$pad_string = ' ';

		if ($length > 0) {
			switch ($pad_type) {
			case STR_PAD_LEFT:
				$padding = str_repeat($pad_string, ceil($length / strlen($pad_string)));
				$output = substr($padding, 0, $length) . $input;
				break;

			case STR_PAD_BOTH:
				$left_length = floor($length / 2);
				$right_length = ceil($length / 2);
				$padding = str_repeat($pad_string, ceil($right_length / strlen($pad_string)));
				$output = substr($padding, 0, $left_length) . $input . 
					substr($padding, 0, $right_length);
				break;

			case STR_PAD_RIGHT:
			default:
				$padding = str_repeat($pad_string, ceil($length / strlen($pad_string)));
				$output = $input . substr($padding, 0, $length);
			}
		} else {
           $output = $input;
		}
		return $output;
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
		$reg_exp = '/&#?[a-z0-9]*?;/su';

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

	// }}}
}

?>
