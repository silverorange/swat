<?php


require_once('Swat/SwatControl.php');
require_once('Swat/SwatFlydown.php');
require_once('Swat/SwatState.php');
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
class SwatTimeEntry extends SwatControl implements SwatState {
	
	/**
	 * Time of the widget
	 *
	 * The year, month, and day fields of the Date are
	 * unused and should be considered undefined.
	 *
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
	 *
	 * @var int
	 */
	public $required;
	
	/**
	 * Time parts that are displayed
	 *
	 * Bitwise combination of SwatTime::HOUR,
	 * SwatTime::MINUTE, and SwatTime::SECOND.
	 *
	 * @var int
	 */
	public $display;
	
	/**
	 * Start time of the valid range (inclusive)
	 *
	 * Default 00:00:00 The year, month, and day fields of the Date are
	 * ignored and should be considered undefined.
	 *
	 * @var Date
	 */
	public $valid_range_start;
	
	/**
	 * End time of the valid range (inclusive)
	 *
	 * Default 23:59:59 The year, month, and day fields of the Date
	 * are ignored and should be considered undefined.
	 *
	 * @var Date
	 */
	public $valid_range_end;
	
	private $hour_flydown;
	private $minute_flydown;
	private $second_flydown;
	private $am_pm_flydown;
	
	private $created = false;
	
	public function init() {
		$this->display  = self::HOUR | self::MINUTE;
		$this->required = $this->display;

		$date = new SwatDate();
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
			$this->hour_flydown->display();
		
		if ($this->display & self::MINUTE)
			$this->minute_flydown->display();
		
		if ($this->display & self::SECOND)
			$this->second_flydown->display();

		if ($this->display & self::HOUR)
			$this->am_pm_flydown->display();
	}
	
	public function process() {
		$this->createFlydowns();

		if ($this->display & self::HOUR) {
			$this->hour_flydown->process();
			$this->am_pm_flydown->process();
			$hour = intval($this->hour_flydown->value);
			$ampm = $this->am_pm_flydown->value;
			
			if ($this->required & self::HOUR && $hour === null) {
				$msg = _S("Hour is Required.");
				$this->addMessage(new SwatMessage($msg, SwatMessage::USER_ERROR));
			}
				
			if ($this->required & self::HOUR && $ampm === null) {
				$msg = _S("AM/PM is Required.");
				$this->addMessage(new SwatMessage($msg, SwatMessage::USER_ERROR));
			}
			
			if ($ampm == 'pm') {
				$hour += 12;
				if ($hour == 24)
					$hour = 0;
			}
		} else {
			$hour = 0;
		}

		if ($this->display & self::MINUTE) {
			$this->minute_flydown->process();
			$minute = intval($this->minute_flydown->value);
			
			if ($this->required & self::MINUTE && $minute === null) {
				$msg = _S("Minute is Required.");
				$this->addMessage(new SwatMessage($msg, SwatMessage::USER_ERROR));
			}
		} else {
			$minute = 0;
		}

		if ($this->display & self::SECOND) {
			$this->second_flydown->process();
			$second = intval($this->second_flydown->value);
			
			if ($this->required & self::SECOND && $second === null) {
				$msg = _S("Second is Required.");
				$this->addMessage(new SwatMessage($msg, SwatMessage::USER_ERROR));
			}
		} else {
			$second = 0;
		}

		$this->value = new SwatDate();
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
		$this->hour_flydown = new SwatFlydown($this->id.'_hour');
		$this->hour_flydown->onchange = sprintf("timeSet('%s', this);",
			$this->id);
				
		for ($i = 1; $i <= 12; $i++)
			$this->hour_flydown->options[$i] = $i;
	}
	
	private function createMinuteFlydown() {
		$this->minute_flydown = new SwatFlydown($this->id.'_minute');
		$this->minute_flydown->onchange =
			sprintf("timeSet('%s', this);", $this->id);
		
		for ($i = 0; $i <= 59; $i++)
			$this->minute_flydown->options[$i] =
				str_pad($i, 2, '0', STR_PAD_LEFT);
	}
	
	private function createSecondFlydown() {	
		$this->second_flydown = new SwatFlydown($this->id.'_second');
		$this->second_flydown->onchange =
			sprintf("timeSet('%s', this);", $this->id);
		
		for ($i = 0; $i <= 59; $i++)
			$this->second_flydown->options[$i] =
				str_pad($i, 2 ,'0', STR_PAD_LEFT);
	}
	
	private function createAmPmFlydown() {
		$this->am_pm_flydown = new SwatFlydown($this->id.'_ampm');
		$this->am_pm_flydown->options = array('am' => 'AM', 'pm' => 'PM');
		$this->am_pm_flydown->onchange =
			sprintf("timeSet('%s', this);", $this->id);
	}
	
	private function validateRanges() {
		$this->valid_range_start->setYear(0);
		$this->valid_range_start->setMonth(1);
		$this->valid_range_start->setDay(1);
		
		$this->valid_range_end->setYear(0);
		$this->valid_range_end->setMonth(1);
		$this->valid_range_end->setDay(1);
		
		if (Date::compare($this->value, $this->valid_range_start, true) == -1) {
			
			$msg = sprintf(_S("The time you have entered is invalid. ".
				"It must be after %s."),
				$this->displayTime($this->valid_range_start));

			$this->addMessage(new SwatMessage($msg, SwatMessage::USER_ERROR));
			
		} elseif (Date::compare($this->value, $this->valid_range_end, true) == 1) {
			
			$msg = sprintf(_S("The time you have entered is invalid. ".
				"It must be before %s."),
				$this->displayTime($this->valid_range_end));
			
			$this->addMessage(new SwatMessage($msg, SwatMessage::USER_ERROR));
			
		}
	}
	
	private function displayTime($time) {
		return $time->format('%r'); // TODO: %X
	}

	private function displayJavascript() {
		echo '<script type="text/javascript">';
		include_once('Swat/javascript/swat-find-index.js');
		include_once('Swat/javascript/swat-time.js');
		echo '</script>';
	}
	
	public function getState() {
		if ($this->value === null)
			return null;
		else
			return $this->value->getDate();	
	}

	public function setState($state) {
		$this->value = new SwatDate($state);
	}
}

?>
