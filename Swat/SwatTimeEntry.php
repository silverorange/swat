<?php

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatFlydown.php';
require_once 'Swat/SwatState.php';
require_once 'Date.php';

// TODO: figure out why the valid-ranges are getting having the time inproperly
//       offset.

/**
 * A time entry widget
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTimeEntry extends SwatControl implements SwatState
{
	const HOUR   = 1;
	const MINUTE = 2;
	const SECOND = 4;

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
	public $required;

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
	public $display;

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

	/**
	 * Initializes this widget
	 *
	 * Sets default required and display parts and sets default valid range
	 * for this time entry.
	 */
	public function init()
	{
		$this->display  = self::HOUR | self::MINUTE;
		$this->required = $this->display;

		$this->valid_range_start = new SwatDate('0000-01-01T00:00:00.0000Z');
		$this->valid_range_end   = new SwatDate('0000-01-01T23:59:59.0000Z');
	}

	/**
	 * Displays this time entry
	 *
	 * Creates internal widgets if they do not exits then displays required
	 * javascript, then displays internal widgets.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		echo '<span class="swat-time-span">';

		$this->createFlydowns();

		if ($this->display & self::HOUR) {
			$this->hour_flydown->display();
			if ($this->display & (self::MINUTE | self::SECOND))
				echo ':';
		}

		if ($this->display & self::MINUTE) {
			$this->minute_flydown->display();
			if ($this->display & self::SECOND)
				echo ':';
		}

		if ($this->display & self::SECOND)
			$this->second_flydown->display();

		if ($this->display & self::HOUR)
			$this->am_pm_flydown->display();

		echo '</span>';

		$this->displayJavascript();
	}

	/**
	 * Processes this time entry
	 *
	 * Creates internal widgets if they do not exist and then assigns their
	 * values based on the time entered by the user. If the time is not valid,
	 * an error message is attached to this time entry.
	 */
	public function process()
	{
		$this->createFlydowns();

		if ($this->display & self::HOUR) {
			$this->hour_flydown->process();
			$this->am_pm_flydown->process();
			$hour = intval($this->hour_flydown->value);
			$ampm = $this->am_pm_flydown->value;

			if ($this->required & self::HOUR && $hour === null) {
				$msg = Swat::_('Hour is Required.');
				$this->addMessage(new SwatMessage($msg, SwatMessage::ERROR));
			}

			if ($this->required & self::HOUR && $ampm === null) {
				$msg = Swat::_('AM/PM is Required.');
				$this->addMessage(new SwatMessage($msg, SwatMessage::ERROR));
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
				$msg = Swat::_('Minute is Required.');
				$this->addMessage(new SwatMessage($msg, SwatMessage::ERROR));
			}
		} else {
			$minute = 0;
		}

		if ($this->display & self::SECOND) {
			$this->second_flydown->process();
			$second = intval($this->second_flydown->value);

			if ($this->required & self::SECOND && $second === null) {
				$msg = Swat::_('Second is Required.');
				$this->addMessage(new SwatMessage($msg, SwatMessage::ERROR));
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

	/**
	 * Creates all internal widgets required for this date entry
	 */
	private function createFlydowns()
	{
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

	/**
	 * Creates the hour flydown for this time entry
	 */
	private function createHourFlydown()
	{
		$this->hour_flydown = new SwatFlydown($this->id.'_hour');
		$this->hour_flydown->onchange = sprintf('%s.set(this);', $this->id);

		for ($i = 1; $i <= 12; $i++)
			$this->hour_flydown->addOption($i, $i);
	}

	/**
	 * Creates the minute flydown for this time entry
	 */
	private function createMinuteFlydown()
	{
		$this->minute_flydown = new SwatFlydown($this->id.'_minute');
		$this->minute_flydown->onchange = sprintf('%s.set(this);', $this->id);

		for ($i = 0; $i <= 59; $i++)
			$this->minute_flydown->addOption($i,
				str_pad($i, 2, '0', STR_PAD_LEFT));
	}

	/**
	 * Creates the second flydown for this time entry
	 */
	private function createSecondFlydown()
	{
		$this->second_flydown = new SwatFlydown($this->id.'_second');
		$this->second_flydown->onchange = sprintf('%s.set(this);', $this->id);

		for ($i = 0; $i <= 59; $i++)
			$this->second_flydown->addOptions($i,
				str_pad($i, 2 ,'0', STR_PAD_LEFT));
	}

	/**
	 * Creates the am/pm flydown for this time entry
	 */
	private function createAmPmFlydown()
	{
		$this->am_pm_flydown = new SwatFlydown($this->id.'_ampm');
		$this->am_pm_flydown->addOptionsByArray(array('am' => 'AM', 'pm' => 'PM'));
		$this->am_pm_flydown->onchange = sprintf('%s.set(this);', $this->id);
	}

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

			$msg = sprintf(Swat::_('The time you have entered is invalid. '.
				'It must be after %s.'),
				$this->displayTime($this->valid_range_start));

			$this->addMessage(new SwatMessage($msg, SwatMessage::ERROR));

		} elseif (Date::compare($this->value, $this->valid_range_end, true) == 1) {

			$msg = sprintf(Swat::_('The time you have entered is invalid. '.
				'It must be before %s.'),
				$this->displayTime($this->valid_range_end));

			$this->addMessage(new SwatMessage($msg, SwatMessage::ERROR));

		}
	}

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

	/**
	 * Outputs the javascript required for this control
	 */
	private function displayJavascript()
	{
		static $shown = false;

		if (!$shown) {
			echo '<script type="text/javascript" src="swat/javascript/swat-find-index.js"></script>';
			echo '<script type="text/javascript" src="swat/javascript/swat-time.js"></script>';
			$shown = true;
		}

		echo '<script type="text/javascript">';

		echo sprintf("%s = new SwatTime('%s');\n", $this->id, $this->id);

		echo '</script>';
	}
}

?>
