<?php

require_once('Date.php');

/**
 * Pop-up calendar widget.
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatCalendar extends SwatControl {

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
	
	
	public function display() {
		echo '<script type="text/javascript">';
		include_once('Swat/javascript/swat-find-index.js');
		include_once('Swat/javascript/swat-calendar.js');
		echo '</script>';
		
		$date = new Date();
		$date->setDay(1);
		$date->setMonth(1);
		$date->setYear(1995);
		
		//set the names of weeks (locale-specific)
		$weeks = array();
		for ($i = 1; $i < 8; $i++) {
			$weeks[] = $date->format('%a');
			$date->setDay($i + 1);
		}
		$weeks = "['".implode("','", $weeks)."']";
		
		//set the names of months (locale-specific)
		$months = array();
		for ($i = 1; $i < 13; $i++) {
			$months[] = $date->format('%b');
			$date->setMonth($i + 1);
		}
		$months = "['".implode("','", $months)."']";
		
		$close  = _S("Close");
		$nodate = _S("No Date");
		$today  = _S("Today");
		
		if (isset($this->valid_range_start))
			$start_date = $this->valid_range_start->format("%m/%d/%Y");
		else 
			$start_date = '';
			
		if (isset($this->valid_range_end)) {
			//javascript calendar is inclusive, subtract one second from range
			$tmp = clone $this->valid_range_end;
	        $tmp->subtractSeconds(1);
			$end_date = $tmp->format("%m/%d/%Y");
		} else 
			$end_date = '';

		 
		echo '<script type="text/javascript">';
			echo "createCalendarWidget('$this->name', $months, $weeks, '$close',
					'$nodate', '$today');";
		echo '</script>';
		
		echo '<img src="swat/images/b_calendar.gif"
				class="swat-calendar-icon"
				id="'.$this->name.'_calendar"';
		echo '	onmousedown="';
		echo " clickWidgetIcon('$this->name','$start_date','$end_date');";
		echo '" />';
		echo '<br />';
		echo '<div id="'.$this->name.'Div" class="swat-calendar-div-hide">';
		echo '</div>';

	}
}
