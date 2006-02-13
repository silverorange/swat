<?php

require_once 'Date.php';

/**
 * Date Tools
 *
 * @package   Swat
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDate extends Date
{
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

	public function __construct($date)
	{
		parent::Date($date);
	}

	/**
	 * Formats this date given either a format string of a format id
	 *
	 * @param mixed $format either a format string or an integer format id.
	 *
	 * @return string the formatted date.
	 */
	public function format($format)
	{
		if (is_int($format))
			$format = self::getFormatById($format);

		return parent::format($format);
	}

	/**
	 * Gets a date format string by id
	 *
	 * @param integer $id the id of the format string to retrieve.
	 *
	 * @return string the formatting string that was requested.
	 *
	 * @throws SwatException
	 */
	static function getFormatById($id)
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
		default:
			throw new Exception("Unknown date format id '$id'.");
		}
	}
}

?>
