<?php

/**
 * A time entry widget.
 *
 * @copyright 2004-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @todo      Should we add a display_time_zone parameter?
 */
class SwatTimeEntry extends SwatInputControl implements SwatState
{
    // {{{ constants

    public const HOUR = 1;
    public const MINUTE = 2;
    public const SECOND = 4;

    // }}}
    // {{{ public properties

    /**
     * Time of this time entry widget.
     *
     * The year, month and day fields of the SwatDate object are unused and
     * undefined. If the state of this time entry does not represent a valid
     * time, the value will be null.
     *
     * @var SwatDate
     */
    public $value;

    /**
     * Required time parts.
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
     * @var int
     */
    public $required_parts;

    /**
     * Displayed time parts.
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
     * @var int
     */
    public $display_parts;

    /**
     * Start time of the valid range (inclusive).
     *
     * Defaults to 00:00:00. The year, month and day fields of the Date object
     * are ignored and undefined. This value is inclusive. The time-zone of
     * this date is ignored. Internal time comparisons are done in UTC.
     *
     * @var SwatDate
     */
    public $valid_range_start;

    /**
     * End time of the valid range (inclusive).
     *
     * Defaults to 23:59:59. The year, month and day fields of the Date object
     * are ignored and undefined. This value is inclusive. The time-zone of
     * this date is ignored. Internal time comparisons are done in UTC.
     *
     * @var SwatDate
     */
    public $valid_range_end;

    /**
     * Whether or not times are entered and displayed in 12-hour format.
     *
     * If not specified, defaults to the default format of the current locale.
     *
     * @var bool
     */
    public $twelve_hour;

    /**
     * Whether or not this time entry should auto-complete to the current time.
     *
     * @var bool
     */
    public $use_current_time = true;

    // }}}
    // {{{ private properties

    /**
     * Default year value used for time value.
     *
     * Defined here so internal time comparisons all happen on the same day.
     *
     * @var int
     */
    private static $date_year = 2000;

    /**
     * Default month value used for time value.
     *
     * Defined here so internal time comparisons all happen on the same day.
     *
     * @var int
     */
    private static $date_month = 1;

    /**
     * Default day value used for time value.
     *
     * Defined here so internal time comparisons all happen on the same day.
     *
     * @var int
     */
    private static $date_day = 1;

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new time entry widget.
     *
     * Sets default required and display parts and sets default valid range
     * for this time entry.
     *
     * @param string $id a non-visible unique id for this widget
     *
     * @see SwatWidget::__construct()
     */
    public function __construct($id = null)
    {
        parent::__construct($id);

        $this->display_parts = self::HOUR | self::MINUTE;
        $this->required_parts = $this->display_parts;

        // Don't specify a zero offset for these times as it triggers a
        // PEAR::Date bug. All comparisons are explicitly done in UTC.
        $this->valid_range_start = new SwatDate('2000-01-01T00:00:00');
        $this->valid_range_end = new SwatDate('2000-01-01T23:59:59');

        $this->requires_id = true;

        $yui = new SwatYUI(['event']);
        $this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());
        $this->addJavaScript('packages/swat/javascript/swat-time-entry.js');

        // guess twelve-hour or twenty-four hour default based on locale
        $locale_format = nl_langinfo(T_FMT);
        $this->twelve_hour
            = preg_match('/(%T|%R|%k|.*%H.*)/', $locale_format) === 0;
    }

    // }}}
    // {{{ public function __clone()

    /**
     * Clones the valid time range of this time entry.
     */
    public function __clone()
    {
        $this->valid_range_start = clone $this->valid_range_start;
        $this->valid_range_end = clone $this->valid_range_end;
    }

    // }}}
    // {{{ public function display()

    /**
     * Displays this time entry.
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

        echo '<span class="swat-time-entry-span">';

        if ($this->display_parts & self::HOUR) {
            $hour_flydown = $this->getCompositeWidget('hour_flydown');
            if ($hour_flydown->value === null && $this->value !== null) {
                // work around a bug in PEAR::Date that returns hour as a string
                $hour = intval($this->value->getHour());

                // convert 24-hour value to 12-hour display
                if ($this->twelve_hour) {
                    if ($hour > 12) {
                        $hour -= 12;
                    }

                    if ($hour === 0) {
                        $hour = 12;
                    }
                }

                $hour_flydown->value = $hour;
            }

            $hour_flydown->display();

            if ($this->display_parts & (self::MINUTE | self::SECOND)) {
                echo ':';
            }
        }

        if ($this->display_parts & self::MINUTE) {
            $minute_flydown = $this->getCompositeWidget('minute_flydown');
            if ($minute_flydown->value === null && $this->value !== null) {
                // work around a bug in PEAR::Date that returns minutes as a
                // 2-character string
                $minute = intval($this->value->getMinute());
                $minute_flydown->value = $minute;
            }

            $minute_flydown->display();
            if ($this->display_parts & self::SECOND) {
                echo ':';
            }
        }

        if ($this->display_parts & self::SECOND) {
            $second_flydown = $this->getCompositeWidget('second_flydown');
            if ($second_flydown->value === null && $this->value !== null) {
                $second_flydown->value = $this->value->getSecond();
            }

            $second_flydown->display();
        }

        if ($this->display_parts & self::HOUR && $this->twelve_hour) {
            $am_pm_flydown = $this->getCompositeWidget('am_pm_flydown');
            if ($am_pm_flydown->value === null && $this->value !== null) {
                $am_pm_flydown->value
                    = $this->value->getHour() < 12 ? 'am' : 'pm';
            }

            $am_pm_flydown->display();
        }

        echo '</span>';

        Swat::displayInlineJavaScript($this->getInlineJavaScript());

        $div_tag->close();
    }

    // }}}
    // {{{ public function process()

    /**
     * Processes this time entry.
     *
     * If the time is not valid an error message is attached to this time
     * entry.
     */
    public function process()
    {
        parent::process();

        if (!$this->isVisible()) {
            return;
        }

        $hour = 0;
        $minute = 0;
        $second = 0;

        $all_empty = true;
        $any_empty = false;

        if ($this->display_parts & self::HOUR) {
            $hour_flydown = $this->getCompositeWidget('hour_flydown');
            $hour = $hour_flydown->value;
            if ($hour === null) {
                if ($this->required_parts & self::HOUR) {
                    $any_empty = true;
                } else {
                    $hour = 0;
                }
            } else {
                $all_empty = false;
            }

            if ($this->twelve_hour) {
                $am_pm_flydown = $this->getCompositeWidget('am_pm_flydown');
                $am_pm = $am_pm_flydown->value;
                if ($am_pm === null) {
                    if ($this->required_parts & self::HOUR) {
                        $any_empty = true;
                    } else {
                        $am_pm = 'am';
                    }
                } else {
                    $all_empty = false;
                }

                // convert 12-hour display to 24-hour value
                if ($hour !== null && $am_pm == 'pm' && $hour != 12) {
                    $hour += 12;
                }
                if ($hour == 12 && $am_pm == 'am') {
                    $hour = 0;
                }
            }
        }

        if ($this->display_parts & self::MINUTE) {
            $minute_flydown = $this->getCompositeWidget('minute_flydown');
            $minute = $minute_flydown->value;
            if ($minute === null) {
                if ($this->required_parts & self::MINUTE) {
                    $any_empty = true;
                } else {
                    $minute = 0;
                }
            } else {
                $all_empty = false;
            }
        }

        if ($this->display_parts & self::SECOND) {
            $second_flydown = $this->getCompositeWidget('second_flydown');
            $second = $second_flydown->value;
            if ($second === null) {
                if ($this->required_parts & self::SECOND) {
                    $any_empty = true;
                } else {
                    $second = 0;
                }
            } else {
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
                $message = Swat::_('%s is not a valid time.');
            } else {
                $message = Swat::_('This is not a valid time.');
            }
            $this->addMessage(new SwatMessage($message, 'error'));
            $this->value = null;
        } else {
            try {
                $date = new SwatDate();
                if (
                    $date->setDate(
                        self::$date_year,
                        self::$date_month,
                        self::$date_day,
                    ) === false
                ) {
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
                    $message = Swat::_('%s is not a valid time.');
                } else {
                    $message = Swat::_('This is not a valid time.');
                }
                $this->addMessage(new SwatMessage($message, 'error'));
                $this->value = null;
            }
        }
    }

    // }}}
    // {{{ public function getState()

    /**
     * Gets the current state of this time entry widget.
     *
     * @return bool the current state of this time entry widget
     *
     * @see SwatState::getState()
     */
    public function getState()
    {
        if ($this->value === null) {
            return null;
        }

        return $this->value->getDate();
    }

    // }}}
    // {{{ public function setState()

    /**
     * Sets the current state of this time entry widget.
     *
     * @param bool $state the new state of this time entry widget
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
     * Gets the array of CSS classes that are applied to this time entry widget.
     *
     * @return array the array of CSS classes that are applied to this time
     *               entry widget
     */
    protected function getCSSClassNames()
    {
        $classes = ['swat-time-entry'];

        return array_merge($classes, parent::getCSSClassNames());
    }

    // }}}
    // {{{ protected function getInlineJavaScript()

    /**
     * Gets the inline JavaScript required for this control.
     *
     * @return string the inline JavaScript required for this control
     */
    protected function getInlineJavaScript()
    {
        $use_current_time = $this->use_current_time ? 'true' : 'false';

        $javascript = sprintf(
            "var %s_obj = new SwatTimeEntry('%s', %s);\n",
            $this->id,
            $this->id,
            $use_current_time,
        );

        if ($this->display_parts & self::HOUR) {
            $hour_flydown = $this->getCompositeWidget('hour_flydown');

            $lookup_hours = [];
            foreach ($hour_flydown->options as $key => $option) {
                $lookup_hours[] = sprintf(
                    '%s: %s',
                    $option->value,
                    $hour_flydown->show_blank ? $key + 1 : $key,
                );
            }

            $javascript .= sprintf(
                "\n%s_obj.addLookupTable('hour', {%s});",
                $this->id,
                implode(', ', $lookup_hours),
            );
        }

        if ($this->display_parts & self::MINUTE) {
            $minute_flydown = $this->getCompositeWidget('minute_flydown');

            $lookup_minutes = [];
            foreach ($minute_flydown->options as $key => $option) {
                $lookup_minutes[] = sprintf(
                    '%s: %s',
                    $option->value,
                    $minute_flydown->show_blank ? $key + 1 : $key,
                );
            }

            $javascript .= sprintf(
                "\n%s_obj.addLookupTable('minute', {%s});",
                $this->id,
                implode(', ', $lookup_minutes),
            );
        }

        if ($this->display_parts & self::SECOND) {
            $second_flydown = $this->getCompositeWidget('second_flydown');

            $lookup_seconds = [];
            foreach ($second_flydown->options as $key => $option) {
                $lookup_seconds[] = sprintf(
                    '%s: %s',
                    $option->value,
                    $second_flydown->show_blank ? $key + 1 : $key,
                );
            }

            $javascript .= sprintf(
                "\n%s_obj.addLookupTable('second', {%s});",
                $this->id,
                implode(', ', $lookup_seconds),
            );
        }

        return $javascript;
    }

    // }}}
    // {{{ protected function validateRanges()

    /**
     * Makes sure the date the user entered is within the valid range.
     *
     * If the time is not within the valid range, this method attaches an
     * error message to this time entry.
     */
    protected function validateRanges()
    {
        if (!$this->isStartTimeValid()) {
            $message = sprintf(
                Swat::_(
                    'The time you have entered is invalid. '
                        . 'It must be on or after %s.',
                ),
                $this->getFormattedTime($this->valid_range_start),
            );

            $this->addMessage(new SwatMessage($message, 'error'));
        } elseif (!$this->isEndTimeValid()) {
            $message = sprintf(
                Swat::_(
                    'The time you have entered is invalid. '
                        . 'It must be on or before %s.',
                ),
                $this->getFormattedTime($this->valid_range_end),
            );

            $this->addMessage(new SwatMessage($message, 'error'));
        }
    }

    // }}}
    // {{{ protected function isStartTimeValid()

    /**
     * Checks if the entered time is valid with respect to the valid start
     * time.
     *
     * @return bool true if the entered time is on or after the valid start
     *              time and false if the entered time is before the valid
     *              start time
     */
    protected function isStartTimeValid()
    {
        $this->valid_range_start->setYear(self::$date_year);
        $this->valid_range_start->setMonth(self::$date_month);
        $this->valid_range_start->setDay(self::$date_day);
        $this->valid_range_start->setTZById('UTC');

        return SwatDate::compare(
            $this->value,
            $this->valid_range_start,
            true,
        ) >= 0;
    }

    // }}}
    // {{{ protected function isEndTimeValid()

    /**
     * Checks if the entered time is valid with respect to the valid end time.
     *
     * @return bool true if the entered time is before the valid end time
     *              and false if the entered time is on or after the valid
     *              end time
     */
    protected function isEndTimeValid()
    {
        $this->valid_range_end->setYear(self::$date_year);
        $this->valid_range_end->setMonth(self::$date_month);
        $this->valid_range_end->setDay(self::$date_day);
        $this->valid_range_end->setTZById('UTC');

        return SwatDate::compare($this->value, $this->valid_range_end, true)
            <= 0;
    }

    // }}}
    // {{{ protected function createCompositeWidgets()

    /**
     * Creates the composite widgets used by this time entry.
     *
     * @see SwatWidget::createCompositeWidgets()
     */
    protected function createCompositeWidgets()
    {
        if ($this->display_parts & self::HOUR) {
            $this->addCompositeWidget(
                $this->createHourFlydown(),
                'hour_flydown',
            );

            if ($this->twelve_hour) {
                $this->addCompositeWidget(
                    $this->createAmPmFlydown(),
                    'am_pm_flydown',
                );
            }
        }

        if ($this->display_parts & self::MINUTE) {
            $this->addCompositeWidget(
                $this->createMinuteFlydown(),
                'minute_flydown',
            );
        }

        if ($this->display_parts & self::SECOND) {
            $this->addCompositeWidget(
                $this->createSecondFlydown(),
                'second_flydown',
            );
        }
    }

    // }}}
    // {{{ private function createHourFlydown()

    /**
     * Creates the hour flydown for this time entry.
     *
     * @return the hour flydown for this time entry
     */
    private function createHourFlydown()
    {
        $flydown = new SwatFlydown($this->id . '_hour');
        $flydown->classes = ['swat-time-entry-hour'];

        if ($this->twelve_hour) {
            for ($i = 1; $i <= 12; $i++) {
                $flydown->addOption($i, $i);
            }
        } else {
            for ($i = 0; $i < 24; $i++) {
                $flydown->addOption($i, $i);
            }
        }

        return $flydown;
    }

    // }}}
    // {{{ private function createMinuteFlydown()

    /**
     * Creates the minute flydown for this time entry.
     *
     * @return SwatFlydown the minute flydown for this time entry
     */
    private function createMinuteFlydown()
    {
        $flydown = new SwatFlydown($this->id . '_minute');
        $flydown->classes = ['swat-time-entry-minute'];

        for ($i = 0; $i <= 59; $i++) {
            $flydown->addOption($i, str_pad($i, 2, '0', STR_PAD_LEFT));
        }

        return $flydown;
    }

    // }}}
    // {{{ private function createSecondFlydown()

    /**
     * Creates the second flydown for this time entry.
     *
     * @return SwatFlydown the second flydown for this time entry
     */
    private function createSecondFlydown()
    {
        $flydown = new SwatFlydown($this->id . '_second');
        $flydown->classes = ['swat-time-entry-second'];

        for ($i = 0; $i <= 59; $i++) {
            $flydown->addOption($i, str_pad($i, 2, '0', STR_PAD_LEFT));
        }

        return $flydown;
    }

    // }}}
    // {{{ private function createAmPmFlydown()

    /**
     * Creates the am/pm flydown for this time entry.
     *
     * @return SwatFlydown the am/pm flydown for this time entry
     */
    private function createAmPmFlydown()
    {
        $flydown = new SwatFlydown($this->id . '_am_pm');
        $flydown->classes = ['swat-time-entry-ampm'];
        $flydown->addOptionsByArray([
            'am' => Swat::_('am'),
            'pm' => Swat::_('pm'),
        ]);

        return $flydown;
    }

    // }}}
    // {{{ private function getFormattedTime()

    /**
     * Formats a time for display in error messages.
     *
     * @param SwatDate $time the time to format
     *
     * @return string the formatted time
     */
    private function getFormattedTime(SwatDate $time)
    {
        $format = '';

        if ($this->display_parts & self::HOUR) {
            $format .= $this->twelve_hour ? 'h' : 'H';
            if ($this->display_parts & (self::MINUTE | self::SECOND)) {
                $format .= ':';
            }
        }

        if ($this->display_parts & self::MINUTE) {
            $format .= 'mm';
            if ($this->display_parts & self::SECOND) {
                $format .= ':';
            }
        }

        if ($this->display_parts & self::SECOND) {
            $format .= 'ss';
        }

        if ($this->display_parts & self::HOUR && $this->twelve_hour) {
            $format .= ' a';
        }

        return $time->formatLikeIntl($format);
    }

    // }}}
}
