<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/Swat.php';

/**
 * Date class and PEAR-compatibility layer
 *
 * Notable unsupported features:
 * - leap-seconds
 * - microseconds
 * - localization
 *
 * @package   Swat
 * @copyright 2005-2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDate extends DateTime implements Serializable
{
	// {{{ time zone format constants

	/**
	 * America/Halifax
	 */
	const TZ_ID                     = 1;

	/**
	 * AST
	 */
	const TZ_SHORT                  = 2;

	/**
	 * Alias for {@link SwatDate::TZ_SHORT}
	 *
	 * @deprecated
	 */
	const TZ_LONG                   = 3;

	/**
	 * ADT
	 */
	const TZ_DST_SHORT              = 4;

	/**
	 * Alias for {@link SwatDate::TZ_DST_SHORT}
	 *
	 * @deprecated
	 */
	const TZ_DST_LONG               = 5;

	/**
	 * AST/ADT
	 */
	const TZ_COMBINED               = 6;

	/**
	 * AST or ADT, depending on whether or not the date is in daylight time
	 */
	const TZ_CURRENT_SHORT          = 7;

	/**
	 * Alias for {@link SwatDate::TZ_CURRENT_SHORT}
	 *
	 * @deprecated
	 */
	const TZ_CURRENT_LONG           = 8;

	// }}}
	// {{{ date format constants

	/**
	 * 07/02/02
	 */
	const DF_MDY                    = 1;

	/**
	 * 070202
	 */
	const DF_MDY_SHORT              = 2;

	/**
	 * July 2, 2002
	 */
	const DF_DATE                   = 3;

	/**
	 * Tuesday, July 2, 2002
	 */
	const DF_DATE_LONG              = 4;

	/**
	 * July 2, 2002 10:09 am
	 */
	const DF_DATE_TIME              = 5;

	/**
	 * Tuesday, July 2, 2002 10:09 am
	 */
	const DF_DATE_TIME_LONG         = 6;

	/**
	 * 10:09 am
	 */
	const DF_TIME                   = 7;

	/**
	 * Aug 5, 2002
	 */
	const DF_DATE_SHORT             = 8;

	/**
	 * Aug 5
	 */
	const DF_DATE_SHORT_NOYEAR      = 9;

	/**
	 * Aug 5, 2002 10:09 am
	 */
	const DF_DATE_TIME_SHORT        = 10;

	/**
	 * Aug 5, 10:09 am
	 */
	const DF_DATE_TIME_SHORT_NOYEAR = 11;

	/**
	 * August 2002
	 */
	const DF_MY                     = 12;

	/**
	 * 08 / 2002
	 */
	const DF_CC_MY                  = 13;

	/**
	 * 2002
	 */
	const DF_Y                      = 14;

	/**
	 * 20020822T180526Z
	 */
	const DF_ISO_8601_BASIC         = 15;

	/**
	 * 2002-08-22T18:05:26Z
	 */
	const DF_ISO_8601_EXTENDED      = 16;

	/**
	 * Thu, 22 Aug 2002 18:05:26 Z
	 */
	const DF_RFC_2822               = 17;

	// }}}
	// {{{ ISO 8601 option constants

	/**
	 * Value to use for no options.
	 *
	 * @see SwatDate::getISO8601()
	 */
	const ISO_BASIC     = 0;

	/**
	 * Include '-' and ':' separator characters.
	 *
	 * @see SwatDate::getISO8601()
	 */
	const ISO_EXTENDED  = 1;

	/**
	 * Include microseconds.
	 *
	 * @see SwatDate::getISO8601()
	 */
	const ISO_MICROTIME = 2;

	/**
	 * Include time zone offset.
	 *
	 * Time zone offset will be 'Z' for 0, otherwise
	 * will be +-HH:MM.
	 *
	 * @see SwatDate::getISO8601()
	 */
	const ISO_TIME_ZONE = 4;

	// }}}
	// {{{ date interval part constants

	/**
	 * A set of bitwise contants to control which parts of the interval we want
	 * when returning a DateInterval.
	 *
	 * @see SwatString::getHumanReadableTimePeriodParts()
	 */
	const DI_YEARS   = 1;
	const DI_MONTHS  = 2;
	const DI_WEEKS   = 4;
	const DI_DAYS    = 8;
	const DI_HOURS   = 16;
	const DI_MINUTES = 32;
	const DI_SECONDS = 64;

	// }}}
	// {{{ protected properties

	static $tz_abbreviations = null;
	static $valid_tz_abbreviations = array(
		'acdt'  => true,
		'acst'  => true,
		'act'   => true,
		'adt'   => true,
		'aedt'  => true,
		'aest'  => true,
		'aft'   => true,
		'akdt'  => true,
		'akst'  => true,
		'amst'  => true,
		'amt'   => true,
		'art'   => true,
		'ast'   => true,
		'ast'   => true,
		'ast'   => true,
		'ast'   => true,
		'awdt'  => true,
		'awst'  => true,
		'azost' => true,
		'azt'   => true,
		'bdt'   => true,
		'biot'  => true,
		'bit'   => true,
		'bot'   => true,
		'brt'   => true,
		'bst'   => true,
		'bst'   => true,
		'btt'   => true,
		'cat'   => true,
		'cct'   => true,
		'cdt'   => true,
		'cedt'  => true,
		'cest'  => true,
		'cet'   => true,
		'chast' => true,
		'cist'  => true,
		'ckt'   => true,
		'clst'  => true,
		'clt'   => true,
		'cost'  => true,
		'cot'   => true,
		'cst'   => true,
		'cst'   => true,
		'cvt'   => true,
		'cxt'   => true,
		'chst'  => true,
		'dst'   => true,
		'dft'   => true,
		'east'  => true,
		'eat'   => true,
		'ect'   => true,
		'ect'   => true,
		'edt'   => true,
		'eedt'  => true,
		'eest'  => true,
		'eet'   => true,
		'est'   => true,
		'fjt'   => true,
		'fkst'  => true,
		'fkt'   => true,
		'galt'  => true,
		'get'   => true,
		'gft'   => true,
		'gilt'  => true,
		'git'   => true,
		'gmt'   => true,
		'gst'   => true,
		'gyt'   => true,
		'hadt'  => true,
		'hast'  => true,
		'hkt'   => true,
		'hmt'   => true,
		'hst'   => true,
		'irkt'  => true,
		'irst'  => true,
		'ist'   => true,
		'ist'   => true,
		'ist'   => true,
		'jst'   => true,
		'krat'  => true,
		'kst'   => true,
		'lhst'  => true,
		'lint'  => true,
		'magt'  => true,
		'mdt'   => true,
		'mit'   => true,
		'msd'   => true,
		'msk'   => true,
		'mst'   => true,
		'mst'   => true,
		'mst'   => true,
		'mut'   => true,
		'ndt'   => true,
		'nft'   => true,
		'npt'   => true,
		'nst'   => true,
		'nt'    => true,
		'omst'  => true,
		'pdt'   => true,
		'pett'  => true,
		'phot'  => true,
		'pkt'   => true,
		'pst'   => true,
		'pst'   => true,
		'ret'   => true,
		'samt'  => true,
		'sast'  => true,
		'sbt'   => true,
		'sct'   => true,
		'slt'   => true,
		'sst'   => true,
		'sst'   => true,
		'taht'  => true,
		'tha'   => true,
		'utc'   => true,
		'uyst'  => true,
		'uyt'   => true,
		'vet'   => true,
		'vlat'  => true,
		'wat'   => true,
		'wedt'  => true,
		'west'  => true,
		'wet'   => true,
		'yakt'  => true,
		'yekt'  => true,
	);

	// }}}
	// {{{ public function format()

	/**
	 * Formats this date given either a format string or a format id
	 *
	 * Note: The results of this method are not localized. For a localized
	 * formatted date, use {@link SwatDate::formatLikeIntl()} or
	 * {@link SwatDate::formatLikeStrftime()}.
	 *
	 * @param mixed $format either a format string or an integer format id.
	 * @param integer $tz_format optional time zone format id.
	 *
	 * @return string the formatted date.
	 */
	public function format($format, $tz_format = null)
	{
		if (is_int($format)) {
			$format = self::getFormatById($format);
		}

		$out = parent::format($format);

		if ($tz_format !== null) {
			$out.= ' '.$this->formatTZ($tz_format);
		}

		return $out;
	}

	// }}}
	// {{{ public function formatLikeStrftime()

	/**
	 * Formats this date like strftime() given either a format string or a
	 * format id
	 *
	 * This method returns localized results.
	 *
	 * @param mixed $format either a format string or an integer format id.
	 * @param integer $tz_format optional. A time zone format id.
	 * @param string $locale optional. The locale to use to format the date.
	 *                        If not specified, the current locale is used.
	 *
	 * @return string the formatted date according to the current locale.
	 */
	public function formatLikeStrftime($format, $tz_format = null,
		$locale = null)
	{
		if (is_int($format)) {
			$format = self::getFormatLikeStrftimeById($format);
		}

		if ($locale != '') {
			$old_locale = setlocale(LC_ALL, 0);
			setlocale(LC_ALL, $locale);
		}

		$timestamp = $this->getTimestamp();
		$out = strftime($format, $timestamp);

		if ($tz_format !== null) {
			$out.= ' '.$this->formatTZ($tz_format);
		}

		if ($locale != '') {
			setlocale(LC_ALL, $old_locale);
		}

		return $out;
	}

	// }}}
	// {{{ public function formatLikeIntl()

	/**
	 * Formats this date using the ICU IntlDateFormater given either a format
	 * string or a format id
	 *
	 * This method returns localized results.
	 *
	 * @param mixed $format either a format string or an integer format id.
	 * @param integer $tz_format optional. A time zone format id.
	 * @param string $locale optional. The locale to use to format the date.
	 *                        If not specified, the current locale is used.
	 *
	 * @return string the formatted date according to the current locale.
	 */
	public function formatLikeIntl($format, $tz_format = null,
		$locale = null)
	{
		if (is_int($format)) {
			$format = self::getFormatLikeIntlById($format);
		}

		if ($locale == '') {
			$locale = setlocale(LC_TIME, 0);
		}

		static $formatters = array();

		if (!isset($formatters[$locale])) {
			$formatters[$locale] = new IntlDateFormatter(
				$locale,
				IntlDateFormatter::FULL,
				IntlDateFormatter::FULL);
		}

		$formatter = $formatters[$locale];
		$formatter->setTimeZoneId($this->getTimezone()->getName());
		$formatter->setPattern($format);

		$timestamp = $this->getTimestamp();
		$out = $formatter->format($timestamp);

		if ($tz_format !== null) {
			$out.= ' '.$this->formatTZ($tz_format);
		}

		return $out;
	}

	// }}}
	// {{{ public function formatTZ()

	/**
	 * Formats the time zone part of this date
	 *
	 * @param integer $format an integer time zone format id.
	 *
	 * @return string the formatted time zone.
	 */
	public function formatTZ($format)
	{
		$out = '';

		switch ($format) {
		case self::TZ_ID:
			$out = $this->format('e');
			break;

		case self::TZ_SHORT:
		case self::TZ_LONG:
			$id = $this->format('e');
			$abbreviations = self::getTimeZoneAbbreviations();
			if (isset($abbreviations[$id]) &&
				isset($abbreviations[$id]['st'])) {
				$out = $abbreviations[$id]['st'];
			}
			break;

		case self::TZ_DST_SHORT:
		case self::TZ_DST_LONG:
			$id = $this->format('e');
			$abbreviations = self::getTimeZoneAbbreviations();
			if (isset($abbreviations[$id])) {
				if (isset($abbreviations[$id]['dt'])) {
					$out = $abbreviations[$id]['dt'];
				} else {
					$out = $abbreviations[$id]['st'];
				}
			}
			break;

		case self::TZ_CURRENT_SHORT:
		case self::TZ_CURRENT_LONG:
			$out = $this->format('T');
			break;

		case self::TZ_COMBINED:
			$out = array();
			$id = $this->format('e');
			$abbreviations = self::getTimeZoneAbbreviations();
			if (isset($abbreviations[$id])) {
				if (isset($abbreviations[$id]['st'])) {
					$out[] = $abbreviations[$id]['st'];
				}
				if (isset($abbreviations[$id]['dt'])) {
					$out[] = $abbreviations[$id]['dt'];
				}
			}
			$out = implode('/', $out);
			break;
		}

		return $out;
	}

	// }}}
	// {{{ public function clearTime() - deprecated

	/**
	 * Clears the time portion of the date object
	 *
	 * @deprecated Use <kbd>SwatDate::setTime(0, 0, 0);</kbd> instead.
	 */
	public function clearTime()
	{
		$this->setTime(0, 0, 0);
	}

	// }}}
	// {{{ public function __toString()

	public function __toString()
	{
		return $this->format('Y-m-d\TH:i:s');
	}

	// }}}
	// {{{ public function getHumanReadableDateDiff()

	/**
	 * Get a human-readable string representing the difference between
	 * two dates
	 *
	 * This method formats the date diff as the difference of seconds,
	 * minutes, hours, or days between two dates. The closest major date
	 * part will be used for the return value. For example, a difference of
	 * 50 seconds returns "50 seconds" while a difference of 90 seconds
	 * returns "1 minute".
	 *
	 * @param SwatDate $compare_date Optional date to compare to. If null, the
	 *                               the current date/time will be used.
	 *
	 * @return string A human-readable date diff.
	 */
	public function getHumanReadableDateDiff(SwatDate $compare_date = null)
	{
		if ($compare_date === null) {
			$compare_date = new SwatDate();
		}

		$seconds = $compare_date->getTime() - $this->getTime();
		return SwatString::toHumanReadableTimePeriod($seconds, true);
	}

	// }}}
	// {{{ public function getHumanReadableDateDiffWithWeeks()

	/**
	 * Get a human-readable string representing the difference between
	 * two dates
	 *
	 * This method formats the date diff as the difference of seconds,
	 * minutes, hours, or days and weeks between two dates. The closest major
	 * date part will be used for the return value. For example, a difference of
	 * 50 seconds returns "50 seconds" while a difference of 90 seconds
	 * returns "1 minute".
	 *
	 * @param SwatDate $compare_date Optional date to compare to. If null, the
	 *                               the current date/time will be used.
	 *
	 * @return string A human-readable date diff.
	 */
	public function getHumanReadableDateDiffWithWeeks(
		SwatDate $compare_date = null)
	{
		if ($compare_date === null) {
			$compare_date = new SwatDate();
		}

		$seconds = $compare_date->getTime() - $this->getTime();
		return SwatString::toHumanReadableTimePeriodWithWeeks($seconds, true);
	}

	// }}}
	// {{{ public function getHumanReadableDateDiffWithWeeksAndDays()

	/**
	 * Get a human-readable string representing the difference between
	 * two dates
	 *
	 * This method formats the date diff as the difference of seconds,
	 * minutes, hours, or days and weeks between two dates. The closest major
	 * date part will be used for the return value. For example, a difference of
	 * 50 seconds returns "50 seconds" while a difference of 90 seconds
	 * returns "1 minute".
	 *
	 * @param SwatDate $compare_date Optional date to compare to. If null, the
	 *                               the current date/time will be used.
	 *
	 * @return string A human-readable date diff.
	 */
	public function getHumanReadableDateDiffWithWeeksAndDays(
		SwatDate $compare_date = null)
	{
		if ($compare_date === null) {
			$compare_date = new SwatDate();
		}

		$seconds = $compare_date->getTime() - $this->getTime();
		return SwatString::toHumanReadableTimePeriodWithWeeksAndDays($seconds);
	}

	// }}}
	// {{{ public static function getFormatById()

	/**
	 * Gets a date format string by id
	 *
	 * @param integer $id the id of the format string to retrieve.
	 *
	 * @return string the formatting string that was requested.
	 *
	 * @throws SwatException
	 */
	public static function getFormatById($id)
	{
		// Note: The format() method does not localize results, so these
		// format codes are _not_ wrapped in gettext calls.

		switch ($id) {
		case self::DF_MDY:
			return 'm/d/y';
		case self::DF_MDY_SHORT:
			return 'mdy';
		case self::DF_DATE:
			return 'F j, Y';
		case self::DF_DATE_LONG:
			return 'l, F j, Y';
		case self::DF_DATE_TIME:
			return 'F j, Y g:i a';
		case self::DF_DATE_TIME_LONG:
			return 'l, F j, Y g:i a';
		case self::DF_TIME:
			return 'g:i a';
		case self::DF_DATE_SHORT:
			return 'M j, Y';
		case self::DF_DATE_SHORT_NOYEAR:
			return 'M j';
		case self::DF_DATE_TIME_SHORT:
			return 'M j, Y g:i a';
		case self::DF_DATE_TIME_SHORT_NOYEAR:
			return 'M j, g:i a';
		case self::DF_MY:
			return 'F Y';
		case self::DF_CC_MY:
			return 'm / Y';
		case self::DF_Y:
			return 'Y';
		case self::DF_ISO_8601_BASIC:
			return 'Ymd\THis';
		case self::DF_ISO_8601_EXTENDED:
			return 'Y-m-d\TH:i:s';
		case self::DF_RFC_2822:
			return 'r';
		default:
			throw new Exception("Unknown date format id '$id'.");
		}
	}

	// }}}
	// {{{ public static function getFormatLikeStrftimeById()

	/**
	 * Gets a strftime() date format string by id
	 *
	 * @param integer $id the id of the format string to retrieve.
	 *
	 * @return string the formatting string that was requested.
	 *
	 * @throws SwatException
	 */
	public static function getFormatLikeStrftimeById($id)
	{
		switch ($id) {
		case self::DF_MDY:
			return Swat::_('%m/%d/%y');
		case self::DF_MDY_SHORT:
			return Swat::_('%m%d%y');
		case self::DF_DATE:
			return Swat::_('%B %e, %Y');
		case self::DF_DATE_LONG:
			return Swat::_('%A, %B %e, %Y');
		case self::DF_DATE_TIME:
			return Swat::_('%B %e, %Y %i:%M %p');
		case self::DF_DATE_TIME_LONG:
			return Swat::_('%A, %B %e, %Y %i:%M %p');
		case self::DF_TIME:
			return Swat::_('%i:%M %p');
		case self::DF_DATE_SHORT:
			return Swat::_('%b %e %Y');
		case self::DF_DATE_SHORT_NOYEAR:
			return Swat::_('%b %e');
		case self::DF_DATE_TIME_SHORT:
			return Swat::_('%b %e, %Y %i:%M %p');
		case self::DF_DATE_TIME_SHORT_NOYEAR:
			return Swat::_('%b %e, %i:%M %p');
		case self::DF_MY:
			return Swat::_('%B %Y');
		case self::DF_CC_MY:
			return Swat::_('%m / %Y');
		case self::DF_Y:
			return Swat::_('%Y');
		case self::DF_ISO_8601_BASIC:
			return Swat::_('%Y%m%dT%H%M%S');
		case self::DF_ISO_8601_EXTENDED:
			return Swat::_('%Y-%m-%dT%H:%M:%S');
		case self::DF_RFC_2822:
			return Swat::_('%a, %d %b %Y %T');
		default:
			throw new Exception("Unknown date format id '$id'.");
		}
	}

	// }}}
	// {{{ public static function getFormatLikeIntlById()

	/**
	 * Gets a strftime() date format string by id
	 *
	 * @param integer $id the id of the format string to retrieve.
	 *
	 * @return string the formatting string that was requested.
	 *
	 * @throws SwatException
	 */
	public static function getFormatLikeIntlById($id)
	{
		switch ($id) {
		case self::DF_MDY:
			return Swat::_('MM/dd/yy');
		case self::DF_MDY_SHORT:
			return Swat::_('MMddyy');
		case self::DF_DATE:
			return Swat::_('MMMM d, yyyy');
		case self::DF_DATE_LONG:
			return Swat::_('EEEE, MMMM d, yyyy');
		case self::DF_DATE_TIME:
			return Swat::_('MMMM d, yyyy h:mm a');
		case self::DF_DATE_TIME_LONG:
			return Swat::_('EEEE, MMMM d, yyyy h:mm a');
		case self::DF_TIME:
			return Swat::_('h:mm a');
		case self::DF_DATE_SHORT:
			return Swat::_('MMM d yyyy');
		case self::DF_DATE_SHORT_NOYEAR:
			return Swat::_('MMM d');
		case self::DF_DATE_TIME_SHORT:
			return Swat::_('MMM d, yyyy h:mm a');
		case self::DF_DATE_TIME_SHORT_NOYEAR:
			return Swat::_('MMM d, h:mm a');
		case self::DF_MY:
			return Swat::_('MMMM yyyy');
		case self::DF_CC_MY:
			return Swat::_('MM / yyyy');
		case self::DF_Y:
			return Swat::_('yyyy');
		case self::DF_ISO_8601_BASIC:
			return Swat::_('yyyyMMdd\'T\'HHmmss');
		case self::DF_ISO_8601_EXTENDED:
			return Swat::_('yyyy-MM-dd\'T\'HH:mm:ss');
		case self::DF_RFC_2822:
			return Swat::_('EEE, dd MMM yyyy HH:mm:ss');
		default:
			throw new Exception("Unknown date format id '$id'.");
		}
	}

	// }}}
	// {{{ public static function getTimeZoneAbbreviations()

	/**
	 * Gets a mapping of time zone names to time zone abbreviations
	 *
	 * Note: the data generated by this method is cached in a static array. The
	 * first call will be relatively expensive but subsequent calls won't do
	 * additional calculation.
	 *
	 * @return array an array where the array key is a time zone name and the
	 *               array value is an array containing one or both of
	 *               - 'st' for the standard time abbreviation, and
	 *               - 'dt' for the daylight time abbreviation.
	 */
	public static function getTimeZoneAbbreviations()
	{
		static $shortnames = null;

		if (self::$tz_abbreviations === null) {

			self::$tz_abbreviations = array();

			$abbreviations = DateTimeZone::listAbbreviations();
			foreach ($abbreviations as $abbreviation => $time_zones) {

				if (isset(self::$valid_tz_abbreviations[$abbreviation])) {
					foreach ($time_zones as $tz) {
						$tz_id = $tz['timezone_id'];
						if (!isset(self::$tz_abbreviations[$tz_id])) {
							self::$tz_abbreviations[$tz_id] = array();
						}

						// daylight-time or standard-time
						$key = ($tz['dst']) ? 'dt' : 'st';
						if (!isset(self::$tz_abbreviations[$tz_id][$key])) {
							self::$tz_abbreviations[$tz_id][$key] =
								strtoupper($abbreviation);
						}
					}
				}

			}
		}

		return self::$tz_abbreviations;
	}

	// }}}
	// {{{ public static function getTimeZoneAbbreviation()

	/**
	 * Gets an array of time zone abbreviations for a specific time zone
	 *
	 * @param DateTimeZone $time_zone the new time zone.
	 *
	 * @return array an array containing one or both of
	 *               - 'st' for the standard time abbreviation, and
	 *               - 'dt' for the daylight time abbreviation.
	 */
	public static function getTimeZoneAbbreviation(DateTimeZone $time_zone)
	{
		$abbreviations = self::getTimeZoneAbbreviations();
		$key = $time_zone->getName();

		if (array_key_exists($key, $abbreviations)) {
			$abbreviation = $abbreviations[$key];
		}

		return $abbreviation;
	}

	// }}}
	// {{{ public static function compare()

	/**
	 * Compares two SwatDates
	 *
	 * This method is provided for backwards compatibility with PEAR::Date.
	 *
	 * @param DateTime $date1 the first date to compare.
	 * @param DateTime $date2 the second date to compare.
	 *
	 * @return integer a tri-value where -1 indicates $date1 is before $date2,
	 *                  0 indicates $date1 is equivalent to $date2 and 1
	 *                  indicates $date1 is after $date2.
	 */
	public static function compare(DateTime $date1, DateTime $date2)
	{
		// Not using getTimestamp() here because it is clamped to the 32-bit
		// signed integer range. Float compaison should be safe here as the
		// number are within the double-precision offered by floats and can
		// be represented exactly.
		$seconds1 = (float)$date1->format('U');
		$seconds2 = (float)$date2->format('U');

		if ($seconds1 > $seconds2) {
			return 1;
		}

		if ($seconds1 < $seconds2) {
			return -1;
		}

		return 0;
	}

	// }}}
	// {{{ public static function getIntervalFromSeconds()

	/**
	 * Gets a date interval with appropriate values for the specified
	 * number of seconds
	 *
	 * As this method applies on seconds, no time zone considerations
	 * are made. Years are assumed to be 365 days. Months are assumed
	 * to be 30 days.
	 *
	 * @param integer $seconds seconds for which to get interval.
	 *
	 * @return DateInterval a date interval with the relevant parts
	 *                         set.
	 */
	public static function getIntervalFromSeconds($seconds)
	{
		// don't care about micro-seconds.
		$seconds = floor(abs($seconds));

		$minute = 60;
		$hour = $minute * 60;
		$day = $hour * 24;
		$month = $day * 30;
		$year = $day * 365;

		$interval_spec = 'P';

		if ($seconds > $year) {
			$years = floor($seconds / $year);
			$seconds -= $year * $years;
			$interval_spec.= $years.'Y';
		}

		if ($seconds > $month) {
			$months = floor($seconds / $month);
			$seconds -= $month * $months;
			$interval_spec.= $months.'M';
		}

		if ($seconds > $day) {
			$days = floor($seconds / $day);
			$seconds -= $day * $days;
			$interval_spec.= $days.'D';
		}

		if ($seconds > 0 || $interval_spec === 'P') {
			$interval_spec.= 'T';

			if ($seconds > $hour) {
				$hours = floor($seconds / $hour);
				$seconds -= $hour * $hours;
				$interval_spec.= $hours.'H';
			}

			if ($seconds > $minute) {
				$minutes = floor($seconds / $minute);
				$seconds -= $minute * $minutes;
				$interval_spec.= $minutes.'M';
			}

			$interval_spec.= $seconds.'S';
		}

		return new DateInterval($interval_spec);
	}

	// }}}
	// {{{ public function getYear()

	/**
	 * Gets the year of this date
	 *
	 * This method is provided for backwards compatibility with PEAR::Date.
	 *
	 * @return integrer the year of this date.
	 */
	public function getYear()
	{
		return (integer)$this->format('Y');
	}

	// }}}
	// {{{ public function getMonth()

	/**
	 * Gets the month of this date as a number from 1-12
	 *
	 * This method is provided for backwards compatibility with PEAR::Date.
	 *
	 * @return integer the month of this date.
	 */
	public function getMonth()
	{
		return (integer)$this->format('n');
	}

	// }}}
	// {{{ public function getDay()

	/**
	 * Gets the day of this date as a number from 1-31
	 *
	 * This method is provided for backwards compatibility with PEAR::Date.
	 *
	 * @return integer the day of this date.
	 */
	public function getDay()
	{
		return (integer)$this->format('j');
	}

	// }}}
	// {{{ public function getHour()

	/**
	 * Gets the hour of this date as a number from 0-23
	 *
	 * This method is provided for backwards compatibility with PEAR::Date.
	 *
	 * @return integer the hour of this date.
	 */
	public function getHour()
	{
		return (integer)ltrim($this->format('H'), '0');
	}

	// }}}
	// {{{ public function getMinute()

	/**
	 * Gets the minute of this date as a number from 0-59
	 *
	 * This method is provided for backwards compatibility with PEAR::Date.
	 *
	 * @return integer the minute of this date.
	 */
	public function getMinute()
	{
		return (integer)ltrim($this->format('i'), '0');
	}

	// }}}
	// {{{ public function getSecond()

	/**
	 * Gets the second of this date as a number from 0-59
	 *
	 * This method is provided for backwards compatibility with PEAR::Date.
	 *
	 * @return float the second of this date.
	 */
	public function getSecond()
	{
		return (integer)ltrim($this->format('s'), '0');
	}

	// }}}
	// {{{ public function getISO8601()

	/**
	 * Gets this date formatted as an ISO 8601 timestamp
	 *
	 * Options are:
	 *
	 * - <kbd>{@link SwatDate::ISO_EXTENDED}</kbd>  - include '-' and ':'
	 *                                                separators.
	 * - <kbd>{@link SwatDate::ISO_MICROTIME}</kbd> - include microseconds.
	 * - <kbd>{@link SwatDate::ISO_TIME_ZONE}</kbd> - include time zone.
	 *
	 * @param integer $options optional. A bitwise combination of options.
	 *                          Options include the SwatDate::ISO_* constants.
	 *                          Default options are to use extended formatting
	 *                          and to include time zone offset.
	 *
	 * @return string this date formatted as an ISO 8601 timestamp.
	 */
	public function getISO8601($options = 5)
	{
		if (($options & self::ISO_EXTENDED) === self::ISO_EXTENDED) {
			$format = self::DF_ISO_8601_EXTENDED;
		} else {
			$format = self::DF_ISO_8601_BASIC;
		}

		if (($options & self::ISO_MICROTIME) === self::ISO_MICROTIME) {
			$format.= '.SSSS';
		}

		$date = $this->format($format);

		if (($options & self::ISO_TIME_ZONE) === self::ISO_TIME_ZONE) {
			$date.= $this->getFormattedOffsetById($format);
		}

		return $date;
	}

	// }}}
	// {{{ public function getRFC2822()

	/**
	 * Gets this date formatted as required by RFC 2822
	 *
	 * {@link http://tools.ietf.org/html/rfc2822#section-3.3}
	 *
	 * @return string this date formatted as an RFC 2822 timestamp.
	 */
	public function getRFC2822()
	{
		return $this->format(self::DF_RFC_2822);
	}

	// }}}
	// {{{ public function getFormattedOffsetById()

	/**
	 * Returns this date's timezone offset from GMT using a format id.
	 *
	 * @param integer $format an integer date format id.
	 *
	 * @return string the formatted timezone offset.
	 */
	public function getFormattedOffsetById($id)
	{
		switch ($id) {
		case self::DF_ISO_8601_BASIC:
		case self::DF_ISO_8601_EXTENDED:
		case self::DF_RFC_2822:
			$offset = $this->getOffset();
			$offset = floor($offset / 60); // minutes

			if ($offset == 0) {
				$offset = 'Z';
			} else {
				$offset_hours   = floor($offset / 60);
				$offset_minutes = abs($offset % 60);

				$offset = sprintf(
					'%+03.0d:%02.0d',
					$offset_hours,
					$offset_minutes
				);
			}

			return $offset;
		default:
			throw new Exception("Unknown offset format for id '$id'.");
		}
	}

	// }}}
	// {{{ public function getDaysInMonth()

	/**
	 * Gets the number of days in the current month as a number from 28-21
	 *
	 * This method is provided for backwards compatibility with PEAR::Date.
	 *
	 * @return integer the number of days in the current month of this date.
	 */
	public function getDaysInMonth()
	{
		return (integer)$this->format('t');
	}

	// }}}
	// {{{ public function getDayOfWeek()

	/**
	 * Gets the day of the current week as a number from 0 to 6
	 *
	 * Day 0 is Sunday, day 6 is Saturday. This method is provided for
	 * backwards compatibility with PEAR::Date.
	 *
	 * @return integer the day of the current week of this date.
	 */
	public function getDayOfWeek()
	{
		return (integer)$this->format('w');
	}

	// }}}
	// {{{ public function getDayOfYear()

	/**
	 * Gets the day of the year as a number from 1 to 365
	 *
	 * This method is provided for backwards compatibility with PEAR::Date.
	 *
	 * @return integer the day of the year of this date.
	 */
	public function getDayOfYear()
	{
		$day = (integer)$this->format('z');
		return $day + 1; // the "z" format starts at 0
	}

	// }}}
	// {{{ public function getNextDay()

	/**
	 * Gets a new date a day after this date
	 *
	 * This method is provided for backwards compatibility with PEAR::Date.
	 *
	 * @return a new SwatDate object on the next day of this date.
	 */
	public function getNextDay()
	{
		$date = clone $this;
		$date->addDays(1);
		return $date;
	}

	// }}}
	// {{{ public function getPrevDay()

	/**
	 * Gets a new date a day before this date
	 *
	 * This method is provided for backwards compatibility with PEAR::Date.
	 *
	 * @return a new SwatDate object on the previous day of this date.
	 */
	public function getPrevDay()
	{
		$date = clone $this;
		$date->subtractDays(1);
		return $date;
	}

	// }}}
	// {{{ public function getDate() - deprecated

	/**
	 * Gets a PEAR-conanical formatted date
	 *
	 * This method is provided for backwards compatibility with PEAR::Date.
	 *
	 * This is a valid ISO 8601 representation of this date, but omits the
	 * time zone offset. The returned string is YYYY-MM-DD HH:MM:SS.
	 *
	 * @return string a PEAR-conanical formatted version of this date.
	 *
	 * @deprecated Use {@link SwatDate::formatLikeIntl()} instead. The format
	 *             code <i>yyyy-MM-dd HH:mm:ss</i> is equivalent. Alternatively,
	 *             just cast the SwatDate object to a string.
	 */
	public function getDate()
	{
		return $this->format('Y-m-d H:i:s');
	}

	// }}}
	// {{{ public function getTime() - deprecated

	/**
	 * Gets the number of seconds since the UNIX epoch for this date
	 *
	 * This method is provided for backwards compatibility with PEAR::Date.
	 *
	 * @return integer the number of seconds since the UNIX epoch for this date.
	 *
	 * @deprecated Use {@link DateTime::getTimestamp()} instead.
	 */
	public function getTime()
	{
		return $this->getTimestamp();
	}

	// }}}
	// {{{ public function convertTZ() - deprecated

	/**
	 * Sets the time zone for this date
	 *
	 * This method is provided for backwards compatibility with PEAR::Date.
	 *
	 * @param DateTimeZone $time_zone the new time zone.
	 *
	 * @return mixed this object on success, or false if the time zone is
	 *               invalid.
	 *
	 * @deprecated Use {@link SwatDate::setTimezone()} instead.
	 */
	public function convertTZ(DateTimeZone $time_zone)
	{
		return $this->setTimezone($time_zone);
	}

	// }}}
	// {{{ public function convertTZById() - deprecated

	/**
	 * Sets the time zone for this date
	 *
	 * This method is provided for backwards compatibility with PEAR::Date.
	 *
	 * @param string $time_zone_name the name of the new time zone.
	 *
	 * @return mixed this object on success, or false if the time zone name is
	 *               invalid.
	 *
	 * @deprecated Use {@link SwatDate::setTimezone()} instead.
	 */
	public function convertTZById($time_zone_name)
	{
		return $this->setTimezone(new DateTimeZone($time_zone_name));
	}

	// }}}
	// {{{ public function setTZ()

	/**
	 * Sets the time zone for this date and updates this date's time so the
	 * hours are the same as with the old time zone
	 *
	 * @param DateTimeZone $time_zone the new time zone.
	 *
	 * @return mixed this object on success, or false if the time zone name is
	 *               invalid.
	 */
	public function setTZ(DateTimeZone $time_zone)
	{
		$this->addSeconds($this->format('Z'));
		$result = $this->setTimezone($time_zone);
		$this->subtractSeconds($this->format('Z'));
		return $result;
	}

	// }}}
	// {{{ public function setTZById()

	/**
	 * Sets the time zone for this date and updates this date's time so the
	 * hours are the same as with the old time zone
	 *
	 * @param string $time_zone_name the name of the new time zone.
	 *
	 * @return mixed this object on success, or false if the time zone name is
	 *               invalid.
	 */
	public function setTZById($time_zone_name)
	{
		$this->setTZ(new DateTimeZone($time_zone_name));
	}

	// }}}
	// {{{ public function toUTC()

	/**
	 * Sets the time zone of this date to UTC
	 *
	 * @return mixed this object on success, or false if the time zone name is
	 *               invalid.
	 */
	public function toUTC()
	{
		return $this->setTimezone(new DateTimeZone('UTC'));
	}

	// }}}
	// {{{ public function getMonthName()

	/**
	 * Gets the full name of the current month of this date
	 *
	 * The returned string is for the current locale. This method is provided
	 * for backwards compatibility with PEAR::Date.
	 *
	 * @return string the name of the current month.
	 */
	public function getMonthName()
	{
		return $this->formatLikeIntl('LLLL');
	}

	// }}}
	// {{{ public function addYears()

	/**
	 * Adds the specified number of years to this date
	 *
	 * @param integer $years the number of years to add.
	 *
	 * @return this object on success or false on failure.
	 */
	public function addYears($years)
	{
		$years = (integer)$years;
		$interval = new DateInterval('P'.abs($years).'Y');

		if ($years < 0) {
			$interval->invert = 1;
		}

		return $this->add($interval);
	}

	// }}}
	// {{{ public function subtractYears()

	/**
	 * Subtracts the specified number of years from this date
	 *
	 * @param integer $years the number of years to subtract.
	 *
	 * @return this object on success or false on failure.
	 */
	public function subtractYears($years)
	{
		$years = (integer)$years;
		$years = -$years;
		return $this->addYears($years);
	}

	// }}}
	// {{{ public function addMonths()

	/**
	 * Adds the specified number of months to this date
	 *
	 * @param integer $months the number of months to add.
	 *
	 * @return this object on success or false on failure.
	 */
	public function addMonths($months)
	{
		$months = (integer)$months;
		$interval = new DateInterval('P'.abs($months).'M');

		if ($months < 0) {
			$interval->invert = 1;
		}

		return $this->add($interval);
	}

	// }}}
	// {{{ public function subtractMonths()

	/**
	 * Subtracts the specified number of months from this date
	 *
	 * @param integer $months the number of months to subtract.
	 *
	 * @return this object on success or false on failure.
	 */
	public function subtractMonths($months)
	{
		$months = (integer)$months;
		$months = -$months;
		return $this->addMonths($months);
	}

	// }}}
	// {{{ public function addDays()

	/**
	 * Adds the specified number of days to this date
	 *
	 * @param integer $days the number of days to add.
	 *
	 * @return this object on success or false on failure.
	 */
	public function addDays($days)
	{
		$days = (integer)$days;
		$interval = new DateInterval('P'.abs($days).'D');

		if ($days < 0) {
			$interval->invert = 1;
		}

		return $this->add($interval);
	}

	// }}}
	// {{{ public function subtractDays()

	/**
	 * Subtracts the specified number of days from this date
	 *
	 * @param integer $days the number of days to subtract.
	 *
	 * @return this object on success or false on failure.
	 */
	public function subtractDays($days)
	{
		$days = (integer)$days;
		$days = -$days;
		return $this->addDays($days);
	}

	// }}}
	// {{{ public function addHours()

	/**
	 * Adds the specified number of hours to this date
	 *
	 * @param integer $hours the number of hours to add.
	 *
	 * @return this object on success or false on failure.
	 */
	public function addHours($hours)
	{
		$hours = (integer)$hours;
		$interval = new DateInterval('PT'.abs($hours).'H');

		if ($hours < 0) {
			$interval->invert = 1;
		}

		return $this->add($interval);
	}

	// }}}
	// {{{ public function subtractHours()

	/**
	 * Subtracts the specified number of hours from this date
	 *
	 * @param integer $hours the number of hours to subtract.
	 *
	 * @return this object on success or false on failure.
	 */
	public function subtractHours($hours)
	{
		$hours = (integer)$hours;
		$hours = -$hours;
		return $this->addHours($hours);
	}

	// }}}
	// {{{ public function addMinutes()

	/**
	 * Adds the specified number of minutes to this date
	 *
	 * @param integer $minutes the number of minutes to add.
	 *
	 * @return this object on success or false on failure.
	 */
	public function addMinutes($minutes)
	{
		$minutes = (integer)$minutes;
		$interval = new DateInterval('PT'.abs($minutes).'M');

		if ($minutes < 0) {
			$interval->invert = 1;
		}

		return $this->add($interval);
	}

	// }}}
	// {{{ public function subtractMinutes()

	/**
	 * Subtracts the specified number of minutes from this date
	 *
	 * @param integer $minutes the number of minutes to subtract.
	 *
	 * @return this object on success or false on failure.
	 */
	public function subtractMinutes($minutes)
	{
		$minutes = (integer)$minutes;
		$minutes = -$minutes;
		return $this->addMinutes($minutes);
	}

	// }}}
	// {{{ public function addSeconds()

	/**
	 * Adds the specified number of seconds to this date
	 *
	 * @param float $seconds the number of seconds to add.
	 *
	 * @return this object on success or false on failure.
	 */
	public function addSeconds($seconds)
	{
		$seconds = (float)$seconds;
		$interval = new DateInterval('PT'.abs($seconds).'S');

		if ($seconds < 0) {
			$interval->invert = 1;
		}

		return $this->add($interval);
	}

	// }}}
	// {{{ public function subtractSeconds()

	/**
	 * Subtracts the specified number of seconds from this date
	 *
	 * @param float $seconds the number of seconds to subtract.
	 *
	 * @return this object on success or false on failure.
	 */
	public function subtractSeconds($seconds)
	{
		$seconds = (float)$seconds;
		$seconds = -$seconds;
		return $this->addSeconds($seconds);
	}

	// }}}
	// {{{ public function setDate()

	/**
	 * Sets the date fields for this date
	 *
	 * This differs from PHP's DateTime in that it returns false if the
	 * parameters are not a valid date (i.e. February 31st).
	 *
	 * @param integer $year the year.
	 * @param integer $month the month.
	 * @param integer $day the day.
	 *
	 * @return mixed either this object on success, or false if the resulting
	 *               date is not a valid date.
	 */
	public function setDate($year, $month, $day)
	{
		if (!checkdate($month, $day, $year)) {
			return false;
		}

		return parent::setDate($year, $month, $day);
	}

	// }}}
	// {{{ public function setYear()

	/**
	 * Sets the year of this date without affecting the other date parts
	 *
	 * This method is provided for backwards compatibility with PEAR::Date. You
	 * may be able to use the method {@link DateTime::setDate()} instead.
	 *
	 * @param integer $year the new year. This should be the full four-digit
	 *                       representation of the year.
	 *
	 * @return mixed either this object on success, or false if the resulting
	 *               date is not a valid date.
	 */
	public function setYear($year)
	{
		return $this->setDate(
			$year,
			$this->getMonth(),
			$this->getDay()
		);
	}

	// }}}
	// {{{ public function setMonth()

	/**
	 * Sets the month of this date without affecting the other date parts
	 *
	 * This method is provided for backwards compatibility with PEAR::Date. You
	 * may be able to use the method {@link DateTime::setDate()} instead.
	 *
	 * @param integer $month the new month. This must be a value between
	 *                        1 and 12.
	 *
	 * @return mixed either this object on success, or false if the resulting
	 *               date is not a valid date.
	 */
	public function setMonth($month)
	{
		return $this->setDate(
			$this->getYear(),
			$month,
			$this->getDay()
		);
	}

	// }}}
	// {{{ public function setDay()

	/**
	 * Sets the day of this date without affecting the other date parts
	 *
	 * This method is provided for backwards compatibility with PEAR::Date. You
	 * may be able to use the method {@link DateTime::setDate()} instead.
	 *
	 * @param integer $day the new day. This must be a value between 1 and 31.
	 *
	 * @return mixed either this object on success, or false if the resulting
	 *               date is not a valid date.
	 */
	public function setDay($day)
	{
		return $this->setDate(
			$this->getYear(),
			$this->getMonth(),
			$day
		);
	}

	// }}}
	// {{{ public function setHour()

	/**
	 * Sets the hour of this date without affecting the other date parts
	 *
	 * This method is provided for backwards compatibility with PEAR::Date. You
	 * may be able to use the method {@link DateTime::setTime()} instead.
	 *
	 * @param integer $hour the new hour. This must be a value between 0 and 23.
	 *
	 * @return mixed either this object on success, or false if the resulting
	 *               date is not a valid date.
	 */
	public function setHour($hour)
	{
		return $this->setTime(
			$hour,
			$this->getMinute(),
			$this->getSecond()
		);
	}

	// }}}
	// {{{ public function setMinute()

	/**
	 * Sets the minute of this date without affecting the other date parts
	 *
	 * This method is provided for backwards compatibility with PEAR::Date. You
	 * may be able to use the method {@link DateTime::setTime()} instead.
	 *
	 * @param integer $minute the new minute. This must be a value between
	 *                         0 and 59.
	 *
	 * @return mixed either this object on success, or false if the resulting
	 *               date is not a valid date.
	 */
	public function setMinute($minute)
	{
		return $this->setTime(
			$this->getHour(),
			$minute,
			$this->getSecond()
		);
	}

	// }}}
	// {{{ public function setSecond()

	/**
	 * Sets the second of this date without affecting the other date parts
	 *
	 * This method is provided for backwards compatibility with PEAR::Date. You
	 * may be able to use the method {@link DateTime::setTime()} instead.
	 *
	 * @param float $second the new second. This must be a value between
	 *                      0 and 59. Microseconds are accepted.
	 *
	 * @return mixed either this object on success, or false if the resulting
	 *               date is not a valid date.
	 */
	public function setSecond($second)
	{
		return $this->setTime(
			$this->getHour(),
			$this->getMinute(),
			$second
		);
	}

	// }}}
	// {{{ public function before()

	/**
	 * Gets whether or not this date is before the specified date
	 *
	 * This method is provided for backwards compatibility with PEAR::Date.
	 *
	 * @param DateTime $when the date to check.
	 *
	 * @return boolean true if this date is before the specified date, otherwise
	 *                 false.
	 */
	public function before(DateTime $when)
	{
		return (self::compare($this, $when) == -1);
	}

	// }}}
	// {{{ public function after()

	/**
	 * Gets whether or not this date is after the specified date
	 *
	 * This method is provided for backwards compatibility with PEAR::Date.
	 *
	 * @param DateTime $when the date to check.
	 *
	 * @return boolean true if this date is after the specified date, otherwise
	 *                 false.
	 */
	public function after(DateTime $when)
	{
		return (self::compare($this, $when) == 1);
	}

	// }}}
	// {{{ public function equals()

	/**
	 * Gets whether or not this date is equivalent to the specified date
	 *
	 * This method is provided for backwards compatibility with PEAR::Date.
	 *
	 * @param DateTime $when the date to check.
	 *
	 * @return boolean true if this date is equivalent to the specified date,
	 *                 otherwise false.
	 */
	public function equals(DateTime $when)
	{
		return (self::compare($this, $when) == 0);
	}

	// }}}
	// {{{ public function addStrictMonths()

	/**
	 * Adds months to this date without affecting the day of the month
	 *
	 * This differs from {@link SwatDate::addMonths()} in how dates at the end
	 * of a month are handled. In SwatDate::addMonths(), if one month is added
	 * to January 31, the resulting date will be March 2 or 3 depending on
	 * if it is a leap year.
	 *
	 * In this method, if one month is added to January 31, an exception is
	 * thrown.
	 *
	 * @param integer $months the number of months to add.
	 *
	 * @return SwatDate this object.
	 *
	 * @throws Exception if the resulting date is invalid (i.e. February 30) an
	 *                   exception is thrown.
	 */
	public function addStrictMonths($months)
	{
		$months = (integer)$months;

		$years  = (integer)($months / 12);
		$months = $months % 12;

		$year  = $this->getYear()  + $years;
		$month = $this->getMonth() + $months;

		if ($month < 1) {
			$year  -= 1;
			$month += 12;
		} elseif ($month > 12) {
			$year  += 1;
			$month -= 12;
		}

		$success = $this->setDate(
			$year,
			$month,
			$this->getDay()
		);

		if (!$success) {
			throw new Exception(
				sprintf(
					'Can not add %d whole months to %s.',
					$months,
					$this->format('c')
				)
			);
		}

		return $this;
	}

	// }}}
	// {{{ public function subtractStrictMonths()

	/**
	 * Subtracts months to this date without affecting the day of the month
	 *
	 * This differs from {@link SwatDate::subtractMonths()} in how dates at the
	 * end of a month are handled. In SwatDate::subtractMonths(), if one month
	 * is subtracted from March 30, the resulting date will be March 1 or 2
	 * depending on if it is a leap year.
	 *
	 * In this method, if one month is subtracted from March 30, an exception
	 * is thrown.
	 *
	 * @param integer $months the number of months to subtract.
	 *
	 * @return SwatDate this object.
	 *
	 * @throws Exception if the resulting date is invalid (i.e. February 30) an
	 *                   exception is thrown.
	 */
	public function subtractStrictMonths($months)
	{
		return $this->addStrictMonths(-$months);
	}

	// }}}
	// {{{ public function serialize()

	/**
	 * Serializes this date
	 *
	 * Serialization is provided for backwards compatibility with the
	 * transitional and now depreciated HotDate package. The SwatDate serialize
	 * format is not compatible with PHP 5.3+ native DateTime serialization.
	 */
	public function serialize()
	{
		$data = array(
			$this->getTimestamp(),
			$this->getTimeZone()->getName()
		);

		return serialize($data);
	}

	// }}}
	// {{{ public function unserialize()

	/**
	 * Unserializes this date
	 *
	 * @param string $serialized the serialized date data.
	 */
	public function unserialize($serialized)
	{
		$data = unserialize($serialized);

		// Calling __construct here is required to avoid PHP warnings. See
		// PHP bug #65151. DateTime objects that are created through
		// unserialization are not properly initialized until __construct() is
		// called.
		$this->__construct('@'.$data[0]);

		// DateTime constructor with timestamp is always UTC so set time zone
		$this->setTimezone(new DateTimeZone($data[1]));
	}

	// }}}
}

?>
