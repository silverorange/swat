<?php
require_once('Swat/SwatControl.php');
require_once('Swat/SwatFlydown.php');
require_once('Date.php');

// TODO: figure out why the valid-ranges are getting having the time inproperly
// 		 offset.

/**
 * A time entry widget
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatTimeEntry extends SwatControl {
	
	/**
	 * Time of the widget
	 *
	 * The year, month, and day fields of the Date are
	 * unused and should be considered undefined.
	 * @var Date
	 */
	public $value = null;
	
	const  HOUR   = 1;
	const  MINUTE = 2;
	const  SECOND = 4;
	
	/**
	 * Time parts that are required
	 *
	 * Bitwise combination of SwatTime::HOUR,
	 * SwatTime::MINUTE, and SwatTime::SECOND.
	 * @var int
	 */
	public $required;
	
	/**
	 * Time parts that are displayed
	 *
	 * Bitwise combination of SwatTime::HOUR,
	 * SwatTime::MINUTE, and SwatTime::SECOND.
	 * @var int
	 */
	public $display;
	
	/**
	 * Start time of the valid range (inclusive)
	 *
	 * Default 00:00:00 The year, month, and day fields of the Date are
	 * ignored and should be considered undefined.
	 * @var Date
	 */
	public $valid_range_start;
	
	/**
	 * End time of the valid range (inclusive)
	 *
	 * Default 23:59:59 The year, month, and day fields of the Date
	 * are ignored and should be considered undefined.
	 * @var Date
	 */
	public $valid_range_end;
	
	private $hourfly;
	private $minutefly;
	private $secondfly;
	private $ampmfly;
	
	private $created = false;
	
	public function init() {
		$this->display  = self::HOUR | self::MINUTE;
		$this->required = $this->display;

		$date = new Date();
		$date->setYear(0);
		$date->setMonth(1);
		$date->setDay(1);
		$date->setHour(0);
		$date->setMinute(0);
		$date->setSecond(0);
		$date->setTZ('UTC');
		
		$this->valid_range_start = clone $date;

		$date->setHour(23);
		$date->setMinute(59);
		$date->setSecond(59);
		$this->valid_range_end = clone $date;
	}
	
	public function display() {
		$this->createFlydowns();
		$this->displayJavascript();
		
		if ($this->display & self::HOUR)
			$this->hourfly->display();
		
		if ($this->display & self::MINUTE)
			$this->minutefly->display();
		
		if ($this->display & self::SECOND)
			$this->secondfly->display();

		if ($this->display & self::HOUR)
			$this->ampmfly->display();
	}
	
	public function process() {
		$this->createFlydowns();

		if ($this->display & self::HOUR) {
			$this->hourfly->process();
			$this->ampmfly->process();
			$hour   = intval($this->hourfly->value);
			$ampm   = $this->ampmfly->value;
			
			if ($this->required & self::HOUR && $hour == -1)
				$this->addErrorMessage(_S("Hour is Required."));
				
			if ($this->required & self::HOUR && $ampm == -1)
				$this->addErrorMessage(_S("AM/PM is Required."));
			
			if ($ampm == 'pm') {
				$hour += 12;
				if ($hour == 24)
					$hour = 0;
			}
		} else {
			$hour = 0;
		}

		if ($this->display & self::MINUTE) {
			$this->minutefly->process();
			$minute = intval($this->minutefly->value);
			
			if ($this->required & self::MINUTE && $minute == -1)
				$this->addErrorMessage(_S("Minute is Required."));
		} else {
			$minute = 0;
		}

		if ($this->display & self::SECOND) {
			$this->secondfly->process();
			$second = intval($this->secondfly->value);
			
			if ($this->required & self::SECOND && $second == -1)
				$this->addErrorMessage(_S("Second is Required."));
		} else {
			$second = 0;
		}

		$this->value = new Date();
		$this->value->setYear(0);
		$this->value->setMonth(1);
		$this->value->setDay(1);
		$this->value->setHour($hour);
		$this->value->setMinute($minute);
		$this->value->setSecond($second);
		$this->value->setTZ('UTC');
		
		$this->validateRanges();
	}
	
	private function createFlydowns() { 
		if ($this->created) return;
		
		$this->created = true;

		if ($this->display & self::HOUR)
			$this->createHourFlydown();

		if ($this->display & self::MINUTE)
			$this->createMinuteFlydown();

		if ($this->display & self::SECOND)
			$this->createSecondFlydown();
			
		if ($this->display & self::HOUR)
			$this->createAmPmFlydown();
	}
	
	private function createHourFlydown() {
		$this->hourfly = new SwatFlydown($this->name.'_hour');
		$this->hourfly->options = array(-1 => '');
		$this->hourfly->onchange = sprintf("timeSet('%s', this);",
			$this->name);
				
		for ($i = 1; $i <= 12; $i++)
			$this->hourfly->options[$i] = $i;
	}
	
	private function createMinuteFlydown() {
		$this->minutefly = new SwatFlydown($this->name.'_minute');
		$this->minutefly->options = array(-1 => '');
		$this->minutefly->onchange = sprintf("timeSet('%s', this);",
			$this->name);
		
		for ($i = 0; $i <= 59; $i++)
			$this->minutefly->options[$i] = str_pad($i,2,'0',STR_PAD_LEFT);
	}
	
	private function createSecondFlydown() {	
		$this->secondfly = new SwatFlydown($this->name.'_second');
		$this->secondfly->options = array(-1 => '');
		$this->secondfly->onchange = sprintf("timeSet('%s', this);",
			$this->name);
		
		for ($i = 0; $i <= 59; $i++)
			$this->secondfly->options[$i] = str_pad($i,2,'0',STR_PAD_LEFT);
	}
	
	private function createAmPmFlydown() {
		$this->ampmfly = new SwatFlydown($this->name.'_ampm');
		$this->ampmfly->options = array(-1 => '', 'am' => 'AM', 'pm' => 'PM');
		$this->ampmfly->onchange = sprintf("timeSet('%s', this);",
			$this->name);
	}
	
	private function validateRanges() {
		$this->valid_range_start->setYear(0);
		$this->valid_range_start->setMonth(1);
		$this->valid_range_start->setDay(1);
		
		$this->valid_range_end->setYear(0);
		$this->valid_range_end->setMonth(1);
		$this->valid_range_end->setDay(1);
		
		if (Date::compare($this->value,$this->valid_range_start,true) == -1) {
			
			$msg=sprintf(_S("The time you have entered is invalid. 
				It must be after %s."),
				$this->displayTime($this->valid_range_start));
			$this->addErrorMessage($msg);
			
		} elseif (Date::compare($this->value,$this->valid_range_end,true) == 1) {
			
			$msg=sprintf(_S("The time you have entered is invalid. 
				It must be before %s."),
				$this->displayTime($this->valid_range_end));
			$this->addErrorMessage($msg);
			
		}
	}
	
	private function displayTime($time) {
		return $time->format('%r'); //%X
	}

	private function displayJavascript() {
		echo '<script type="text/javascript">';
		include_once('Swat/javascript/swat-find-index.js');
		include_once('Swat/javascript/swat-time.js');
		echo '</script>';
	}
}
?>
