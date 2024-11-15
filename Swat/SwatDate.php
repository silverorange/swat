<?php

/**
 * Date class and PEAR-compatibility layer.
 *
 * Notable unsupported features:
 * - leap-seconds
 * - microseconds
 * - localization
 *
 * @copyright 2005-2024 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDate extends DateTime implements Stringable
{
    /**
     * America/Halifax.
     */
    public const TZ_ID = 1;

    /**
     * AST.
     */
    public const TZ_SHORT = 2;

    /**
     * Alias for {@link SwatDate::TZ_SHORT}.
     *
     * @deprecated
     */
    public const TZ_LONG = 3;

    /**
     * ADT.
     */
    public const TZ_DST_SHORT = 4;

    /**
     * Alias for {@link SwatDate::TZ_DST_SHORT}.
     *
     * @deprecated
     */
    public const TZ_DST_LONG = 5;

    /**
     * AST/ADT.
     */
    public const TZ_COMBINED = 6;

    /**
     * AST or ADT, depending on whether or not the date is in daylight time.
     */
    public const TZ_CURRENT_SHORT = 7;

    /**
     * Alias for {@link SwatDate::TZ_CURRENT_SHORT}.
     *
     * @deprecated
     */
    public const TZ_CURRENT_LONG = 8;

    /**
     * 07/02/02.
     */
    public const DF_MDY = 1;

    /**
     * 070202.
     */
    public const DF_MDY_SHORT = 2;

    /**
     * July 2, 2002.
     */
    public const DF_DATE = 3;

    /**
     * Tuesday, July 2, 2002.
     */
    public const DF_DATE_LONG = 4;

    /**
     * July 2, 2002 10:09 am.
     */
    public const DF_DATE_TIME = 5;

    /**
     * Tuesday, July 2, 2002 10:09 am.
     */
    public const DF_DATE_TIME_LONG = 6;

    /**
     * 10:09 am.
     */
    public const DF_TIME = 7;

    /**
     * Aug 5, 2002.
     */
    public const DF_DATE_SHORT = 8;

    /**
     * Aug 5.
     */
    public const DF_DATE_SHORT_NOYEAR = 9;

    /**
     * Aug 5, 2002 10:09 am.
     */
    public const DF_DATE_TIME_SHORT = 10;

    /**
     * Aug 5, 10:09 am.
     */
    public const DF_DATE_TIME_SHORT_NOYEAR = 11;

    /**
     * August 2002.
     */
    public const DF_MY = 12;

    /**
     * 08 / 2002.
     */
    public const DF_CC_MY = 13;

    /**
     * 2002.
     */
    public const DF_Y = 14;

    /**
     * 20020822T180526Z.
     */
    public const DF_ISO_8601_BASIC = 15;

    /**
     * 2002-08-22T18:05:26Z.
     */
    public const DF_ISO_8601_EXTENDED = 16;

    /**
     * Thu, 22 Aug 2002 18:05:26 Z.
     */
    public const DF_RFC_2822 = 17;

    /**
     * Value to use for no options.
     *
     * @see SwatDate::getISO8601()
     */
    public const ISO_BASIC = 0;

    /**
     * Include '-' and ':' separator characters.
     *
     * @see SwatDate::getISO8601()
     */
    public const ISO_EXTENDED = 1;

    /**
     * Include microseconds.
     *
     * @see SwatDate::getISO8601()
     */
    public const ISO_MICROTIME = 2;

    /**
     * Include time zone offset.
     *
     * Time zone offset will be 'Z' for 0, otherwise
     * will be +-HH:MM.
     *
     * @see SwatDate::getISO8601()
     */
    public const ISO_TIME_ZONE = 4;

    /**
     * A set of bitwise contants to control which parts of the interval we want
     * when returning a DateInterval.
     *
     * @see SwatString::getHumanReadableTimePeriodParts()
     */
    public const DI_YEARS = 1;
    public const DI_MONTHS = 2;
    public const DI_WEEKS = 4;
    public const DI_DAYS = 8;
    public const DI_HOURS = 16;
    public const DI_MINUTES = 32;
    public const DI_SECONDS = 64;

    protected static $tz_abbreviations;
    protected static $valid_tz_abbreviations = [
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
        'cvt'   => true,
        'cxt'   => true,
        'chst'  => true,
        'dst'   => true,
        'dft'   => true,
        'east'  => true,
        'eat'   => true,
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
        'ret'   => true,
        'samt'  => true,
        'sast'  => true,
        'sbt'   => true,
        'sct'   => true,
        'slt'   => true,
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
    ];

    /**
     * Formats this date given either a format string or a format id.
     *
     * Note: The results of this method are not localized. For a localized
     * formatted date, use {@link SwatDate::formatLikeIntl()}.
     *
     * @param int|string $format    either a format string or an integer format
     *                              id
     * @param int        $tz_format optional time zone format id
     *
     * @return string the formatted date
     */
    public function format($format, $tz_format = null): string
    {
        if (is_int($format)) {
            $format = self::getFormatById($format);
        }

        $out = parent::format($format);

        if ($tz_format !== null) {
            $out .= ' ' . $this->formatTZ($tz_format);
        }

        return $out;
    }

    /**
     * Formats this date using the ICU IntlDateFormater given either a format
     * string or a format id.
     *
     * This method returns localized results.
     *
     * @param mixed  $format    either a format string or an integer format id
     * @param int    $tz_format optional. A time zone format id.
     * @param string $locale    optional. The locale to use to format the date.
     *                          If not specified, the current locale is used.
     *
     * @return string the formatted date according to the current locale
     */
    public function formatLikeIntl(
        $format,
        $tz_format = null,
        $locale = null,
    ): string {
        if (is_int($format)) {
            $format = self::getFormatLikeIntlById($format);
        }

        static $formatters = [];

        if (!isset($formatters[$locale])) {
            $formatters[$locale] = new IntlDateFormatter(
                $locale,
                IntlDateFormatter::FULL,
                IntlDateFormatter::FULL,
            );
        }

        $formatter = $formatters[$locale];
        $formatter->setTimeZone($this->getTimezone()->getName());
        $formatter->setPattern($format);

        $timestamp = $this->getTimestamp();
        $out = $formatter->format($timestamp);

        if ($tz_format !== null) {
            $out .= ' ' . $this->formatTZ($tz_format);
        }

        return $out;
    }

    /**
     * Formats the time zone part of this date.
     *
     * @param int $format an integer time zone format id
     *
     * @return string the formatted time zone
     */
    public function formatTZ($format): string
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
                if (
                    isset($abbreviations[$id], $abbreviations[$id]['st'])
                ) {
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
                $out = [];
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

    /**
     * Clears the time portion of the date object.
     *
     * @deprecated use <kbd>SwatDate::setTime(0, 0, 0);</kbd> instead
     */
    public function clearTime(): void
    {
        $this->setTime(0, 0, 0);
    }

    public function __toString(): string
    {
        return $this->format('Y-m-d\TH:i:s');
    }

    /**
     * Get a human-readable string representing the difference between
     * two dates.
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
     * @return string a human-readable date diff
     */
    public function getHumanReadableDateDiff(
        ?SwatDate $compare_date = null,
    ): string {
        if ($compare_date === null) {
            $compare_date = new SwatDate();
        }

        $seconds = $compare_date->getTime() - $this->getTime();

        return SwatString::toHumanReadableTimePeriod($seconds, true);
    }

    /**
     * Get a human-readable string representing the difference between
     * two dates.
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
     * @return string a human-readable date diff
     */
    public function getHumanReadableDateDiffWithWeeks(
        ?SwatDate $compare_date = null,
    ): string {
        if ($compare_date === null) {
            $compare_date = new SwatDate();
        }

        $seconds = $compare_date->getTime() - $this->getTime();

        return SwatString::toHumanReadableTimePeriodWithWeeks($seconds, true);
    }

    /**
     * Get a human-readable string representing the difference between
     * two dates.
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
     * @return string a human-readable date diff
     */
    public function getHumanReadableDateDiffWithWeeksAndDays(
        ?SwatDate $compare_date = null,
    ): string {
        if ($compare_date === null) {
            $compare_date = new SwatDate();
        }

        $seconds = $compare_date->getTime() - $this->getTime();

        return SwatString::toHumanReadableTimePeriodWithWeeksAndDays($seconds);
    }

    /**
     * Gets a date format string by id.
     *
     * @param int $id the id of the format string to retrieve
     *
     * @return string the formatting string that was requested
     *
     * @throws SwatException
     */
    public static function getFormatById($id): string
    {
        // Note: The format() method does not localize results, so these
        // format codes are _not_ wrapped in gettext calls.

        return match ($id) {
            self::DF_MDY                    => 'm/d/y',
            self::DF_MDY_SHORT              => 'mdy',
            self::DF_DATE                   => 'F j, Y',
            self::DF_DATE_LONG              => 'l, F j, Y',
            self::DF_DATE_TIME              => 'F j, Y g:i a',
            self::DF_DATE_TIME_LONG         => 'l, F j, Y g:i a',
            self::DF_TIME                   => 'g:i a',
            self::DF_DATE_SHORT             => 'M j, Y',
            self::DF_DATE_SHORT_NOYEAR      => 'M j',
            self::DF_DATE_TIME_SHORT        => 'M j, Y g:i a',
            self::DF_DATE_TIME_SHORT_NOYEAR => 'M j, g:i a',
            self::DF_MY                     => 'F Y',
            self::DF_CC_MY                  => 'm / Y',
            self::DF_Y                      => 'Y',
            self::DF_ISO_8601_BASIC         => 'Ymd\THis',
            self::DF_ISO_8601_EXTENDED      => 'Y-m-d\TH:i:s',
            self::DF_RFC_2822               => 'r',
            default                         => throw new Exception("Unknown date format id '{$id}'."),
        };
    }

    /**
     * Gets a IntlDateFormatter date format string by id.
     *
     * @param int $id the id of the format string to retrieve
     *
     * @return string the formatting string that was requested
     *
     * @throws SwatException
     */
    public static function getFormatLikeIntlById($id): string
    {
        return match ($id) {
            self::DF_MDY                    => Swat::_('MM/dd/yy'),
            self::DF_MDY_SHORT              => Swat::_('MMddyy'),
            self::DF_DATE                   => Swat::_('MMMM d, yyyy'),
            self::DF_DATE_LONG              => Swat::_('EEEE, MMMM d, yyyy'),
            self::DF_DATE_TIME              => Swat::_('MMMM d, yyyy h:mm a'),
            self::DF_DATE_TIME_LONG         => Swat::_('EEEE, MMMM d, yyyy h:mm a'),
            self::DF_TIME                   => Swat::_('h:mm a'),
            self::DF_DATE_SHORT             => Swat::_('MMM d yyyy'),
            self::DF_DATE_SHORT_NOYEAR      => Swat::_('MMM d'),
            self::DF_DATE_TIME_SHORT        => Swat::_('MMM d, yyyy h:mm a'),
            self::DF_DATE_TIME_SHORT_NOYEAR => Swat::_('MMM d, h:mm a'),
            self::DF_MY                     => Swat::_('MMMM yyyy'),
            self::DF_CC_MY                  => Swat::_('MM / yyyy'),
            self::DF_Y                      => Swat::_('yyyy'),
            self::DF_ISO_8601_BASIC         => Swat::_('yyyyMMdd\'T\'HHmmss'),
            self::DF_ISO_8601_EXTENDED      => Swat::_('yyyy-MM-dd\'T\'HH:mm:ss'),
            self::DF_RFC_2822               => Swat::_('EEE, dd MMM yyyy HH:mm:ss'),
            default                         => throw new Exception("Unknown date format id '{$id}'."),
        };
    }

    /**
     * Gets a mapping of time zone names to time zone abbreviations.
     *
     * Note: the data generated by this method is cached in a static array. The
     * first call will be relatively expensive but subsequent calls won't do
     * additional calculation.
     *
     * @return array an array where the array key is a time zone name and the
     *               array value is an array containing one or both of
     *               - 'st' for the standard time abbreviation, and
     *               - 'dt' for the daylight time abbreviation
     */
    public static function getTimeZoneAbbreviations(): array
    {
        static $shortnames = null;

        if (self::$tz_abbreviations === null) {
            self::$tz_abbreviations = [];

            $abbreviations = DateTimeZone::listAbbreviations();
            foreach ($abbreviations as $abbreviation => $time_zones) {
                if (isset(self::$valid_tz_abbreviations[$abbreviation])) {
                    foreach ($time_zones as $tz) {
                        $tz_id = $tz['timezone_id'];
                        if (!isset(self::$tz_abbreviations[$tz_id])) {
                            self::$tz_abbreviations[$tz_id] = [];
                        }

                        // daylight-time or standard-time
                        $key = $tz['dst'] ? 'dt' : 'st';
                        if (!isset(self::$tz_abbreviations[$tz_id][$key])) {
                            self::$tz_abbreviations[$tz_id][
                                $key
                            ] = mb_strtoupper($abbreviation);
                        }
                    }
                }
            }
        }

        return self::$tz_abbreviations;
    }

    /**
     * Gets an array of time zone abbreviations for a specific time zone.
     *
     * @param DateTimeZone $time_zone the new time zone
     *
     * @return array an array containing one or both of
     *               - 'st' for the standard time abbreviation, and
     *               - 'dt' for the daylight time abbreviation
     */
    public static function getTimeZoneAbbreviation(
        DateTimeZone $time_zone,
    ): array {
        $abbreviations = self::getTimeZoneAbbreviations();
        $key = $time_zone->getName();

        if (array_key_exists($key, $abbreviations)) {
            $abbreviation = $abbreviations[$key];
        }

        return $abbreviation;
    }

    /**
     * Compares two SwatDates.
     *
     * This method is provided for backwards compatibility with PEAR::Date.
     *
     * @param DateTime $date1 the first date to compare
     * @param DateTime $date2 the second date to compare
     *
     * @return int a tri-value where -1 indicates $date1 is before $date2,
     *             0 indicates $date1 is equivalent to $date2 and 1
     *             indicates $date1 is after $date2
     */
    public static function compare(DateTime $date1, DateTime $date2): int
    {
        // Not using getTimestamp() here because it is clamped to the 32-bit
        // signed integer range. Float compaison should be safe here as the
        // number are within the double-precision offered by floats and can
        // be represented exactly.
        $seconds1 = (float) $date1->format('U');
        $seconds2 = (float) $date2->format('U');

        if ($seconds1 > $seconds2) {
            return 1;
        }

        if ($seconds1 < $seconds2) {
            return -1;
        }

        return 0;
    }

    /**
     * Gets a date interval with appropriate values for the specified
     * number of seconds.
     *
     * As this method applies on seconds, no time zone considerations
     * are made. Years are assumed to be 365 days. Months are assumed
     * to be 30 days.
     *
     * @param int $seconds seconds for which to get interval
     *
     * @return DateInterval a date interval with the relevant parts
     *                      set
     */
    public static function getIntervalFromSeconds($seconds): DateInterval
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
            $interval_spec .= $years . 'Y';
        }

        if ($seconds > $month) {
            $months = floor($seconds / $month);
            $seconds -= $month * $months;
            $interval_spec .= $months . 'M';
        }

        if ($seconds > $day) {
            $days = floor($seconds / $day);
            $seconds -= $day * $days;
            $interval_spec .= $days . 'D';
        }

        if ($seconds > 0 || $interval_spec === 'P') {
            $interval_spec .= 'T';

            if ($seconds > $hour) {
                $hours = floor($seconds / $hour);
                $seconds -= $hour * $hours;
                $interval_spec .= $hours . 'H';
            }

            if ($seconds > $minute) {
                $minutes = floor($seconds / $minute);
                $seconds -= $minute * $minutes;
                $interval_spec .= $minutes . 'M';
            }

            $interval_spec .= $seconds . 'S';
        }

        return new DateInterval($interval_spec);
    }

    /**
     * Gets the year of this date.
     *
     * This method is provided for backwards compatibility with PEAR::Date.
     *
     * @return int the year of this date
     */
    public function getYear(): int
    {
        return (int) $this->format('Y');
    }

    /**
     * Gets the month of this date as a number from 1-12.
     *
     * This method is provided for backwards compatibility with PEAR::Date.
     *
     * @return int the month of this date
     */
    public function getMonth(): int
    {
        return (int) $this->format('n');
    }

    /**
     * Gets the day of this date as a number from 1-31.
     *
     * This method is provided for backwards compatibility with PEAR::Date.
     *
     * @return int the day of this date
     */
    public function getDay(): int
    {
        return (int) $this->format('j');
    }

    /**
     * Gets the hour of this date as a number from 0-23.
     *
     * This method is provided for backwards compatibility with PEAR::Date.
     *
     * @return int the hour of this date
     */
    public function getHour(): int
    {
        return (int) ltrim($this->format('H'), '0');
    }

    /**
     * Gets the minute of this date as a number from 0-59.
     *
     * This method is provided for backwards compatibility with PEAR::Date.
     *
     * @return int the minute of this date
     */
    public function getMinute(): int
    {
        return (int) ltrim($this->format('i'), '0');
    }

    /**
     * Gets the second of this date as a number from 0-59.
     *
     * This method is provided for backwards compatibility with PEAR::Date.
     *
     * @return float the second of this date
     */
    public function getSecond()
    {
        return (int) ltrim($this->format('s'), '0');
    }

    /**
     * Gets this date formatted as an ISO 8601 timestamp.
     *
     * Options are:
     *
     * - <kbd>{@link SwatDate::ISO_EXTENDED}</kbd>  - include '-' and ':'
     *                                                separators.
     * - <kbd>{@link SwatDate::ISO_MICROTIME}</kbd> - include microseconds.
     * - <kbd>{@link SwatDate::ISO_TIME_ZONE}</kbd> - include time zone.
     *
     * @param int $options optional. A bitwise combination of options.
     *                     Options include the SwatDate::ISO_* constants.
     *                     Default options are to use extended formatting
     *                     and to include time zone offset.
     *
     * @return string this date formatted as an ISO 8601 timestamp
     */
    public function getISO8601(
        $options = self::ISO_EXTENDED | self::ISO_TIME_ZONE,
    ): string {
        if (($options & self::ISO_EXTENDED) === self::ISO_EXTENDED) {
            $format = self::DF_ISO_8601_EXTENDED;
        } else {
            $format = self::DF_ISO_8601_BASIC;
        }

        if (($options & self::ISO_MICROTIME) === self::ISO_MICROTIME) {
            $format = self::getFormatById($format) . '.u';
        }

        $date = $this->format($format);

        if (($options & self::ISO_TIME_ZONE) === self::ISO_TIME_ZONE) {
            $date .= $this->getFormattedOffsetById($format);
        }

        return $date;
    }

    /**
     * Gets this date formatted as required by RFC 2822.
     *
     * {@link http://tools.ietf.org/html/rfc2822#section-3.3}
     *
     * @return string this date formatted as an RFC 2822 timestamp
     */
    public function getRFC2822(): string
    {
        return $this->format(self::DF_RFC_2822);
    }

    /**
     * Returns this date's timezone offset from GMT using a format id.
     *
     * @param mixed $id
     *
     * @return string the formatted timezone offset
     */
    public function getFormattedOffsetById($id): string
    {
        switch ($id) {
            case self::DF_ISO_8601_BASIC:
            case self::DF_ISO_8601_EXTENDED:
            case self::DF_RFC_2822:
                $offset = $this->getOffset();
                $offset = intval(floor($offset / 60)); // minutes

                if ($offset === 0) {
                    $offset = 'Z';
                } else {
                    $offset_hours = floor($offset / 60);
                    $offset_minutes = abs($offset % 60);

                    $offset = sprintf(
                        '%+03.0d:%02.0d',
                        $offset_hours,
                        $offset_minutes,
                    );
                }

                return $offset;

            default:
                throw new Exception("Unknown offset format for id '{$id}'.");
        }
    }

    /**
     * Gets the number of days in the current month as a number from 28-21.
     *
     * This method is provided for backwards compatibility with PEAR::Date.
     *
     * @return int the number of days in the current month of this date
     */
    public function getDaysInMonth(): int
    {
        return (int) $this->format('t');
    }

    /**
     * Gets the day of the current week as a number from 0 to 6.
     *
     * Day 0 is Sunday, day 6 is Saturday. This method is provided for
     * backwards compatibility with PEAR::Date.
     *
     * @return int the day of the current week of this date
     */
    public function getDayOfWeek(): int
    {
        return (int) $this->format('w');
    }

    /**
     * Gets the day of the year as a number from 1 to 365.
     *
     * This method is provided for backwards compatibility with PEAR::Date.
     *
     * @return int the day of the year of this date
     */
    public function getDayOfYear(): int
    {
        $day = (int) $this->format('z');

        return $day + 1; // the "z" format starts at 0
    }

    /**
     * Gets a new date a day after this date.
     *
     * This method is provided for backwards compatibility with PEAR::Date.
     *
     * @return DateTime a new SwatDate object on the next day of this date
     */
    public function getNextDay(): DateTime
    {
        $date = clone $this;
        $date->addDays(1);

        return $date;
    }

    /**
     * Gets a new date a day before this date.
     *
     * This method is provided for backwards compatibility with PEAR::Date.
     *
     * @return DateTime a new SwatDate object on the previous day of this date
     */
    public function getPrevDay(): DateTime
    {
        $date = clone $this;
        $date->subtractDays(1);

        return $date;
    }

    /**
     * Gets a PEAR-canonical formatted date.
     *
     * This method is provided for backwards compatibility with PEAR::Date.
     *
     * This is a valid ISO 8601 representation of this date, but omits the
     * time zone offset. The returned string is YYYY-MM-DD HH:MM:SS.
     *
     * @return string a PEAR-canonical formatted version of this date
     *
     * @deprecated Use {@link SwatDate::getISO8601()} instead. The format
     *             code <i>yyyy-MM-ddTHH:mm:ss</i> is equivalent for most uses.
     *             Alternatively, just cast the SwatDate object to a string.
     */
    public function getDate(): string
    {
        return $this->format('Y-m-d H:i:s');
    }

    /**
     * Gets the number of seconds since the UNIX epoch for this date.
     *
     * This method is provided for backwards compatibility with PEAR::Date.
     *
     * @return int the number of seconds since the UNIX epoch for this date
     *
     * @deprecated use {@link DateTime::getTimestamp()} instead
     */
    public function getTime(): int
    {
        return $this->getTimestamp();
    }

    /**
     * Sets the time zone for this date.
     *
     * This method is provided for backwards compatibility with PEAR::Date.
     *
     * @param DateTimeZone $time_zone the new time zone
     *
     * @return DateTime this date object
     *
     * @deprecated use {@link SwatDate::setTimezone()} instead
     */
    public function convertTZ(DateTimeZone $time_zone): DateTime
    {
        return $this->setTimezone($time_zone);
    }

    /**
     * Sets the time zone for this date.
     *
     * This method is provided for backwards compatibility with PEAR::Date.
     *
     * @param string $time_zone_name the name of the new time zone
     *
     * @return DateTime this date object
     *
     * @deprecated use {@link SwatDate::setTimezone()} instead
     */
    public function convertTZById($time_zone_name): DateTime
    {
        return $this->setTimezone(new DateTimeZone($time_zone_name));
    }

    /**
     * Sets the time zone for this date and updates this date's time so the
     * hours are the same as with the old time zone.
     *
     * @param DateTimeZone $time_zone the new time zone
     *
     * @return DateTime this date object
     */
    public function setTZ(DateTimeZone $time_zone): DateTime
    {
        return $this->addSeconds($this->format('Z'))
            ->setTimezone($time_zone)
            ->subtractSeconds($this->format('Z'));
    }

    /**
     * Sets the time zone for this date and updates this date's time so the
     * hours are the same as with the old time zone.
     *
     * @param string $time_zone_name the name of the new time zone
     *
     * @return DateTime this date object
     */
    public function setTZById($time_zone_name)
    {
        return $this->setTZ(new DateTimeZone($time_zone_name));
    }

    /**
     * Sets the time zone of this date to UTC.
     *
     * @return DateTime this date object
     */
    public function toUTC()
    {
        return $this->setTimezone(new DateTimeZone('UTC'));
    }

    /**
     * Gets the full name of the current month of this date.
     *
     * The returned string is for the current locale. This method is provided
     * for backwards compatibility with PEAR::Date.
     *
     * @return string the name of the current month
     */
    public function getMonthName(): string
    {
        return $this->formatLikeIntl('LLLL');
    }

    /**
     * Adds the specified number of years to this date.
     *
     * @param int $years the number of years to add
     *
     * @return DateTime this date object
     */
    public function addYears($years): DateTime
    {
        $years = (int) $years;
        $interval = new DateInterval('P' . abs($years) . 'Y');

        if ($years < 0) {
            $interval->invert = 1;
        }

        return $this->add($interval);
    }

    /**
     * Subtracts the specified number of years from this date.
     *
     * @param int $years the number of years to subtract
     *
     * @return DateTime this date object
     */
    public function subtractYears($years): DateTime
    {
        $years = (int) $years;
        $years = -$years;

        return $this->addYears($years);
    }

    /**
     * Adds the specified number of months to this date.
     *
     * @param int $months the number of months to add
     *
     * @return DateTime this date object
     */
    public function addMonths($months): DateTime
    {
        $months = (int) $months;
        $interval = new DateInterval('P' . abs($months) . 'M');

        if ($months < 0) {
            $interval->invert = 1;
        }

        return $this->add($interval);
    }

    /**
     * Subtracts the specified number of months from this date.
     *
     * @param int $months the number of months to subtract
     *
     * @return DateTime this date object
     */
    public function subtractMonths($months): DateTime
    {
        $months = (int) $months;
        $months = -$months;

        return $this->addMonths($months);
    }

    /**
     * Adds the specified number of days to this date.
     *
     * @param int $days the number of days to add
     *
     * @return DateTime this date object
     */
    public function addDays($days): DateTime
    {
        $days = (int) $days;
        $interval = new DateInterval('P' . abs($days) . 'D');

        if ($days < 0) {
            $interval->invert = 1;
        }

        return $this->add($interval);
    }

    /**
     * Subtracts the specified number of days from this date.
     *
     * @param int $days the number of days to subtract
     *
     * @return DateTime this date object
     */
    public function subtractDays($days): DateTime
    {
        $days = (int) $days;
        $days = -$days;

        return $this->addDays($days);
    }

    /**
     * Adds the specified number of hours to this date.
     *
     * @param int $hours the number of hours to add
     *
     * @return DateTime this date object
     */
    public function addHours($hours): DateTime
    {
        $hours = (int) $hours;
        $interval = new DateInterval('PT' . abs($hours) . 'H');

        if ($hours < 0) {
            $interval->invert = 1;
        }

        return $this->add($interval);
    }

    /**
     * Subtracts the specified number of hours from this date.
     *
     * @param int $hours the number of hours to subtract
     *
     * @return DateTime this date object
     */
    public function subtractHours($hours): DateTime
    {
        $hours = (int) $hours;
        $hours = -$hours;

        return $this->addHours($hours);
    }

    /**
     * Adds the specified number of minutes to this date.
     *
     * @param int $minutes the number of minutes to add
     *
     * @return DateTime this date object
     */
    public function addMinutes($minutes): DateTime
    {
        $minutes = (int) $minutes;
        $interval = new DateInterval('PT' . abs($minutes) . 'M');

        if ($minutes < 0) {
            $interval->invert = 1;
        }

        return $this->add($interval);
    }

    /**
     * Subtracts the specified number of minutes from this date.
     *
     * @param int $minutes the number of minutes to subtract
     *
     * @return DateTime this date object
     */
    public function subtractMinutes($minutes): DateTime
    {
        $minutes = (int) $minutes;
        $minutes = -$minutes;

        return $this->addMinutes($minutes);
    }

    /**
     * Adds the specified number of seconds to this date.
     *
     * @param float $seconds the number of seconds to add
     *
     * @return DateTime this date object
     */
    public function addSeconds($seconds): DateTime
    {
        $seconds = (float) $seconds;
        $interval = new DateInterval('PT' . abs($seconds) . 'S');

        if ($seconds < 0) {
            $interval->invert = 1;
        }

        return $this->add($interval);
    }

    /**
     * Subtracts the specified number of seconds from this date.
     *
     * @param float $seconds the number of seconds to subtract
     *
     * @return DateTime this date object
     */
    public function subtractSeconds($seconds): DateTime
    {
        $seconds = (float) $seconds;
        $seconds = -$seconds;

        return $this->addSeconds($seconds);
    }

    /**
     * Sets the year of this date without affecting the other date parts.
     *
     * This method is provided for backwards compatibility with PEAR::Date. You
     * may be able to use the method {@link DateTime::setDate()} instead.
     *
     * @param int $year the new year. This should be the full four-digit
     *                  representation of the year.
     *
     * @return DateTime|false either this object on success, or false if the
     *                        resulting date is not a valid date
     */
    public function setYear($year): DateTime|false
    {
        return $this->setCheckedDate($year, $this->getMonth(), $this->getDay());
    }

    /**
     * Sets the month of this date without affecting the other date parts.
     *
     * This method is provided for backwards compatibility with PEAR::Date. You
     * may be able to use the method {@link DateTime::setDate()} instead.
     *
     * @param int $month the new month. This must be a value between
     *                   1 and 12.
     *
     * @return DateTime|false either this object on success, or false if the
     *                        resulting date is not a valid date
     */
    public function setMonth($month): DateTime|false
    {
        return $this->setCheckedDate($this->getYear(), $month, $this->getDay());
    }

    /**
     * Sets the day of this date without affecting the other date parts.
     *
     * This method is provided for backwards compatibility with PEAR::Date. You
     * may be able to use the method {@link DateTime::setDate()} instead.
     *
     * @param int $day the new day. This must be a value between 1 and 31.
     *
     * @return DateTime|false either this object on success, or false if the
     *                        resulting date is not a valid date
     */
    public function setDay($day): DateTime|false
    {
        return $this->setCheckedDate($this->getYear(), $this->getMonth(), $day);
    }

    /**
     * Sets the hour of this date without affecting the other date parts.
     *
     * This method is provided for backwards compatibility with PEAR::Date. You
     * may be able to use the method {@link DateTime::setTime()} instead.
     *
     * @param int $hour the new hour. This must be a value between 0 and 23.
     *
     * @return DateTime this date object
     */
    public function setHour($hour): DateTime
    {
        return $this->setTime($hour, $this->getMinute(), $this->getSecond());
    }

    /**
     * Sets the minute of this date without affecting the other date parts.
     *
     * This method is provided for backwards compatibility with PEAR::Date. You
     * may be able to use the method {@link DateTime::setTime()} instead.
     *
     * @param int $minute the new minute. This must be a value between
     *                    0 and 59.
     *
     * @return DateTime this date object
     */
    public function setMinute($minute): DateTime
    {
        return $this->setTime($this->getHour(), $minute, $this->getSecond());
    }

    /**
     * Sets the second of this date without affecting the other date parts.
     *
     * This method is provided for backwards compatibility with PEAR::Date. You
     * may be able to use the method {@link DateTime::setTime()} instead.
     *
     * @param float $second the new second. This must be a value between
     *                      0 and 59. Microseconds are accepted.
     *
     * @return DateTime this date object
     */
    public function setSecond($second): DateTime
    {
        return $this->setTime($this->getHour(), $this->getMinute(), $second);
    }

    /**
     * Gets whether or not this date is before the specified date.
     *
     * This method is provided for backwards compatibility with PEAR::Date.
     *
     * @param DateTime $when the date to check
     *
     * @return bool true if this date is before the specified date, otherwise
     *              false
     */
    public function before(DateTime $when)
    {
        return self::compare($this, $when) === -1;
    }

    /**
     * Gets whether or not this date is after the specified date.
     *
     * This method is provided for backwards compatibility with PEAR::Date.
     *
     * @param DateTime $when the date to check
     *
     * @return bool true if this date is after the specified date, otherwise
     *              false
     */
    public function after(DateTime $when)
    {
        return self::compare($this, $when) === 1;
    }

    /**
     * Gets whether or not this date is equivalent to the specified date.
     *
     * This method is provided for backwards compatibility with PEAR::Date.
     *
     * @param DateTime $when the date to check
     *
     * @return bool true if this date is equivalent to the specified date,
     *              otherwise false
     */
    public function equals(DateTime $when)
    {
        return self::compare($this, $when) === 0;
    }

    /**
     * Adds months to this date without affecting the day of the month.
     *
     * This differs from {@link SwatDate::addMonths()} in how dates at the end
     * of a month are handled. In SwatDate::addMonths(), if one month is added
     * to January 31, the resulting date will be March 2 or 3 depending on
     * if it is a leap year.
     *
     * In this method, if one month is added to January 31, an exception is
     * thrown.
     *
     * @param int $months the number of months to add
     *
     * @return SwatDate this object
     *
     * @throws Exception if the resulting date is invalid (i.e. February 30) an
     *                   exception is thrown.
     */
    public function addStrictMonths($months)
    {
        $months = (int) $months;

        $years = (int) ($months / 12);
        $months = $months % 12;

        $year = $this->getYear() + $years;
        $month = $this->getMonth() + $months;

        if ($month < 1) {
            --$year;
            $month += 12;
        } elseif ($month > 12) {
            ++$year;
            $month -= 12;
        }

        $success = $this->setCheckedDate($year, $month, $this->getDay());

        if (!$success) {
            throw new Exception(
                sprintf(
                    'Can not add %d whole months to %s.',
                    $months,
                    $this->format('c'),
                ),
            );
        }

        return $this;
    }

    /**
     * Subtracts months to this date without affecting the day of the month.
     *
     * This differs from {@link SwatDate::subtractMonths()} in how dates at the
     * end of a month are handled. In SwatDate::subtractMonths(), if one month
     * is subtracted from March 30, the resulting date will be March 1 or 2
     * depending on if it is a leap year.
     *
     * In this method, if one month is subtracted from March 30, an exception
     * is thrown.
     *
     * @param int $months the number of months to subtract
     *
     * @return SwatDate this object
     *
     * @throws Exception if the resulting date is invalid (i.e. February 30) an
     *                   exception is thrown.
     */
    public function subtractStrictMonths($months)
    {
        return $this->addStrictMonths(-$months);
    }

    /**
     * Sets the date fields for this date and checks if it is a valid date.
     *
     * This differs from PHP's DateTime in that it returns false if the
     * parameters are not a valid date (i.e. February 31st).
     *
     * @param int $year  the year
     * @param int $month the month
     * @param int $day   the day
     *
     * @return DateTime|false either this object on success, or false if the
     *                        resulting date is not a valid date
     */
    protected function setCheckedDate($year, $month, $day): DateTime|false
    {
        if (!checkdate($month, $day, $year)) {
            return false;
        }

        return $this->setDate($year, $month, $day);
    }
}
