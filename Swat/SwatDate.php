<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatControl.php');
require_once('Swat/SwatHtmlTag.php');
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
	 * @var Date
	 */
	public $valid_range_start;
	
	/**
	 * End date of the valid range (exclusive).
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
		
		//beginning of this year
		$date = new Date();
		$date->setMonth(1);
		$date->setDay(1);
		$date->setHour(0);
		$date->setMinute(0);
		$date->setSecond(0);
		$year = $date->getYear();
		
		$this->valid_range_start = clone $date;
		$this->valid_range_start->setYear($year - 20);
		
		$this->valid_range_end = clone $date;
		$this->valid_range_end->setYear($year + 20);
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
		
		$this->monthfly->process();
		$this->dayfly->process();
		$this->yearfly->process();
		
		$month = $this->monthfly->value;
		$day = $this->dayfly->value;
		$year = $this->yearfly->value;
		
		//set to date object from above
		#$this->value = ;
		
		echo "month: $month day: $day year: $year";
		
		/*
		validate based on ranges here
		if ($this->validate == self::validate_future) {
			if ($this->datechk && !($this->datechk())) {
				$this->addErrorMessage(_S("The email address you have entered is not properly formatted."));
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
			$this->monthfly->onchange = 'dateSet'.$this->name.'(this.form);';
			
			for ($i = 1; $i <= 12; $i++)
				$this->monthfly->options[$i] = Date_Calc::getMonthFullName($i);
		}
		
		if ($this->display & self::DAY) {
			$this->dayfly = new SwatFlydown($this->name.'_day');
			$this->dayfly->options = array(0 => '');
			$this->dayfly->onchange = 'dateSet'.$this->name.'(this.form);';
			
			for ($i = 1; $i <= 31; $i++)
				$this->dayfly->options[$i] = $i;
		}
		
		if ($this->display & self::YEAR) {
			$this->yearfly = new SwatFlydown($this->name.'_year');
			$this->yearfly->options = array(0 => '');
			$this->yearfly->onchange = 'dateSet'.$this->name.'(this.form);';
			
			$startyear = intval(date('Y'));
			$endyear = $startyear+10;
			
			for ($i = $startyear; $i <= $endyear; $i++)
				$this->yearfly->options[$i] = $i;
		}
	}
	
	

	private function displayJavascript() {
		// TODO: needs work - broken
		//		 also, change it so it doesn't need to be outputed twice for two different date classes.
		?>
		<script type="text/javascript">
			function dateSet<?=$this->name?>(theForm) {
				var vDate = new Date();
				if (theForm.<?=$this->name?>_month.selectedIndex==0) {
					//reset
					theForm.<?=$this->name?>_day.selectedIndex=0;
					theForm.<?=$this->name?>_year.selectedIndex=0;
				} else {
					if (theForm.<?=$this->name?>_month.selectedIndex==vDate.getMonth()+1) today=true;
					else today=false;
					
					if (theForm.<?=$this->name?>_day.selectedIndex==0) {
						if (today) theForm.<?=$this->name?>_day.selectedIndex=vDate.getDate();
						else theForm.<?=$this->name?>_day.selectedIndex=1;
					}
					
					if (theForm.<?=$this->name?>_year.selectedIndex==0) {
						theForm.<?=$this->name?>_year.selectedIndex=(vDate.getFullYear()-<?=$this->year_start?>+1);
					}
				}
			}
		</script>
		<?php
	}
}
?>