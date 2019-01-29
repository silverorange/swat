<?php

/**
 * A date entry widget
 *
 * @package   Swat
 * @copyright 2004-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDateEntry extends SwatInputControl implements SwatState
{
	// {{{ class constants

	const YEAR = 1;
	const MONTH = 2;
	const DAY = 4;
	const TIME = 8;
	const CALENDAR = 16;

	// }}}
	// {{{ public properties

	/**
	 * Date of this date entry widget
	 *
	 * @var SwatDate
	 */
	public $value = null;

	/**
	 * Required date parts
	 *
	 * Bitwise combination of {@link SwatDateEntry::YEAR},
	 * {@link SwatDateEntry::MONTH}, {@link SwatDateEntry::DAY} and
	 * {@link SwatDateEntry::TIME}.
	 *
	 * For example, to require the month and day to be entered in a date
	 * selector widget use the following:
	 *
	 * <code>
	 * $date->required_parts = SwatDateEntry::MONTH | SwatDateEntry::DAY;
	 * </code>
	 *
	 * @var integer
	 */
	public $required_parts;

	/**
	 * Displayed date parts
	 *
	 * Bitwise combination of {@link SwatDateEntry::YEAR},
	 * {@link SwatDateEntry::MONTH}, {@link SwatDateEntry::DAY},
	 * {@link SwatDateEntry::TIME} and {@link SwatDateEntry::CALENDAR}.
	 *
	 * For example, to show a date selector widget with just the month and year
	 * use the following:
	 *
	 * <code>
	 * $date->display_parts = SwatDateEntry::YEAR | SwatDateEntry::MONTH;
	 * </code>
	 *
	 * @var integer
	 */
	public $display_parts;

	/**
	 * Start date of the valid range (inclusive)
	 *
	 * Defaults to 20 years in the past.
	 *
	 * @var SwatDate
	 */
	public $valid_range_start;

	/**
	 * End date of the valid range (exclusive)
	 *
	 * Defaults to 20 years in the future.
	 *
	 * @var SwatDate
	 */
	public $valid_range_end;

	/**
	 * Whether the numeric month code is displayed in the month flydown
	 *
	 * This is useful for credit card date entry
	 *
	 * @var boolean
	 */
	public $show_month_number = false;

	/**
	 * Whether or not this time entry should auto-complete to the current date
	 *
	 * @var boolean
	 */
	public $use_current_date = true;

	/**
	 * Whether or not the displayed parts should show their blank titles.
	 *
	 * @var boolean
	 */
	public $show_blank_titles = true;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new date entry widget
	 *
	 * Sets default required and display parts and sets default valid range
	 * for this date entry.
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->required_parts = self::YEAR | self::MONTH | self::DAY;
		$this->display_parts =
			self::YEAR | self::MONTH | self::DAY | self::CALENDAR;

		$this->setValidRange(-20, 20);

		$this->requires_id = true;

		$yui = new SwatYUI(array('event'));
		$this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());
		$this->addJavaScript('packages/swat/javascript/swat-date-entry.js');
	}

	// }}}
	// {{{ public function __clone()

	/**
	 * Clones the valid date range of this date entry
	 */
	public function __clone()
	{
		$this->valid_range_start = clone $this->valid_range_start;
		$this->valid_range_end = clone $this->valid_range_end;
	}

	// }}}
	// {{{ public function setValidRange()

	/**
	 * Set the valid date range
	 *
	 * Convenience method to set the valid date range by year offsets.
	 *
	 * @param integer $start_offset offset from the current year in years used
	 *                               to set the starting year of the valid
	 *                               range.
	 * @param integer $end_offset offset from the current year in years used
	 *                             to set the ending year of the valid range.
	 */
	public function setValidRange($start_offset, $end_offset)
	{
		// Beginning of this year
		$date = new SwatDate();
		$date->setMonth(1);
		$date->setDay(1);
		$date->setHour(0);
		$date->setMinute(0);
		$date->setSecond(0);
		$date->setTZById('UTC');

		$this->valid_range_start = clone $date;
		$this->valid_range_end = clone $date;

		$year = $date->getYear();
		$this->valid_range_start->setYear($year + $start_offset);
		$this->valid_range_end->setYear($year + $end_offset + 1);
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this date entry
	 *
	 * Creates internal widgets if they do not exits then displays required
	 * JavaScript, then displays internal widgets.
	 */
	public function display()
	{
		if (!$this->visible) {
			return;
		}

		parent::display();

		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id;
		$div_tag->class = $this->getCSSClassString();
		$div_tag->open();

		echo '<span class="swat-date-entry-span">';

		foreach ($this->getDatePartOrder() as $datepart) {
			if ($datepart == 'd' && $this->display_parts & self::DAY) {
				$day_flydown = $this->getCompositeWidget('day_flydown');

				if ($day_flydown->value === null && $this->value !== null) {
					$day_flydown->value = $this->value->getDay();
				}

				$day_flydown->display();
			} elseif ($datepart == 'm' && $this->display_parts & self::MONTH) {
				$month_flydown = $this->getCompositeWidget('month_flydown');

				if ($month_flydown->value === null && $this->value !== null) {
					$month_flydown->value = $this->value->getMonth();
				}

				$month_flydown->display();
			} elseif ($datepart == 'y' && $this->display_parts & self::YEAR) {
				$year_flydown = $this->getCompositeWidget('year_flydown');

				if ($year_flydown->value === null && $this->value !== null) {
					$year_flydown->value = $this->value->getYear();
				}

				$year_flydown->display();
			}
		}

		echo '</span>';

		if ($this->display_parts & self::CALENDAR) {
			$calendar = $this->getCompositeWidget('calendar');
			$calendar->display();
		}

		if ($this->display_parts & self::TIME) {
			$time_entry = $this->getCompositeWidget('time_entry');

			// if we aren't using the current date then we won't use the
			// current time
			if (!$this->use_current_date) {
				$time_entry->use_current_time = false;
			}

			echo ' ';
			if ($time_entry->value === null && $this->value !== null) {
				$time_entry->value = $this->value;
			}

			$time_entry->display();
		}

		Swat::displayInlineJavaScript($this->getInlineJavaScript());

		$div_tag->close();
	}

	// }}}
	// {{{ public function process()

	/**
	 * Processes this date entry
	 *
	 * Creates internal widgets if they do not exist and then assigns their
	 * values based on the date entered by the user. If the date is not valid,
	 * an error message is attached to this date entry.
	 */
	public function process()
	{
		parent::process();

		if (!$this->isVisible()) {
			return;
		}

		$year = 0;
		$month = 1;
		$day = 1;
		$hour = 0;
		$minute = 0;
		$second = 0;

		$all_empty = true;
		$any_empty = false;

		if ($this->display_parts & self::YEAR) {
			$year_flydown = $this->getCompositeWidget('year_flydown');
			$year = $year_flydown->value;
			if ($year === null) {
				if ($this->required_parts & self::YEAR) {
					$any_empty = true;
				} else {
					$year = date('Y');
				}
			} else {
				$all_empty = false;
			}
		} else {
			$year = date('Y');
		}

		if ($this->display_parts & self::MONTH) {
			$month_flydown = $this->getCompositeWidget('month_flydown');
			$month = $month_flydown->value;
			if ($month === null) {
				if ($this->required_parts & self::MONTH) {
					$any_empty = true;
				} else {
					$month = 1;
				}
			} else {
				$all_empty = false;
			}
		}

		if ($this->display_parts & self::DAY) {
			$day_flydown = $this->getCompositeWidget('day_flydown');
			$day = $day_flydown->value;
			if ($day === null) {
				if ($this->required_parts & self::DAY) {
					$any_empty = true;
				} else {
					$day = 1;
				}
			} else {
				$all_empty = false;
			}
		}

		if ($this->display_parts & self::TIME) {
			$time_entry = $this->getCompositeWidget('time_entry');
			if ($time_entry->value === null) {
				if ($this->required_parts & self::TIME) {
					$any_empty = true;
				} else {
					$hour = 0;
					$minute = 0;
					$second = 0;
				}
			} else {
				$hour = $time_entry->value->getHour();
				$minute = $time_entry->value->getMinute();
				$second = $time_entry->value->getSecond();
				$all_empty = false;
			}
		}

		if ($all_empty) {
			if ($this->required && $this->isSensitive()) {
				if ($this->show_field_title_in_messages) {
					$message = Swat::_('%s is required.');
				} else {
					$message = Swat::_('This field is required.');
				}
				$this->addMessage(new SwatMessage($message, 'error'));
			}
			$this->value = null;
		} elseif ($any_empty) {
			if ($this->show_field_title_in_messages) {
				$message = Swat::_('%s is not a valid date.');
			} else {
				$message = Swat::_('This is not a valid date.');
			}
			$this->addMessage(new SwatMessage($message, 'error'));
			$this->value = null;
		} else {
			try {
				$date = new SwatDate();
				if ($date->setDate($year, $month, $day) === false) {
					throw new SwatException('Invalid date.');
				}

				if ($date->setTime($hour, $minute, $second) === false) {
					throw new SwatException('Invalid date.');
				}

				$date->setTZById('UTC');

				$this->value = $date;
				$this->validateRanges();
			} catch (SwatException $e) {
				if ($this->show_field_title_in_messages) {
					$message = Swat::_('%s is not a valid date.');
				} else {
					$message = Swat::_('This is not a valid date.');
				}
				$this->addMessage(new SwatMessage($message, 'error'));
				$this->value = null;
			}
		}
	}

	// }}}
	// {{{ public function getState()

	/**
	 * Gets the current state of this date entry widget
	 *
	 * @return boolean the current state of this date entry widget.
	 *
	 * @see SwatState::getState()
	 */
	public function getState()
	{
		if ($this->value === null) {
			return null;
		} else {
			return $this->value->getDate();
		}
	}

	// }}}
	// {{{ public function setState()

	/**
	 * Sets the current state of this date entry widget
	 *
	 * @param boolean $state the new state of this date entry widget.
	 *
	 * @see SwatState::setState()
	 */
	public function setState($state)
	{
		$this->value = new SwatDate($state);
	}

	// }}}
	// {{{ public function isValid()

	/**
	 * Checks if the entered date is within the valid range
	 *
	 * @return boolean true if the entered date is within the valid range and
	 *                  false if the entered date is not within the valid range.
	 */
	public function isValid()
	{
		return $this->isStartDateValid() && $this->isEndDateValid();
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
		$use_current_date = $this->use_current_date ? 'true' : 'false';

		$javascript = sprintf(
			"var %s_obj = new SwatDateEntry('%s', %s);",
			$this->id,
			$this->id,
			$use_current_date
		);

		if ($this->display_parts & self::DAY) {
			$day_flydown = $this->getCompositeWidget('day_flydown');

			$lookup_days = array();
			foreach ($day_flydown->options as $key => $option) {
				$lookup_days[] = sprintf(
					'%s: %s',
					$option->value,
					$day_flydown->show_blank ? $key + 1 : $key
				);
			}

			$javascript .= sprintf(
				"\n%s_obj.addLookupTable('day', {%s});",
				$this->id,
				implode(', ', $lookup_days)
			);
		}

		if ($this->display_parts & self::MONTH) {
			$month_flydown = $this->getCompositeWidget('month_flydown');

			$lookup_months = array();
			foreach ($month_flydown->options as $key => $option) {
				$lookup_months[] = sprintf(
					'%s: %s',
					$option->value,
					$month_flydown->show_blank ? $key + 1 : $key
				);
			}

			$javascript .= sprintf(
				"\n%s_obj.addLookupTable('month', {%s});",
				$this->id,
				implode(', ', $lookup_months)
			);
		}

		if ($this->display_parts & self::YEAR) {
			$year_flydown = $this->getCompositeWidget('year_flydown');

			$lookup_years = array();
			foreach ($year_flydown->options as $key => $option) {
				$lookup_years[] = sprintf(
					'%s: %s',
					$option->value,
					$year_flydown->show_blank ? $key + 1 : $key
				);
			}

			$javascript .= sprintf(
				"\n%s_obj.addLookupTable('year', {%s});",
				$this->id,
				implode(', ', $lookup_years)
			);
		}

		if ($this->display_parts & self::TIME) {
			$javascript .= sprintf(
				"\n%s_obj.setTimeEntry(%s_time_entry_obj);",
				$this->id,
				$this->id
			);
		}

		if ($this->display_parts & self::CALENDAR) {
			$javascript .= sprintf(
				"\n%s_obj.setCalendar(%s_calendar_obj);",
				$this->id,
				$this->id
			);
		}

		return $javascript;
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this date entry widget
	 *
	 * @return array the array of CSS classes that are applied to this date
	 *                entry widget.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-date-entry');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
	// {{{ protected function isStartDateValid()

	/**
	 * Checks if the entered date is valid with respect to the valid start
	 * date
	 *
	 * @return boolean true if the entered date is on or after the valid start
	 *                  date and false if the entered date is before the valid
	 *                  start date.
	 */
	protected function isStartDateValid()
	{
		$this->valid_range_start->setTZById('UTC');
		return SwatDate::compare(
			$this->value,
			$this->valid_range_start,
			true
		) >= 0;
	}

	// }}}
	// {{{ protected function isEndDateValid()

	/**
	 * Checks if the entered date is valid with respect to the valid end date
	 *
	 * @return boolean true if the entered date is before the valid end date
	 *                  and false if the entered date is on or after the valid
	 *                  end date.
	 */
	protected function isEndDateValid()
	{
		$this->valid_range_end->setTZById('UTC');
		return SwatDate::compare($this->value, $this->valid_range_end, true) <
			0;
	}

	// }}}
	// {{{ protected function validateRanges()

	/**
	 * Makes sure the date the user entered is within the valid range
	 *
	 * If the date is not within the valid range, this method attaches an
	 * error message to this date entry.
	 */
	protected function validateRanges()
	{
		if (!$this->isStartDateValid()) {
			$message = sprintf(
				Swat::_(
					'The date you have entered is invalid. ' .
						'It must be on or after %s.'
				),
				$this->getFormattedDate($this->valid_range_start)
			);

			$this->addMessage(new SwatMessage($message, 'error'));
		} elseif (!$this->isEndDateValid()) {
			$message = sprintf(
				Swat::_(
					'The date you have entered is invalid. ' .
						'It must be before %s.'
				),
				$this->getFormattedDate($this->valid_range_end)
			);

			$this->addMessage(new SwatMessage($message, 'error'));
		}
	}

	// }}}
	// {{{ protected function createCompositeWidgets()

	/**
	 * Creates the composite widgets used by this date entry
	 *
	 * @see SwatWidget::createCompositeWidgets()
	 */
	protected function createCompositeWidgets()
	{
		if ($this->display_parts & self::YEAR) {
			$this->addCompositeWidget(
				$this->createYearFlydown(),
				'year_flydown'
			);
		}

		if ($this->display_parts & self::MONTH) {
			$this->addCompositeWidget(
				$this->createMonthFlydown(),
				'month_flydown'
			);
		}

		if ($this->display_parts & self::DAY) {
			$this->addCompositeWidget($this->createDayFlydown(), 'day_flydown');
		}

		if ($this->display_parts & self::TIME) {
			$this->addCompositeWidget($this->createTimeEntry(), 'time_entry');
		}

		if ($this->display_parts & self::CALENDAR) {
			$this->addCompositeWidget($this->createCalendar(), 'calendar');
		}
	}

	// }}}
	// {{{ protected function createYearFlydown()

	/**
	 * Creates the year flydown for this date entry
	 *
	 * @return SwatFlydown the year flydown for this date entry.
	 */
	protected function createYearFlydown()
	{
		$flydown = new SwatFlydown($this->id . '_year');
		$flydown->classes = array('swat-date-entry-year');

		if ($this->show_blank_titles) {
			$flydown->blank_title = $this->getYearBlankValueTitle();
		}

		$start_year = $this->valid_range_start->getYear();

		// Subtract a second from the end date. Since end date is exclusive,
		// this means that if the end date is the first of a year, we'll
		// prevent showing that year in the flydown.
		$range_end = clone $this->valid_range_end;
		$range_end->subtractSeconds(1);
		$end_year = $range_end->getYear();

		for ($i = $start_year; $i <= $end_year; $i++) {
			$flydown->addOption($i, $i);
		}

		return $flydown;
	}

	// }}}
	// {{{ protected function getYearBlankValueTitle()

	/**
	 * Gets the blank value to use for the year flydown option
	 *
	 * @return string the blank value to use for the year flydown.
	 */
	protected function getYearBlankValueTitle()
	{
		return Swat::_('Year');
	}

	// }}}
	// {{{ protected function createMonthFlydown()

	/**
	 * Creates the month flydown for this date entry
	 *
	 * @return SwatFlydown the month flydown for this date entry.
	 */
	protected function createMonthFlydown()
	{
		$flydown = new SwatFlydown($this->id . '_month');
		$flydown->classes = array('swat-date-entry-month');

		if ($this->show_blank_titles) {
			$flydown->blank_title = $this->getMonthBlankValueTitle();
		}

		$range_end = clone $this->valid_range_end;
		$difference = $this->valid_range_start->diff($range_end);

		// Subtract a second from the end date. This makes comparison correct,
		// and prevents displaying extra months. The difference needs to happen
		// before this to prevent December specific bugs.
		$range_end->subtractSeconds(1);

		$start_year = $this->valid_range_start->getYear();
		$end_year = $range_end->getYear();
		$start_month = $this->valid_range_start->getMonth();
		$end_month = $range_end->getMonth();

		if ($end_year == $start_year) {
			// if the years match, only display the valid months.
			for ($i = $start_month; $i <= $end_month; $i++) {
				$flydown->addOption($i, $this->getMonthOptionText($i));
			}
		} else {
			for ($i = 1; $i <= 12; $i++) {
				$flydown->addOption($i, $this->getMonthOptionText($i));
			}
		}

		return $flydown;
	}

	// }}}
	// {{{ protected function getMonthBlankValueTitle()

	/**
	 * Gets the blank value to use for the month flydown option
	 *
	 * @return string the blank value to use for the month flydown.
	 */
	protected function getMonthBlankValueTitle()
	{
		return Swat::_('Month');
	}

	// }}}
	// {{{ protected function getMonthOptionText()

	/**
	 * Gets the title of a month flydown option
	 *
	 * @param integer $month the numeric identifier of the month.
	 *
	 * @return string the option text of the month.
	 */
	protected function getMonthOptionText($month)
	{
		$text = '';

		if ($this->show_month_number) {
			$text .= str_pad($month, 2, '0', STR_PAD_LEFT) . ' - ';
		}

		$date = new SwatDate('2010-' . $month . '-01');

		$text .= $date->formatLikeIntl('MMMM');

		return $text;
	}

	// }}}
	// {{{ protected function createDayFlydown()

	/**
	 * Creates the day flydown for this date entry
	 *
	 * @return SwatFlydown the day flydown for this date entry.
	 */
	protected function createDayFlydown()
	{
		$flydown = new SwatFlydown($this->id . '_day');
		$flydown->classes = array('swat-date-entry-day');

		if ($this->show_blank_titles) {
			$flydown->blank_title = $this->getDayBlankValueTitle();
		}

		// Subtract a second from the end date. This makes comparison correct,
		// and prevents displaying extra days.
		$range_end = clone $this->valid_range_end;
		$range_end->subtractSeconds(1);

		$start_year = $this->valid_range_start->getYear();
		$end_year = $range_end->getYear();
		$start_month = $this->valid_range_start->getMonth();
		$end_month = $range_end->getMonth();
		$start_day = $this->valid_range_start->getDay();
		$end_day = $range_end->getDay();

		$end_check = clone $this->valid_range_start;
		$end_check->addSeconds(2678400); // add 31 days

		if ($start_year == $end_year && $start_month == $end_month) {
			// Only days left in the month
			for ($i = $start_day; $i <= $end_day; $i++) {
				$flydown->addOption($i, $i);
			}
		} elseif (SwatDate::compare($end_check, $range_end, true) != -1) {
			// extra days at the beginning of the next month allowed
			$days_in_month = $this->valid_range_start->getDaysInMonth();

			for ($i = $start_day; $i <= $days_in_month; $i++) {
				$flydown->addOption($i, $i);
			}

			for ($i = 1; $i <= $end_day; $i++) {
				$flydown->addOption($i, $i);
			}
		} else {
			// all days are valid
			for ($i = 1; $i <= 31; $i++) {
				$flydown->addOption($i, $i);
			}
		}

		return $flydown;
	}

	// }}}
	// {{{ protected function getDayBlankValueTitle()

	/**
	 * Gets the blank value to use for the day flydown option
	 *
	 * @return string the blank value to use for the day flydown.
	 */
	protected function getDayBlankValueTitle()
	{
		return Swat::_('Day');
	}

	// }}}
	// {{{ protected function createTimeEntry()

	/**
	 * Creates the time entry widget for this date entry
	 *
	 * @return SwatTimeEntry the time entry widget for this date entry.
	 */
	protected function createTimeEntry()
	{
		$time_entry = new SwatTimeEntry($this->id . '_time_entry');
		$time_entry->classes = array('swat-date-entry-time');
		return $time_entry;
	}

	// }}}
	// {{{ protected function createCalendar()

	/**
	 * Creates the calendar widget for this date entry
	 *
	 * @return SwatCalendar the calendar widget for this date entry.
	 */
	protected function createCalendar()
	{
		$calendar = new SwatCalendar($this->id . '_calendar');
		$calendar->classes = array('swat-date-entry-calendar');
		$calendar->valid_range_start = $this->valid_range_start;
		$calendar->valid_range_end = $this->valid_range_end;
		return $calendar;
	}

	// }}}
	// {{{ private function getFormattedDate()

	/**
	 * Formats a date for this date entry
	 *
	 * Returns a date string formatted according to the properties of this
	 * date entry widget. This is used primarily for returning formatted
	 * valid start and valid end dates for user error messages.
	 *
	 * @param SwatDate $date the date object to format.
	 *
	 * @return string a date formatted according to the properties of this date
	 *                 entry.
	 */
	private function getFormattedDate(SwatDate $date)
	{
		// note: the display of the date is not locale specific as this
		// is quite difficult without a good i18n/l10n library

		$format = '';

		if ($this->display_parts & self::MONTH) {
			$format .= ' MMMM';
		}

		if ($this->display_parts & self::DAY) {
			$format .= ' d,';
		}

		if ($this->display_parts & self::YEAR) {
			$format .= ' yyyy';
		}

		if ($this->display_parts & self::TIME) {
			$format .= ' h:mm a';
		}

		$format = trim($format, ', ');

		return $date->formatLikeIntl($format);
	}

	// }}}
	// {{{ private function getDatePartOrder()

	/**
	 * Gets the order of date parts for the current locale
	 *
	 * Note: The technique used within this method does not work correcty for
	 * RTL languages that display month names, month abbreviations or weekday
	 * names. Since we're displaying months textually these locales may have
	 * date parts incorrectly ordered.
	 *
	 * @return array an array containg the values 'd', 'm' and 'y' in the
	 *                correct order for the current locale.
	 */
	private function getDatePartOrder()
	{
		$format = nl_langinfo(D_FMT);

		// expand short form format
		$format = str_replace('%D', '%m/%d/%y', $format);

		$day = null;
		$month = null;
		$year = null;

		$matches = array();
		if (
			preg_match('/(%d|%e)/', $format, $matches, PREG_OFFSET_CAPTURE) == 1
		) {
			$day = $matches[0][1];
		}

		$matches = array();
		if (
			preg_match(
				'/(%[bB]|%m)/',
				$format,
				$matches,
				PREG_OFFSET_CAPTURE
			) == 1
		) {
			$month = $matches[0][1];
		}

		$matches = array();
		if (
			preg_match('/(%[Yy])/', $format, $matches, PREG_OFFSET_CAPTURE) == 1
		) {
			$year = $matches[0][1];
		}

		if ($day === null || $month === null || $year === null) {
			// fallback to d-m-y if the locale format is unknown
			$order = array('d', 'm', 'y');
		} else {
			$order = array();
			$order[$day] = 'd';
			$order[$month] = 'm';
			$order[$year] = 'y';
			ksort($order);
		}

		return $order;
	}

	// }}}
}
