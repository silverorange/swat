<?php

require_once 'Date.php';

/**
 * Pop-up calendar widget.
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatCalendar extends SwatControl
{
	/**
	 * Id of the {@link SwatDateEntry} the Calendar corresponds to
	 *
	 * @var string
	 */
	public $entry_id;

	/**
	 * Start date of the valid range (inclusive).
	 *
	 * @var Date
	 */
	public $valid_range_start;
	
	/**
	 * End date of the valid range (exclusive).
	 *
	 * @var Date
	 */
	public $valid_range_end;

	/**
	 * Displays this calendar widget
	 */
	public function display()
	{
		$this->displayJavascript();
		
		/*
		 * This date is arbitrary and is just used for getting week and month
		 * names. We may want to add a method to Date() to get week and month
		 * names.
		 */
		$date = new Date();
		$date->setDay(1);
		$date->setMonth(1);
		$date->setYear(1995);
		
		// Get the names of weeks (locale-specific)
		$weeks = array();
		for ($i = 1; $i < 8; $i++) {
			$weeks[] = $date->format('%a');
			$date->setDay($i + 1);
		}
		$weeks = "['".implode("','", $weeks)."']";
		
		// Get the names of months (locale-specific)
		$months = array();
		for ($i = 1; $i < 13; $i++) {
			$months[] = $date->format('%b');
			$date->setMonth($i + 1);
		}
		$months = "['".implode("','", $months)."']";
		
		$close  = Swat::_('Close');
		$nodate = Swat::_('No Date');
		$today  = Swat::_('Today');
		
		if (isset($this->valid_range_start))
			$start_date = $this->valid_range_start->format("%m/%d/%Y");
		else 
			$start_date = '';
			
		if (isset($this->valid_range_end)) {
			// Javascript calendar is inclusive, subtract one second from range
			$tmp = clone $this->valid_range_end;
	        $tmp->subtractSeconds(1);
			$end_date = $tmp->format("%m/%d/%Y");
		} else { 
			$end_date = '';
		}
		 
		echo '<script type="text/javascript">';
		
		echo "createCalendarWidget('{$this->entry_id}', {$months}, {$weeks}, ",
			"'{$close}', '$nodate', '$today');";
			
		echo '</script>';
		
		$img_tag = new SwatHtmlTag('img');
		$img_tag->src = 'swat/images/b_calendar.gif';
		$img_tag->class = 'swat-calendar-icon';
		$img_tag->id = $this->entry_id.'_calendar';
		$img_tag->onmousedown = "clickWidgetIcon('{$this->entry_id}', ".
			"'{$start_date}', '{$end_date}');";

		$img_tag->display();
		
		echo '<br />';

		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->entry_id.'_div';
		$div_tag->class = 'swat-calendar-div-hide';
		// TODO: try display() here
		$div_tag->open();
		$div_tag->close();
	}

	/**
	 * Displays calendar javascript
	 *
	 * The javascript is the majority of the calendar code
	 */
	private function displayJavascript()
	{
		echo '<script type="text/javascript">';
		echo '//<![CDATA[';
		include_once 'Swat/javascript/swat-find-index.js';
		include_once 'Swat/javascript/swat-calendar.js';
		echo '//]]>';
		echo '</script>';
	}
}

?>
