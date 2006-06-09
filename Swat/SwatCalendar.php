<?php

require_once 'Swat/SwatControl.php';
require_once 'Date.php';

/**
 * Pop-up calendar widget
 *
 * This widget uses JavaScript to display a popup date selector. It is used
 * inside the {@link SwatDateEntry} widget but can be used by itself as well.
 *
 * @package   Swat
 * @copyright 2004-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatCalendar extends SwatControl
{
	/**
	 * Id of the {@link SwatDateEntry} this calendar corresponds to
	 *
	 * @var string
	 */
	public $entry_id = '';

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
	 * Creates a new calendar
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->requires_id = true;

		$this->addJavaScript('swat/javascript/swat-calendar.js');
		$this->addJavaScript('swat/javascript/swat-z-index-manager.js');
		$this->addStyleSheet('swat/styles/swat-calendar.css');
	}

	/**
	 * Displays this calendar widget
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$anchor_tag = new SwatHtmlTag('a');
		$anchor_tag->href = "javascript:{$this->id}_obj.toggle();";
		$anchor_tag->title = Swat::_('toggle calendar');
		$anchor_tag->open();

		$img_tag = new SwatHtmlTag('img');
		$img_tag->src = 'swat/images/calendar.png';
		$img_tag->alt = Swat::_('calendar toggle graphic');
		$img_tag->class = 'swat-calendar-icon';
		$img_tag->id = $this->id.'_toggle';
		$img_tag->display();

		$anchor_tag->close();

		echo '<br />';

		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id.'_div';
		$div_tag->class = 'swat-calendar-div-hide';
		$div_tag->setContent('&nbsp;');
		$div_tag->display();

		$this->displayJavaScript();
	}

	/**
	 * Displays calendar JavaScript
	 *
	 * The JavaScript is the majority of the calendar code
	 */
	private function displayJavaScript()
	{
		static $shown = false;

		if (!$shown) {
			$this->displayJavaScriptTranslations();

			$shown = true;
		}

		$swat_date_entry = (strlen($this->entry_id) != 0) ?
			$this->entry_id : 'null';

		if (isset($this->valid_range_start))
			$start_date = $this->valid_range_start->format('%m/%d/%Y');
		else 
			$start_date = '';

		if (isset($this->valid_range_end)) {
			// JavaScript calendar is inclusive, subtract one second from range
			$tmp = clone $this->valid_range_end;
			$tmp->subtractSeconds(1);
			$end_date = $tmp->format('%m/%d/%Y');
		} else { 
			$end_date = '';
		}

		echo '<script type="text/javascript">'."\n";

		echo "{$this->id}_obj = new SwatCalendar(".
			"'{$this->id}', ".
			"'{$start_date}', '{$end_date}', {$swat_date_entry});";

		echo "\n</script>";
	}

	/**
	 * Displays translatable string resources for the JavaScript object for
	 * this widget
	 */
	private function displayJavaScriptTranslations()
	{
		/*
		 * This date is arbitrary and is just used for getting week and
		 * month names.
		 */
		$date = new Date();
		$date->setDay(1);
		$date->setMonth(1);
		$date->setYear(1995);

		// Get the names of weeks (locale-specific)
		$week_names = array();
		for ($i = 1; $i < 8; $i++) {
			$week_names[] = $date->format('%a');
			$date->setDay($i + 1);
		}
		$week_names = "['".implode("', '", $week_names)."']";

		// Get the names of months (locale-specific)
		$month_names = array();
		for ($i = 1; $i < 13; $i++) {
			$month_names[] = $date->format('%b');
			$date->setMonth($i + 1);
		}
		$month_names = "['".implode("', '", $month_names)."']";

		$prev_alt_text = Swat::_('Previous Month');
		$next_alt_text = Swat::_('Next Month');
		$close_text    = Swat::_('Close');
		$nodate_text   = Swat::_('No Date');
		$today_text    = Swat::_('Today');

		echo '<script type="text/javascript">',
			"SwatCalendar.week_names = {$week_names};\n",
			"SwatCalendar.month_names = {$month_names};\n",
			"SwatCalendar.prev_alt_text = '{$prev_alt_text}';\n",
			"SwatCalendar.next_alt_text = '{$next_alt_text}';\n",
			"SwatCalendar.close_text = '{$close_text}';\n",
			"SwatCalendar.nodate_text = '{$nodate_text}';\n",
			"SwatCalendar.today_text = '{$today_text}';\n",
			'</script>';
	}
}

?>
