<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */

require_once('Date.php');

/**
 * A pop-up calendar widget.
 */
class SwatCalendar extends SwatControl {

	var $valid_range_start;
	var $valid_range_end;
	
	public function display() {
		echo '<script type="text/javascript">';
		include_once('Swat/javascript/swat-find-index.js');
		include_once('Swat/javascript/swat-calendar.js');
		echo '</script>';
		
		$date = new Date();
		$date->setDay(1);
		$date->setMonth(1);
		$date->setYear(1995);
		
		$weeks = array();
		for ($i = 1; $i < 8; $i++) {
			$weeks[] = $date->format('%a');
			$date->setDay($i + 1);
		}
		$weeks = "['".implode("','", $weeks)."']";
		
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
			
		if (isset($this->valid_range_end))
			$end_date = $this->valid_range_end->format("%m/%d/%Y");
		else 
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
		echo '<br /><div id="'.$this->name.'Div" class="swat-calendar-div-hide"></div>';

	}
}