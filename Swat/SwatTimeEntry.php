<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatInputControl.php';
require_once 'Swat/SwatFlydown.php';
require_once 'Swat/SwatDate.php';
require_once 'Swat/SwatState.php';
require_once 'Swat/SwatYUI.php';

/**
 * A time entry widget
 *
 * @package   Swat
 * @copyright 2004-2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @todo      Should we add a display_time_zone paramater?
 */
class SwatTimeEntry extends SwatInputControl implements SwatState
{
	// {{{ constants

	const HOUR   = 1;
	const MINUTE = 2;
	const SECOND = 4;

	// }}}
	// {{{ public properties

	/**
	 * Time of this time entry widget
	 *
	 * The year, month and day fields of the Date object are unused and
	 * undefined. If the state of this time entry does not represent a valid
	 * time, the value will be null.
	 *
	 * @var Date
	 */
	public $value = null;

	/**
	 * Required time parts
	 *
	 * Bitwise combination of {@link SwatTimeEntry::HOUR},
	 * {@link SwatTimeEntry::MINUTE} and {@link SwatTimeEntry::SECOND}.
	 *
	 * For example, to require the minute and second to be entered in a time
	 * selector widget use the following:
	 *
	 * <code>
	 * $time->required_parts = SwatTimeEntry::MINUTE | SwatTimeEntry::SECOND;
	 * </code>
	 *
	 * @var integer
	 */
	public $required_parts;

	/**
	 * Displayed time parts
	 *
	 * Bitwise combination of {@link SwatTimeEntry::HOUR},
	 * {@link SwatTimeEntry::MINUTE} and {@link SwatTimeEntry::SECOND}.
	 *
	 * For example, to show a time selector widget with just the hour and
	 * minute use the following:
	 *
	 * <code>
	 * $time->display_parts = SwatTimeEntry::HOUR | SwatDateEntry::MINUTE;
	 * </code>
	 *
	 * @var integer
	 */
	public $display_parts;

	/**
	 * Start time of the valid range (inclusive)
	 *
	 * Defaults to 00:00:00. The year, month and day fields of the Date object
	 * are ignored and undefined. This value is inclusive.
	 *
	 * @var Date
	 */
	public $valid_range_start;

	/**
	 * End time of the valid range (inclusive)
	 *
	 * Defaults to 23:59:59. The year, month and day fields of the Date object
	 * are ignored and undefined. This value is inclusive.
	 *
	 * @var Date
	 */
	public $valid_range_end;

	/**
	 * Whether or not times are entered and displayed in 12-hour format
	 *
	 * If not specified, defaults to the default format of the current locale.
	 *
	 * @var boolean
	 */
	public $twelve_hour;

	// }}}
	// {{{ private properties

	/**
	 * A reference to the internal hour flydown
	 *
	 * @var SwatFlydown
	 */
	private $hour_flydown;

	/**
	 * A reference to the internal minute flydown
	 *
	 * @var SwatFlydown
	 */
	private $minute_flydown;

	/**
	 * A reference to the internal second flydown
	 *
	 * @var SwatFlydown
	 */
	private $second_flydown;

	/**
	 * A reference to the internal am/pm flydown
	 *
	 * @var SwatFlydown
	 */
	private $am_pm_flydown;

	/**
	 * An internal flag telling whether internal widgets have been
	 * created or not
	 *
	 * @var boolean
	 */
	private $widgets_created = false;

	/**
	 * Default year value used for time value
	 *
	 * Defined here so internal time comparisons all happen on the same day.
	 *
	 * @var integer
	 */
	private static $date_year = 2000;

	/**
	 * Default month value used for time value
	 *
	 * Defined here so internal time comparisons all happen on the same day.
	 *
	 * @var integer
	 */
	private static $date_month = 1;

	/**
	 * Default day value used for time value
	 *
	 * Defined here so internal time comparisons all happen on the same day.
	 *
	 * @var integer
	 */
	private static $date_day = 1;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new time entry widget
	 *
	 * Sets default required and display parts and sets default valid range
	 * for this time entry.
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->display_parts = self::HOUR | self::MINUTE;
		$this->required_parts = $this->display_parts;

		$this->valid_range_start = new SwatDate('2000-01-01T00:00:00.0000Z');
		$this->valid_range_end   = new SwatDate('2000-01-01T23:59:59.0000Z');

		$this->requires_id = true;

		$yui = new SwatYUI(array('event'));
		$this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());
		$this->addJavaScript('packages/swat/javascript/swat-time-entry.js',
			Swat::PACKAGE_ID);

		// guess twelve-hour or twenty-four hour default based on locale
		$locale_format = nl_langinfo(T_FMT);
		$this->twelve_hour = 
			(preg_match('/(%T|%R|%k|.*%H.*)/', $locale_format) == 1);
	}

	// }}}
	// {{{ public function __clone()

	/**
	 * Clones the embedded widgets of this time entry widget
	 */
	public function __clone()
	{
		$this->valid_range_start = clone $this->valid_range_start;
		$this->valid_range_end = clone $this->valid_range_end;

		if ($this->widgets_created) {
			if ($this->display_parts & self::HOUR) {
				$this->hour_flydown = clone $this->hour_flydown;
				if ($this->twelve_hour)
					$this->am_pm_flydown = clone $this->am_pm_flydown;
			}

			if ($this->display_parts & self::MINUTE)
				$this->minute_flydown = clone $this->minute_flydown;

			if ($this->display_parts & self::SECOND)
				$this->second_flydown = clone $this->second_flydown;
		}
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this time entry
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$this->createEmbeddedWidgets();

		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id;
		$div_tag->class = $this->getCSSClassString();
		$div_tag->open();

		echo '<span class="swat-time-entry-span">';

		if ($this->display_parts & self::HOUR) {
			if ($this->hour_flydown->value === null && $this->value !== null) {
				// work around a bug in PEAR::Date that returns hour as a string
				$hour = intval($this->value->getHour());

				// convert 24-hour value to 12-hour display
				if ($this->twelve_hour) {
					if ($hour > 12)
						$hour -= 12;

					if ($hour == 0)
						$hour = 12;
				}

				$this->hour_flydown->value = $hour;
			}

			$this->hour_flydown->display();

			if ($this->display_parts & (self::MINUTE | self::SECOND))
				echo ':';
		}

		if ($this->display_parts & self::MINUTE) {
			if ($this->minute_flydown->value === null &&
				$this->value !== null) {
				// work around a bug in PEAR::Date that returns minutes as a
				// 2-character string
				$minute = intval($this->value->getMinute());
				$this->minute_flydown->value = $minute;
			}

			$this->minute_flydown->display();
			if ($this->display_parts & self::SECOND)
				echo ':';
		}

		if ($this->display_parts & self::SECOND) {
			if ($this->second_flydown->value === null && $this->value !== null)
				$this->second_flydown->value = $this->value->getSecond();

			$this->second_flydown->display();
		}

		if (($this->display_parts & self::HOUR) && $this->twelve_hour) {
			if ($this->am_pm_flydown->value === null && $this->value !== null)
				$this->am_pm_flydown->value =
					($this->value->getHour() < 12) ? 'am' : 'pm';

			$this->am_pm_flydown->display();
		}

		echo '</span>';

		Swat::displayInlineJavaScript($this->getInlineJavaScript());

		$div_tag->close();
	}

	// }}}
	// {{{ public function init()

	/** 
	 * Initialize this time entry
	 *
	 * Sets this time entry to required if any of the
	 * {@link SwatTimeEntry::$required_parts} are set.
	 */
	public function init()
	{
		$this->required = $this->required || ($this->required_parts != 0);

		parent::init();
	}

	// }}}
	// {{{ public function process()

	/**
	 * Processes this time entry
	 *
	 * If the time is not valid an error message is attached to this time
	 * entry.
	 */
	public function process()
	{
		parent::process();

		if (!$this->isVisible())
			return;

		$this->createEmbeddedWidgets();

		$hour   = 0;
		$minute = 0;
		$second = 0;

		$all_empty = true;
		$any_empty = false;

		if ($this->display_parts & self::HOUR) {
			$this->hour_flydown->process();
			$hour = $this->hour_flydown->value;

			if ($hour === null) {
				if ($this->required_parts & self::HOUR) {
					$any_empty = true;
				} else {
					$all_empty = false;
					$hour = 0;
				}
			} else {
				$all_empty = false;
			}

			if ($this->twelve_hour) {
				$this->am_pm_flydown->process();
				$am_pm = $this->am_pm_flydown->value;

				if ($am_pm === null) {
					if ($this->required_parts & self::HOUR) {
						$any_empty = true;
					} else {
						$all_empty = false;
						$am_pm = 'am';
					}
				} else {
					$all_empty = false;
				}

				// convert 12-hour display to 24-hour value
				if ($hour !== null && $am_pm == 'pm') {
					$hour += 12;
					if ($hour == 24)
						$hour = 0;
				}
			}
		}

		if ($this->display_parts & self::MINUTE) {
			$this->minute_flydown->process();
			$minute = $this->minute_flydown->value;

			if ($minute === null) {
				if ($this->required_parts & self::MINUTE) {
					$any_empty = true;
				} else {
					$all_empty = false;
					$minute = 0;
				}
			} else {
				$all_empty = false;
			}
		}

		if ($this->display_parts & self::SECOND) {
			$this->second_flydown->process();
			$second = $this->second_flydown->value;

			if ($second === null) {
				if ($this->required_parts & self::SECOND) {
					$any_empty = true;
				} else {
					$all_empty = false;
					$second = 0;
				}
			} else {
				$all_empty = false;
			}
		}

		if ($all_empty) {
			$message = Swat::_('The %s field is required.');
			$this->addMessage(new SwatMessage($message, SwatMessage::ERROR));
			$this->value = null;
		} elseif ($any_empty) {
			$message = Swat::_('The %s field is not a valid time.');
			$this->addMessage(new SwatMessage($message, SwatMessage::ERROR));
			$this->value = null;
		} else {
			$this->value = new SwatDate();
			$this->value->setYear(self::$date_year);
			$this->value->setMonth(self::$date_month);
			$this->value->setDay(self::$date_day);
			$this->value->setHour($hour);
			$this->value->setMinute($minute);
			$this->value->setSecond($second);
			$this->value->setTZ('UTC');

			$this->validateRanges();
		}
	}

	// }}}
	// {{{ public function getState()

	/**
	 * Gets the current state of this time entry widget
	 *
	 * @return boolean the current state of this time entry widget.
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

	// }}}
	// {{{ public function setState()

	/**
	 * Sets the current state of this time entry widget
	 *
	 * @param boolean $state the new state of this time entry widget.
	 *
	 * @see SwatState::setState()
	 */
	public function setState($state)
	{
		$this->value = new SwatDate($state);
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this time entry widget
	 *
	 * @return array the array of CSS classes that are applied to this time
	 *                entry widget.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-time-entry');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
	// {{{ protected function getInlineJavaScript()

	/**
	 * Gets the inline JavaScript required for this control
	 *
	 * @return string the inline JavaScript required for this control.
	 */
	protected function getInlineJavaScript()
	{
		$javascript = sprintf("var %s_obj = new SwatTimeEntry('%s');\n",
			$this->id, $this->id);

		if ($this->display_parts & self::HOUR) {
			$lookup_hours = array();
			foreach ($this->hour_flydown->options as $key => $option)
				$lookup_hours[] = sprintf('%s: %s',
					$option->value,
					($this->hour_flydown->show_blank) ? $key + 1 : $key);

			$javascript.= sprintf("\n%s_obj.addLookupTable('hour', {%s});",
				$this->id, implode(', ', $lookup_hours));
		}

		if ($this->display_parts & self::MINUTE) {
			$lookup_minutes = array();
			foreach ($this->minute_flydown->options as $key => $option)
				$lookup_minutes[] = sprintf('%s: %s',
					$option->value,
					($this->minute_flydown->show_blank) ? $key + 1 : $key);

			$javascript.= sprintf("\n%s_obj.addLookupTable('minute', {%s});",
				$this->id, implode(', ', $lookup_minutes));
		}

		if ($this->display_parts & self::SECOND) {
			$lookup_seconds = array();
			foreach ($this->second_flydown->options as $key => $option)
				$lookup_seconds[] = sprintf('%s: %s',
					$option->value,
					($this->second_flydown->show_blank) ? $key + 1 : $key);

			$javascript.= sprintf("\n%s_obj.addLookupTable('second', {%s});",
				$this->id, implode(', ', $lookup_seconds));
		}

		return $javascript;
	}

	// }}}
	// {{{ protected function validateRanges()

	/**
	 * Makes sure the date the user entered is within the valid range
	 *
	 * If the time is not within the valid range, this method attaches an
	 * error message to this time entry.
	 */
	protected function validateRanges()
	{
		if (!$this->isStartTimeValid()) {
			$message = sprintf(Swat::_('The time you have entered is invalid. '.
				'It must be on or after %s.'),
				$this->getFormattedTime($this->valid_range_start));

			$this->addMessage(new SwatMessage($message, SwatMessage::ERROR));

		} elseif (!$this->isEndTimeValid()) {
			$message = sprintf(Swat::_('The time you have entered is invalid. '.
				'It must be on or before %s.'),
				$this->getFormattedTime($this->valid_range_end));

			$this->addMessage(new SwatMessage($message, SwatMessage::ERROR));
		}
	}

	// }}}
	// {{{ protected function isStartTimeValid()

	/**
	 * Checks if the entered time is valid with respect to the valid start
	 * time
	 *
	 * @return boolean true if the entered time is on or after the valid start
	 *                  time and false if the entered time is before the valid
	 *                  start time.
	 */
	protected function isStartTimeValid()
	{
		$this->valid_range_start->setYear(self::$date_year);
		$this->valid_range_start->setMonth(self::$date_month);
		$this->valid_range_start->setDay(self::$date_day);
		$this->valid_range_start->setTZ('UTC');

		return (Date::compare(
			$this->value, $this->valid_range_start, true) >= 0);
	}

	// }}}
	// {{{ protected function isEndTimeValid()

	/**
	 * Checks if the entered time is valid with respect to the valid end time
	 *
	 * @return boolean true if the entered time is before the valid end time
	 *                  and false if the entered time is on or after the valid
	 *                  end time.
	 */
	protected function isEndTimeValid()
	{
		$this->valid_range_end->setYear(self::$date_year);
		$this->valid_range_end->setMonth(self::$date_month);
		$this->valid_range_end->setDay(self::$date_day);
		$this->valid_range_end->setTZ('UTC');

		return (Date::compare(
			$this->value, $this->valid_range_end, true) <= 0);
	}

	// }}}
	// {{{ private function createEmbeddedWidgets()

	/**
	 * Creates all internal widgets required for this time entry
	 */
	private function createEmbeddedWidgets()
	{
		if (!$this->widgets_created) {
			if ($this->display_parts & self::HOUR) {
				$this->createHourFlydown();
				if ($this->twelve_hour)
					$this->createAmPmFlydown();
			}

			if ($this->display_parts & self::MINUTE)
				$this->createMinuteFlydown();

			if ($this->display_parts & self::SECOND)
				$this->createSecondFlydown();

			$this->widgets_created = true;
		}
	}

	// }}}
	// {{{ private function createHourFlydown()

	/**
	 * Creates the hour flydown for this time entry
	 */
	private function createHourFlydown()
	{
		$this->hour_flydown = new SwatFlydown($this->id.'_hour');
		$this->hour_flydown->parent = $this;

		if ($this->twelve_hour) {
			for ($i = 1; $i <= 12; $i++)
				$this->hour_flydown->addOption($i, $i);
		} else {
			for ($i = 0; $i < 24; $i++)
				$this->hour_flydown->addOption($i, $i);
		}
	}

	// }}}
	// {{{ private function createMinuteFlydown()

	/**
	 * Creates the minute flydown for this time entry
	 */
	private function createMinuteFlydown()
	{
		$this->minute_flydown = new SwatFlydown($this->id.'_minute');
		$this->minute_flydown->parent = $this;

		for ($i = 0; $i <= 59; $i++)
			$this->minute_flydown->addOption($i,
				str_pad($i, 2, '0', STR_PAD_LEFT));
	}

	// }}}
	// {{{ private function createSecondFlydown()

	/**
	 * Creates the second flydown for this time entry
	 */
	private function createSecondFlydown()
	{
		$this->second_flydown = new SwatFlydown($this->id.'_second');
		$this->second_flydown->parent = $this;

		for ($i = 0; $i <= 59; $i++)
			$this->second_flydown->addOption($i,
				str_pad($i, 2 ,'0', STR_PAD_LEFT));
	}

	// }}}
	// {{{ private function createAmPmFlydown()

	/**
	 * Creates the am/pm flydown for this time entry
	 */
	private function createAmPmFlydown()
	{
		$this->am_pm_flydown = new SwatFlydown($this->id.'_am_pm');
		$this->am_pm_flydown->addOptionsByArray(array(
			'am' => Swat::_('am'),
			'pm' => Swat::_('pm'),
		));

		$this->am_pm_flydown->parent = $this;
	}

	// }}}
	// {{{ private function getFormattedTime()

	/**
	 * Formats a time for display in error messages
	 *
	 * @param Date $time the time to format.
	 *
	 * @return string the formatted time.
	 */
	private function getFormattedTime(Date $time)
	{
		$format = '';

		if ($this->display_parts & self::HOUR) {
			$format.= ($this->twelve_hour) ? '%i' : '%h';
			if ($this->display_parts & (self::MINUTE | self::SECOND))
				$format.= ':';
		}

		if ($this->display_parts & self::MINUTE) {
			$format.= '%M';
			if ($this->display_parts & self::SECOND)
				$format.= ':';
		}

		if ($this->display_parts & self::SECOND) {
			$format.= '%S';
		}

		if (($this->display_parts & self::HOUR) && $this->twelve_hour) {
			$format.= ' %p';
		}

		return $time->format($format);
	}

	// }}}
}

?>
