<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatInputControl.php';
require_once 'Swat/SwatFlydown.php';
require_once 'Swat/SwatDate.php';
require_once 'Swat/SwatState.php';
require_once 'Swat/SwatYUI.php';

// TODO: figure out why the valid-ranges are getting having the time inproperly
//       offset.

/**
 * A time entry widget
 *
 * @package   Swat
 * @copyright 2004-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
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
	 * undefined.
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
	 * are ignored and undefined.
	 *
	 * @var Date
	 */
	public $valid_range_start;

	/**
	 * End time of the valid range (inclusive)
	 *
	 * Defaults to 23:59:59. The year, month and day fields of the Date object
	 * are ignored and undefined.
	 *
	 * @var Date
	 */
	public $valid_range_end;

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
	private $created = false;

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

		$this->display_parts  = self::HOUR | self::MINUTE;
		$this->required_parts = $this->display_parts;

		$this->valid_range_start = new SwatDate('0000-01-01T00:00:00.0000Z');
		$this->valid_range_end   = new SwatDate('0000-01-01T23:59:59.0000Z');

		$yui = new SwatYUI(array('event'));
		$this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());
		$this->addJavaScript('packages/swat/javascript/swat-time-entry.js',
			Swat::PACKAGE_ID);
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this time entry
	 *
	 * Creates internal widgets if they do not exits then displays required
	 * JavaScript, then displays internal widgets.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id;
		$div_tag->class = $this->getCSSClassString();
		$div_tag->open();

		$this->createFlydowns();

		if ($this->display_parts & self::HOUR) {
			if ($this->value !== null)
				$this->hour_flydown->value = (int) $this->value->getHour();

			$this->hour_flydown->display();
			if ($this->display_parts & (self::MINUTE | self::SECOND))
				echo ':';
		}

		if ($this->display_parts & self::MINUTE) {
			if ($this->value !== null)
				$this->minute_flydown->value = (int) $this->value->getMinute();

			$this->minute_flydown->display();
			if ($this->display_parts & self::SECOND)
				echo ':';
		}

		if ($this->display_parts & self::SECOND) {
			if ($this->value !== null)
				$this->second_flydown->value = (int) $this->value->getSecond();

			$this->second_flydown->display();
		}

		if ($this->display_parts & self::HOUR) {
			if ($this->value !== null)
				$this->am_pm_flydown->value = ($this->value->getHour() < 12 ||
					$this->value->getHour() == 0) ?
					'am' : 'pm';

			$this->am_pm_flydown->display();
		}

		$div_tag->close();

		Swat::displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ public function process()

	/**
	 * Processes this time entry
	 *
	 * Creates internal widgets if they do not exist and then assigns their
	 * values based on the time entered by the user. If the time is not valid,
	 * an error message is attached to this time entry.
	 */
	public function process()
	{
		parent::process();

		if (!$this->isVisible())
			return;

		$this->createFlydowns();

		$all_empty = true;

		if ($this->display_parts & self::HOUR) {
			$this->hour_flydown->process();
			$this->am_pm_flydown->process();
			$hour = $this->hour_flydown->value;
			$ampm = $this->am_pm_flydown->value;
			$all_empty = $all_empty && ($hour === null);
			$all_empty = $all_empty && ($ampm === null);

			if ($this->required_parts & self::HOUR && $hour === null) {
				$message = Swat::_('Hour is Required.');
				$this->addMessage(new SwatMessage($message,
					SwatMessage::ERROR));
			}

			if ($this->required_parts & self::HOUR && $ampm === null) {
				$message = Swat::_('AM/PM is Required.');
				$this->addMessage(new SwatMessage($message,
					SwatMessage::ERROR));
			}

			$hour = intval($hour);

			if ($ampm == 'pm') {
				$hour += 12;
				if ($hour == 24)
					$hour = 0;
			}
		} else {
			$hour = 0;
		}

		if ($this->display_parts & self::MINUTE) {
			$this->minute_flydown->process();
			$minute = $this->minute_flydown->value;
			$all_empty = $all_empty && ($minute === null);

			if ($this->required_parts & self::MINUTE && $minute === null) {
				$message = Swat::_('Minute is Required.');
				$this->addMessage(new SwatMessage($message,
					SwatMessage::ERROR));
			}

			$minute = intval($minute);
		} else {
			$minute = 0;
		}

		if ($this->display_parts & self::SECOND) {
			$this->second_flydown->process();
			$second = $this->second_flydown->value;
			$all_empty = $all_empty && ($second === null);

			if ($this->required_parts & self::SECOND && $second === null) {
				$message = Swat::_('Second is Required.');
				$this->addMessage(new SwatMessage($message,
					SwatMessage::ERROR));
			}

			$second = intval($second);
		} else {
			$second = 0;
		}

		if ($this->required && $all_empty) {
			$message = Swat::_('Time is Required.');
			$this->addMessage(new SwatMessage($message, SwatMessage::ERROR));
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
	// {{{ private function createFlydowns()

	/**
	 * Creates all internal widgets required for this date entry
	 */
	private function createFlydowns()
	{
		if ($this->created) return;

		$this->created = true;

		if ($this->display_parts & self::HOUR)
			$this->createHourFlydown();

		if ($this->display_parts & self::MINUTE)
			$this->createMinuteFlydown();

		if ($this->display_parts & self::SECOND)
			$this->createSecondFlydown();

		if ($this->display_parts & self::HOUR)
			$this->createAmPmFlydown();
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

		for ($i = 1; $i <= 12; $i++)
			$this->hour_flydown->addOption($i, $i);
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
			$this->second_flydown->addOptions($i,
				str_pad($i, 2 ,'0', STR_PAD_LEFT));
	}

	// }}}
	// {{{ private function createAmPmFlydown()

	/**
	 * Creates the am/pm flydown for this time entry
	 */
	private function createAmPmFlydown()
	{
		$this->am_pm_flydown = new SwatFlydown($this->id.'_ampm');
		$this->am_pm_flydown->addOptionsByArray(array('am' => 'AM',
			'pm' => 'PM'));

		$this->am_pm_flydown->parent = $this;
	}

	// }}}
	// {{{ private function validateRanges()

	/**
	 * Makes sure the date the user entered is within the valid range
	 *
	 * If the time is not within the valid range, this method attaches an
	 * error message to this time entry.
	 */
	private function validateRanges()
	{
		$this->valid_range_start->setYear(0);
		$this->valid_range_start->setMonth(1);
		$this->valid_range_start->setDay(1);

		$this->valid_range_end->setYear(0);
		$this->valid_range_end->setMonth(1);
		$this->valid_range_end->setDay(1);

		if (Date::compare($this->value, $this->valid_range_start, true) == -1) {

			$message = sprintf(Swat::_('The time you have entered is invalid. '.
				'It must be after %s.'),
				$this->displayTime($this->valid_range_start));

			$this->addMessage(new SwatMessage($message, SwatMessage::ERROR));

		} elseif (Date::compare($this->value, $this->valid_range_end, true) == 1) {

			$message = sprintf(Swat::_('The time you have entered is invalid. '.
				'It must be before %s.'),
				$this->displayTime($this->valid_range_end));

			$this->addMessage(new SwatMessage($message, SwatMessage::ERROR));

		}
	}

	// }}}
	// {{{ private function displayTime()

	/**
	 * Converts a time to a human readable string
	 *
	 * This is a convenience method.
	 *
	 * @param Date $time the time to convert to a string.
	 *
	 * @return string the time converted to a string.
	 */
	private function displayTime($time)
	{
		return $time->format('%r'); // TODO: %X
	}

	// }}}
}

?>
