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
	 * Date parts that are required.
	 * @var int
	 */
	public $required;
	
	/**
	 * Date parts that are displayed.
	 * @var int
	 */
	public $display;
	
	/**
	 * Start date of the valid range (inclusive).
	 * Default date 20 Years in the past.
	 * @var Date
	 */
	public $valid_range_start;
	
	/**
	 * End date of the valid range (exclusive).
	 * Default date 20 Years in the future.
	 * @var Date
	 */
	public $valid_range_end;
	
	
	private $yearfly;
	private $monthfly;
	private $dayfly;
	
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
		
		if ($this->display & self::MONTH)
			$this->monthfly->display();
		
		if ($this->display & self::DAY)
			$this->dayfly->display();
		
		if ($this->display & self::YEAR)
			$this->yearfly->display();
	}
	
	public function process() {
		$this->createFlydowns();

		$this->yearfly->process();
		$this->monthfly->process();
		$this->dayfly->process();

		$year  = intval($this->yearfly->value);
		$month = intval($this->monthfly->value);
		$day   = intval($this->dayfly->value);

		$this->value = new Date();
		$this->value->setYear($year);
		$this->value->setMonth($month);
		$this->value->setDay($day);

		echo $this->value->getDate();

		/*
		TODO: validate based on ranges here
		if ($this->validate == self::validate_future) {
			if ($this->datechk && !($this->datechk())) {
				$this->addErrorMessage(_S("The email address you have entered ".
				                          "is not properly formatted."));
			}
		}
		*/
	}
	
	private function createFlydowns() { 
		if ($this->created) return;
		
		$this->created = true;
		
		if ($this->display & self::MONTH) {
			$this->monthfly = new SwatFlydown($this->name.'_month');
			$this->monthfly->options = array(0 => '');
			$this->monthfly->onchange = "dateSet('$this->name',this.form);";
			
			for ($i = 1; $i <= 12; $i++)
				$this->monthfly->options[$i] = Date_Calc::getMonthFullName($i);
		}
		
		if ($this->display & self::DAY) {
			$this->dayfly = new SwatFlydown($this->name.'_day');
			$this->dayfly->options = array(0 => '');
			$this->dayfly->onchange = "dateSet('$this->name',this.form);";
			
			for ($i = 1; $i <= 31; $i++)
				$this->dayfly->options[$i] = $i;
		}
		
		if ($this->display & self::YEAR) {
			$this->yearfly = new SwatFlydown($this->name.'_year');
			$this->yearfly->options = array(0 => '');
			$this->yearfly->onchange = "dateSet('$this->name',this.form);";
			
			$startyear = $this->valid_range_start->getYear();
			$endyear   = $this->valid_range_end->getYear();
			
			for ($i = $startyear; $i <= $endyear; $i++)
				$this->yearfly->options[$i] = $i;
		}
	}
	
	

	private function displayJavascript() {
		// TODO: needs work - broken
		//		 also, change it so it doesn't need to be outputed twice
		//		 for two different date classes.
		?>
		<script type="text/javascript">
			function dateSet(name,theForm) {
				var vDate = new Date();
				
				var year  = (eval("theForm." + name + "_year"));
				var month = (eval("theForm." + name + "_month"));
				var day   = (eval("theForm." + name + "_day"));
				
				// stop if all 3 date parts aren't present
				if (!year || !month || !day) return false;
				
				if (month.selectedIndex == 0) {
					//reset
					day.selectedIndex = 0;
					year.selectedIndex = 0;
				} else {
					var this_month = vDate.getMonth()+1;
					if (month.selectedIndex == this_month)
						today = true;
					else
						today = false;
					
					if (day.selectedIndex == 0) {
						if (today) day.selectedIndex = vDate.getDate();
						else day.selectedIndex = 1;
					}
					
					if (year.selectedIndex==0) {
						var first_year = year.options[1].value;
						var this_year  = vDate.getFullYear();
						year.selectedIndex = (this_year - first_year + 1);
					}
				}
			}
		</script>
		<?php
	}
}
?>
