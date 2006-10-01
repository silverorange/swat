<?php

require_once 'Date.php';
require_once 'Swat/Swat.php';

/**
 * Date Tools
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDate extends Date
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
	 * Atlantic Standard Time
	 */
	const TZ_LONG                   = 3;

	/**
	 * ADT
	 */
	const TZ_DST_SHORT              = 4;

	/**
	 * Atlantic Daylight Time
	 */
	const TZ_DST_LONG               = 5;

	/**
	 * AST/ADT
	 */
	const TZ_COMBINED               = 6;

	/**
	 * AST or ADT, depending on inDaylightTime() for the date.
	 */
	const TZ_CURRENT_SHORT          = 7;

	/**
	 * Atlantic Standard Time or Atlantic Daylight Time,
	 * depending on inDaylightTime() for the date.
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
	 * July 2, 2002 10:09 AM
	 */
	const DF_DATE_TIME              = 5;

	/**
	 * Tuesday, July 2, 2002 10:09 AM
	 */
	const DF_DATE_TIME_LONG         = 6;

	/**
	 * 10:09 AM
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
	 * Aug 5, 2002 10:09 AM
	 */
	const DF_DATE_TIME_SHORT        = 10;

	/**
	 * Aug 5, 10:09 AM
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

	// }}}
	// {{{ public function __construct()

	public function __construct($date = null)
	{
		parent::Date($date);
	}

	// }}}
	// {{{ public function format()

	/**
	 * Formats this date given either a format string or a format id
	 *
	 * @param mixed $format either a format string or an integer format id.
	 * @param integer $tz_format optional time zone format id.
	 *
	 * @return string the formatted date.
	 */
	public function format($format, $tz_format = null)
	{
		if (is_int($format))
			$format = self::getFormatById($format);

		$out = parent::format($format);

		if ($tz_format !== null)
			$out.= ' '.$this->formatTZ($tz_format);

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
			$out = $this->tz->getID();
			break;
		case self::TZ_SHORT:
			$out = $this->tz->getShortName();
			break;
		case self::TZ_LONG:
			$out = $this->tz->getLongName();
			break;
		case self::TZ_DST_SHORT:
			$out = $this->tz->getDSTShortName();
			break;
		case self::TZ_DST_LONG:
			$out = $this->tz->getDSTLongName();
			break;
		case self::TZ_CURRENT_SHORT:
			$out = $this->tz->inDaylightTime($this) ? 
				$this->tz->getDSTShortName() : $this->tz->getShortName();

			break;
		case self::TZ_CURRENT_LONG:
			$out = $this->tz->inDaylightTime($this) ? 
				$this->tz->getDSTLongName() : $this->tz->getLongName();

			break;
		case self::TZ_COMBINED:
			$out = sprintf('%s/%s', 
				$this->tz->getShortName(), $this->tz->getDSTShortName());

			break;
		}

		return $out;
	}

	// }}}
	// {{{ pulbic function clearTime()

	/**
	 * Clears the time portion of the date object
	 */
	public function clearTime()
	{
		$this->setHour(0);
		$this->setMinute(0);
		$this->setSecond(0);
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
		switch ($id) {
		case self::DF_MDY:
			return Swat::_('%D');
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
			return Swat::_('%b %e, %Y');
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
		default:
			throw new Exception("Unknown date format id '$id'.");
		}
	}

	// }}}
}

?>
