<?php

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatFlydown.php';
require_once 'Swat/SwatDate.php';
require_once 'Swat/SwatState.php';

/**
 * A date entry widget
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDateEntry extends SwatControl implements SwatState
{
	const YEAR     = 1;
	const MONTH    = 2;
	const DAY      = 4;
	const TIME     = 8;
	const CALENDAR = 16;

	/**
	 * Date of this date entry widget
	 *
	 * @var Date
	 */
	public $value = null;

	/**
	 * Required
	 *
	 * Must have a non-empty value when processed.
	 *
	 * @var boolean
	 */
	public $required = false;

	/**
	 * Required date parts
	 *
	 * Bitwise combination of {@link SwatDateEntry::YEAR},
	 * {@link SwatDateEntry::MONTH}, {@link SwatDateEntry::DAY} and
	 * {@link SwatDateEntry::TIME}.
	 *
	 * For example, to require the month and day to be entered in a date
	 * selector widget use the following:
	 *
	 * <code>
	 * $date->required_parts = SwatDateEntry::MONTH | SwatDateEntry::DAY;
	 * </code>
	 *
	 * @var integer
	 */
	public $required_parts;

	/**
	 * Displayed date parts
	 *
	 * Bitwise combination of {@link SwatDateEntry::YEAR},
	 * {@link SwatDateEntry::MONTH}, {@link SwatDateEntry::DAY},
	 * {@link SwatDateEntry::TIME} and {@link SwatDateEntry::CALENDAR}.
	 *
	 * For example, to show a date selector widget with just the month and year
	 * use the following:
	 *
	 * <code>
	 * $date->display_parts = SwatDateEntry::YEAR | SwatDateEntry::MONTH;
	 * </code>
	 *
	 * @var integer
	 */
	public $display_parts;

	/**
	 * Start date of the valid range (inclusive)
	 *
	 * Defaults to 20 years in the past.
	 *
	 * @var Date
	 */
	public $valid_range_start;

	/**
	 * End date of the valid range (exclusive)
	 *
	 * Defaults to 20 years in the future.
	 *
	 * @var Date
	 */
	public $valid_range_end;

	/**
	 * Whether the numeric month code is displayed in the month flydown
	 *
	 * This is useful for credit card date entry
	 *
	 * @var boolean
	 */
	public $show_month_number = false;

	/**
	 * A reference to the internal year flydown
	 *
	 * @var SwatFlydown
	 */
	private $year_flydown = null;

	/**
	 * A reference to the internal month flydown
	 *
	 * @var SwatFlydown
	 */
	private $month_flydown = null;

	/**
	 * A reference to the internal day flydown
	 *
	 * @var SwatFlydown
	 */
	private $day_flydown = null;

	/**
	 * A reference to the internal time entry
	 *
	 * @var SwatTimeEntry
	 */
	private $time_entry = null;

	/**
	 * An internal flag telling whether internal widgets have been
	 * created or not
	 *
	 * @var boolean
	 */
	private $created = false;

	/**
	 * Initializes this widget
	 *
	 * Sets default required and display parts and sets default valid range
	 * for this date entry.
	 */
	public function init()
	{
		$this->required_parts = self::YEAR | self::MONTH | self::DAY;
		$this->display_parts  = self::YEAR | self::MONTH |
								self::DAY | self::CALENDAR;

		$this->setValidRange(-20, 20);

		// an id is required for this widget.
		if ($this->id === null)
			$this->id = $this->getUniqueId();
	}

	/**
	 * Set the valid date range
	 *
	 * Convenience method to set the valid date range by year offsets.
	 *
	 * @param integer $start_offset offset from the current year in years used
	 *                               to set the starting year of the valid
	 *                               range.
	 * @param integer $end_offset offset from the current year in years used
	 *                             to set the ending year of the valid range.
	 */
	public function setValidRange($start_offset, $end_offset)
	{
		// Beginning of this year
		$date = new SwatDate();
		$date->setMonth(1);
		$date->setDay(1);
		$date->setHour(0);
		$date->setMinute(0);
		$date->setSecond(0);
		$date->setTZ('UTC');

		$this->valid_range_start = clone $date;
		$this->valid_range_end = clone $date;

		$year = $date->getYear();
		$this->valid_range_start->setYear($year + $start_offset);
		$this->valid_range_end->setYear($year + $end_offset);
	}

	/**
	 * Displays this date entry
	 *
	 * Creates internal widgets if they do not exits then displays required
	 * javascript, then displays internal widgets.
	 */
	public function display()
	{
		$this->createFlydowns();

		echo '<span class="swat-date-span">';

		/*
		 * NOTE: Using php date functions here because the Date class does not
		 *       seem to support locale-ordering of date parts.
		 * This returns something like: mm/dd/yy or dd.mm.yyyy
		 */
		$order = split('[/.-]', strftime('%x', mktime(0, 0, 0, 1, 2, 2003)));

		foreach ($order as $datepart) {
			$m = ($datepart == 1);
			$d = ($datepart == 2);
			// strftime outputs the year as either 2003 or 03 depending
			// on the locale
			$y = ($datepart == 2003 || $datepart == 3);

			if ($m && $datepart == 1 && $this->display_parts & self::MONTH) {
				if ($this->value !== null)
					$this->month_flydown->value = $this->value->getMonth();	

				$this->month_flydown->display();
			} elseif ($d && $datepart == 2 &&
				$this->display_parts & self::DAY) {
				
				if ($this->value !== null)
					$this->day_flydown->value = $this->value->getDay();

				$this->day_flydown->display();
			} elseif ($y && $this->display_parts & self::YEAR) {
				if ($this->value !== null)
					$this->year_flydown->value = $this->value->getYear();

				$this->year_flydown->display();
			}
		}

		echo '</span>';

		if ($this->display_parts & self::TIME) {
			echo ' ';
			$this->time_entry->display();
		}

		// calendar javascript is displayed last as it looks for a js object
		// created here.
		$this->displayJavascript();

		if ($this->display_parts & self::CALENDAR) {
			require_once 'Swat/SwatCalendar.php';
			$cal = new SwatCalendar($this->id.'_calendar');
			$cal->entry_id = $this->id;
			$cal->valid_range_start = $this->valid_range_start;
			$cal->valid_range_end   = $this->valid_range_end;
			$cal->display();
		}
	}

	/**
	 * Processes this date entry
	 *
	 * Creates internal widgets if they do not exist and then assigns their
	 * values based on the date entered by the user. If the date is not valid,
	 * an error message is attached to this date entry.
	 */
	public function process()
	{
		$this->createFlydowns();

		$all_empty = true;

		if ($this->display_parts & self::YEAR) {
			$this->year_flydown->process();
			$year = $this->year_flydown->value;
			$all_empty = $all_empty && ($year === null);
		}

		if ($this->display_parts & self::MONTH) {
			$this->month_flydown->process();
			$month = $this->month_flydown->value;
			$all_empty = $all_empty && ($month === null);
		}

		if ($this->display_parts & self::DAY) {
			$this->day_flydown->process();
			$day = $this->day_flydown->value;
			$all_empty = $all_empty && ($day === null);
		}

		if ($this->display_parts & self::TIME) {
			$this->time_entry->process();
			$hour = $this->time_entry->value->getHour();
			$minute = $this->time_entry->value->getMinute();
			$second = $this->time_entry->value->getSecond();
		} else {
			$hour=0;
			$minute=0;
			$second=0;
		}

		if ($this->required && $all_empty) {
			$msg = Swat::_('Date is Required.');
			$this->addMessage(new SwatMessage($msg, SwatMessage::USER_ERROR));
		}

		if ($this->display_parts & self::YEAR) {
			if (!$all_empty && $year === null && 
				($this->required_parts & self::YEAR)) {
				$msg = Swat::_('Year is Required.');
				$this->addMessage(
					new SwatMessage($msg, SwatMessage::USER_ERROR));
			}
		} else {
			$year = 0;
		}

		if ($this->display_parts & self::MONTH) {
			if (!$all_empty && $month === null &&
				($this->required_parts & self::MONTH)) {
				$msg = Swat::_('Month is Required.');
				$this->addMessage(
					new SwatMessage($msg, SwatMessage::USER_ERROR));
			}
		} else {
			$month = 1;
		}

		if ($this->display_parts & self::DAY) {
			if (!$all_empty && $day === null &&
				($this->required_parts & self::DAY)) {
				$msg = Swat::_('Day is Required.');
				$this->addMessage(
					new SwatMessage($msg, SwatMessage::USER_ERROR));
			}
		} else {
			$day = 1;
		}

		if ($all_empty) {
			$this->value = null;
		} else {
			$this->value = new SwatDate();
			$this->value->setYear($year);
			$this->value->setMonth($month);
			$this->value->setDay($day);
			$this->value->setHour($hour);
			$this->value->setMinute($minute);
			$this->value->setSecond($second);
			$this->value->setTZ('UTC');

			$this->validateRanges();
		}
	}

	/**
	 * Gets the current state of this date entry widget
	 *
	 * @return boolean the current state of this date entry widget.
	 *
	 * @see SwatState::getState()
	 */
	public function getState()
	{
		if ($this->value === null)
			return null;
		else
			return $this->value->getDate();	
	}

	/**
	 * Sets the current state of this date entry widget
	 *
	 * @param boolean $state the new state of this date entry widget.
	 *
	 * @see SwatState::setState()
	 */
	public function setState($state)
	{
		$this->value = new SwatDate($state);
	}

	/**
	 * Creates all internal widgets required for this date entry
	 */
	private function createFlydowns()
	{ 
		if ($this->created) return;

		$this->created = true;

		if ($this->display_parts & self::YEAR)
			$this->createYearFlydown();

		if ($this->display_parts & self::MONTH)
			$this->createMonthFlydown();

		if ($this->display_parts & self::DAY)
			$this->createDayFlydown();

		if ($this->display_parts & self::TIME)
			$this->createTimeEntry();
	}

	/**
	 * Creates the year flydown for this date entry
	 */
	private function createYearFlydown()
	{
		$this->year_flydown = new SwatFlydown($this->id.'_year');
		$this->year_flydown->onchange = sprintf('%s.set(this);', $this->id);

		$start_year = $this->valid_range_start->getYear();

		$tmp = clone $this->valid_range_end;
        $tmp->subtractSeconds(1);
        $end_year = $tmp->getYear();

		for ($i = $start_year; $i <= $end_year; $i++)
			$this->year_flydown->addOption($i, $i);
	}

	/**
	 * Creates the month flydown for this date entry
	 */
	private function createMonthFlydown()
	{
		$this->month_flydown = new SwatFlydown($this->id.'_month');
		$this->month_flydown->onchange = sprintf('%s.set(this);', $this->id);

		$start_year = $this->valid_range_start->getYear();
		$tmp = clone $this->valid_range_end;
        $tmp->subtractSeconds(1);
        $end_year = $tmp->getYear();

		if ($end_year == $start_year) {

			$start_month = $this->valid_range_start->getMonth();
			$end_month = $this->valid_range_end->getMonth();

			for ($i = $start_month; $i <= $end_month; $i++)
				$this->month_flydown->addOption($i,
					$this->getMonthOptionText($i));

		} elseif (($end_year - $start_year) == 1) {

			$start_month = $this->valid_range_start->getMonth();
			$end_month = $this->valid_range_end->getMonth();

			for ($i = $start_month; $i <= 12; $i++)
				$this->month_flydown->addOption($i,
					$this->getMonthOptionText($i));

			for ($i = 1; $i <= $end_month; $i++)
				$this->month_flydown->addOption($i,
					$this->getMonthOptionText($i));

		} else {

			for ($i = 1; $i <= 12; $i++)
				$this->month_flydown->addOption($i,
					$this->getMonthOptionText($i));

		}
	}

	/**
	 * Gets the title of a month flydown option
	 *
	 * @param integer $month the numeric identifier of the month.
	 *
	 * @return string the option text of the month.
	 */
	private function getMonthOptionText($month)
	{
		$option = '';

		if ($this->show_month_number)
			$option .= '('.str_pad($month, 2, '0', STR_PAD_LEFT).') ';
			
		$option .= Date_Calc::getMonthFullName($month);

		return $option;
	}
		
	/**
	 * Creates the day flydown for this date entry
	 */
	private function createDayFlydown()
	{
		$this->day_flydown = new SwatFlydown($this->id.'_day');
		$this->day_flydown->onchange = sprintf('%s.set(this);', $this->id);

		$start_year  = $this->valid_range_start->getYear();

		$tmp = clone $this->valid_range_end;
        $tmp->subtractSeconds(1);
        $end_year = $tmp->getYear();

		$start_month = $this->valid_range_start->getMonth();
		$end_month   = $this->valid_range_end->getMonth();

		$end_check = clone($this->valid_range_start);
		$end_check->addSeconds(2678400); // add 31 days

		if ($start_year == $end_year && $start_month == $end_month) {

			$start_day = $this->valid_range_start->getDay();
			$end_day   = $this->valid_range_end->getDay();

			for ($i = $start_day; $i <= $end_day; $i++)
				$this->day_flydown->addOptions($i, $i);

		} elseif (Date::compare($end_check,$this->valid_range_end,true) != -1) {

			$start_day = $this->valid_range_start->getDay();
			$end_day   = $this->valid_range_end->getDay();
			$days_in_month = $this->valid_range_start->getDaysInMonth();

			for ($i = $start_day; $i <= $days_in_month; $i++)
				$this->day_flydown->addOption($i, $i);

			for ($i = 1; $i <= $end_day; $i++)
				$this->day_flydown->addOption($i, $i);

		} else {

			for ($i = 1; $i <= 31; $i++)
				$this->day_flydown->addOption($i, $i);

		}
	}

	/**
	 * Creates the time entry widget for this date entry
	 */
	private function createTimeEntry()
	{
		require_once 'Swat/SwatTimeEntry.php';

		$this->time_entry = new SwatTimeEntry();
		$this->time_entry->id = $this->id.'_time_entry';
	}

	/**
	 * Makes sure the date the user entered is within the valid range
	 *
	 * If the date is not within the valid range, this method attaches an
	 * error message to this date entry.
	 */
	private function validateRanges()
	{
		if (Date::compare($this->value,$this->valid_range_start,true) == -1) {

			$msg = sprintf(Swat::_('The date you have entered is invalid. It must be after %s.'),
				$this->getFormattedDate($this->valid_range_start));

			$this->addMessage(new SwatMessage($msg, SwatMessage::USER_ERROR));

		} elseif
			(Date::compare($this->value, $this->valid_range_end, true) == 1) {

			$msg = sprintf(Swat::_('The date you have entered is invalid. It must be before %s.'),
				$this->getFormattedDate($this->valid_range_end));

			$this->addMessage(new SwatMessage($msg, SwatMessage::USER_ERROR));

		}
	}

	/**
	 * Formats a date for this date entry
	 *
	 * Returns a date string formatted according to the properties of this
	 * date entry widget. This is used primarily for returning formatted
	 * valid start and valid end dates for user error messages.
	 *
	 * @param Date $date the date object to format.
	 *
	 * @return string a date formatted according to the properties of this date
	 *                 entry.
	 */
	private function getFormattedDate($date)
	{
		$time  = '';
		$day   = '';
		$month = '';
		$year  = '';

		if ($this->display_parts & self::TIME)
			$time = ' %I:%M %p';	

		if ($this->display_parts & self::DAY)
			$day = ' %d';

		if ($this->display_parts & self::MONTH)
			$month = ' %b';

		if ($this->display_parts & self::YEAR)
			$year = ' %Y';

		return trim($date->format($month.$day.$year.$time));
	}

	/**
	 * Outputs the javascript required for this control
	 */
	private function displayJavascript()
	{
		static $shown = false;

		if (!$shown) {
			echo '<script type="text/javascript" src="swat/javascript/swat-find-index.js"></script>';
			echo '<script type="text/javascript" src="swat/javascript/swat-date.js"></script>';
			$shown = true;
		}

		echo '<script type="text/javascript">';

		echo sprintf("%s = new SwatDate('%s');\n", $this->id, $this->id);
		
		if ($this->display_parts & self::TIME) {
			echo sprintf("%s.setSwatTime(%s_time_entry);\n",
				$this->id, $this->id);
		}

		echo '</script>';
	}
}

?>
