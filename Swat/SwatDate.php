<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatControl.php');
require_once('Swat/SwatFlydown.php');
require_once('Date.php');

/**
 * A date entry widget.
 */
class SwatDate extends SwatControl {
	
	/**
	 * Date of the widget.
	 * @var Date
	 */
	public $value = null;
	
	const  YEAR  = 1;
	const  MONTH = 2;
	const  DAY   = 4;
	const  TIME  = 8;
		
	/**
	 * Date parts that are required. Bitwise combination of SwatDate::YEAR,
	 * SwatDate::MONTH, SwatDate::DAY, and SwatDate::TIME.
	 * @var int
	 */
	public $required;
	
	/**
	 * Date parts that are displayed. Bitwise combination of SwatDate::YEAR,
	 * SwatDate::MONTH, SwatDate::DAY, and SwatDate::TIME.
	 * @var int
	 */
	public $display;
	
	/**
	 * Start date of the valid range (inclusive).
	 * Default to 20 years in the past.
	 * @var Date
	 */
	public $valid_range_start;
	
	/**
	 * End date of the valid range (exclusive).
	 * Default to 20 years in the future.
	 * @var Date
	 */
	public $valid_range_end;
	
	
	private $yearfly;
	private $monthfly;
	private $dayfly;
	private $timefly;
	
	private $created = false;
	
	public function init() {
		$this->required = self::YEAR | self::MONTH | self::DAY;
		$this->display  = self::YEAR | self::MONTH | self::DAY;

		$this->setValidRange(-20, +20);
	}
		
	/**
	 * Set the valid date range.
	 * Convenience method to set the valid date range by year offsets.
	 * @param int $start_offset Offset from the current year used to set the
	 *                          starting year of the valid range.
	 * @param int $end_offset Offset from the current year used to set the
	 *                          ending year of the valid range.
	 */
	public function setValidRange($start_offset, $end_offset) {
		//beginning of this year
		$date = new Date();
		$date->setMonth(1);
		$date->setDay(1);
		$date->setHour(0);
		$date->setMinute(0);
		$date->setSecond(0);
		
		$this->valid_range_start = clone $date;
		$this->valid_range_end = clone $date;

		$year = $date->getYear();
		$this->valid_range_start->setYear($year + $start_offset);
		$this->valid_range_end->setYear($year + $end_offset);
	}
	
	public function display() {
		$this->createFlydowns();
		$this->displayJavascript();
		
		// TODO: order these based on locale		
		if ($this->display & self::MONTH)
			$this->monthfly->display();
		
		if ($this->display & self::DAY)
			$this->dayfly->display();
		
		if ($this->display & self::YEAR)
			$this->yearfly->display();
			
		if ($this->display & self::TIME)
			$this->timefly->display();
			
		include_once('Swat/SwatCalendar.php');
		$cal = new SwatCalendar();
		$cal->name = $this->name;
		$cal->valid_range_start = $this->valid_range_start;
		$cal->valid_range_end   = $this->valid_range_end;
		$cal->display();
	}
	
	public function process() {
		$this->createFlydowns();

		if ($this->display & self::YEAR) {
			$this->yearfly->process();
			$year = intval($this->yearfly->value);
			
			if ($year == -1)
				$this->addErrorMessage(_S("Year is Required."));
		} else {
			$year = 0;
		}

		if ($this->display & self::MONTH) {
			$this->monthfly->process();
			$month = intval($this->monthfly->value);
			
			if ($month == -1)
				$this->addErrorMessage(_S("Month is Required."));
		} else {
			$month = 1;
		}

		if ($this->display & self::DAY) {
			$this->dayfly->process();
			$day = intval($this->dayfly->value);
			
			if ($day == -1)
				$this->addErrorMessage(_S("Day is Required."));
		} else {
			$day = 1;
		}
		
		if ($this->display & self::TIME) {
			$this->timefly->process();
			$hour = $this->timefly->value->getHour();
			$minute = $this->timefly->value->getMinute();
			$second = $this->timefly->value->getSecond();
		} else {
			$hour=0;
			$minute=0;
			$second=0;
		}

		$this->value = new Date();
		$this->value->setYear($year);
		$this->value->setMonth($month);
		$this->value->setDay($day);
		$this->value->setHour($hour);
		$this->value->setMinute($minute);
		$this->value->setSecond($second);

		$this->validateRanges();
	}
	
	private function createFlydowns() { 
		if ($this->created) return;
		
		$this->created = true;

		if ($this->display & self::YEAR)
			$this->createYearFlydown();

		if ($this->display & self::MONTH)
			$this->createMonthFlydown();

		if ($this->display & self::DAY)
			$this->createDayFlydown();
			
		if ($this->display & self::TIME)
			$this->createTimeFlydown();
	}

	private function createYearFlydown() { 
		$this->yearfly = new SwatFlydown($this->name.'_year');
		$this->yearfly->options = array(-1 => '');
		$this->yearfly->onchange = sprintf("dateSet('%s', this);",
			$this->name);

		$start_year = $this->valid_range_start->getYear();
		$end_year   = $this->valid_range_end->getYear();

		for ($i = $start_year; $i <= $end_year; $i++)
			$this->yearfly->options[$i] = $i;
	}
		
	private function createMonthFlydown() { 
		$this->monthfly = new SwatFlydown($this->name.'_month');
		$this->monthfly->options = array(-1 => '');
		$this->monthfly->onchange = sprintf("dateSet('%s', this);",
			$this->name);

		$start_year = $this->valid_range_start->getYear();
		$end_year   = $this->valid_range_end->getYear();

		if ($end_year == $start_year) {

			$start_month = $this->valid_range_start->getMonth();
			$end_month = $this->valid_range_end->getMonth();

			for ($i = $start_month; $i <= $end_month; $i++)
				$this->monthfly->options[$i] = Date_Calc::getMonthFullName($i);

		} elseif (($end_year - $start_year) == 1) {

			$start_month = $this->valid_range_start->getMonth();
			$end_month = $this->valid_range_end->getMonth();

			for ($i = $start_month; $i <= 12; $i++)
				$this->monthfly->options[$i] = Date_Calc::getMonthFullName($i);

			for ($i = 1; $i <= $end_month; $i++)
				$this->monthfly->options[$i] = Date_Calc::getMonthFullName($i);

		} else {

			for ($i = 1; $i <= 12; $i++)
				$this->monthfly->options[$i] = Date_Calc::getMonthFullName($i);

		}
	}
		
	private function createDayFlydown() {
		$this->dayfly = new SwatFlydown($this->name.'_day');
		$this->dayfly->options = array(-1 => '');
		$this->dayfly->onchange = sprintf("dateSet('%s', this);",
			$this->name);

		$start_year  = $this->valid_range_start->getYear();
		$end_year    = $this->valid_range_end->getYear();
		$start_month = $this->valid_range_start->getMonth();
		$end_month   = $this->valid_range_end->getMonth();

		$end_check = clone($this->valid_range_start);
		$end_check->addSeconds(2678400); //add 31 days
		
		if ($start_year == $end_year && $start_month == $end_month) {
			
			$start_day = $this->valid_range_start->getDay();
			$end_day   = $this->valid_range_end->getDay();

			for ($i = $start_day; $i <= $end_day; $i++)
				$this->dayfly->options[$i] = $i;
		
		} elseif (Date::compare($end_check,$this->valid_range_end,true) != -1) {
			
			$start_day = $this->valid_range_start->getDay();
			$end_day   = $this->valid_range_end->getDay();
			$days_in_month = $this->valid_range_start->getDaysInMonth();
			
			for ($i = $start_day; $i <= $days_in_month; $i++)
				$this->dayfly->options[$i] = $i;

			for ($i = 1; $i <= $end_day; $i++)
				$this->dayfly->options[$i] = $i;
			
		} else {
			
			for ($i = 1; $i <= 31; $i++)
				$this->dayfly->options[$i] = $i;
				
		}
	}
	
	private function createTimeFlydown() {
		require_once('Swat/SwatTime.php');
		
		$this->timefly = new SwatTime();
		$this->timefly->name = $this->name;
	}
	
	private function validateRanges() {
		if (Date::compare($this->value,$this->valid_range_start,true) == -1) {
			
			$msg=sprintf(_S("The date you have entered is invalid. 
				It must be after %s."),
				$this->displayDate($this->valid_range_start));
			$this->addErrorMessage($msg);
			
		} elseif (Date::compare($this->value,$this->valid_range_end,true) == 1) {
			
			$msg=sprintf(_S("The date you have entered is invalid. 
				It must be before %s."),
				$this->displayDate($this->valid_range_end));
			$this->addErrorMessage($msg);
			
		}
	}
	
	private function displayDate($date) {
		$time  = '';
		$day   = '';
		$month = '';
		$year  = '';
	
		if ($this->display & self::TIME)
			$time = ' %I:%M %p';	
			
		if ($this->display & self::DAY)
			$day = ' %d';
		
		if ($this->display & self::MONTH)
			$month = ' %b';
		
		if ($this->display & self::YEAR)
			$year = ' %Y';
			
		return trim($date->format($month.$day.$year.$time));
	}
	
	private function displayJavascript() {
		echo '<script type="text/javascript">';
		include_once('Swat/javascript/swat-find-index.js');
		include_once('Swat/javascript/swat-date.js');
		echo '</script>';
	}
}
?>
