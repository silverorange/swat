<?php

/**
 * A percentage entry widget
 *
 * @package    Swat
 * @copyright  2007 silverorange
 * @lisence    http://www.gnu.org/copyleft/lesser.html LGPL Lisence 2.1
 */
class SwatPercentageEntry extends SwatFloatEntry
{
    // {{{ protected function getDisplayValue()

    /**
     * Returns a value for this widget
     *
     * The method returns a value to be displayed in the widget
     *
     * @return string the final percentage value
     */
    protected function getDisplayValue($value)
    {
        if (is_numeric($value)) {
            $value = $value * 100;
            $value = parent::getDisplayValue($value);
            $value = $value . '%';
        } else {
            $value = parent::getDisplayValue($value);
        }

        return $value;
    }

    // }}}
    // {{{ protected function getNumericValue()

    /**
     * Gets the float value of this widget
     *
     * This allows each widget to parse raw values and turn them into floats
     *
     * @param string $value the raw value to use to get the numeric value.
     *
     * @return mixed the numeric value of this entry widget or null if no
     *               no numeric value is available.
     */
    protected function getNumericValue($value)
    {
        $value = trim($value);
        $value = str_replace('%', '', $value);
        $value = parent::getNumericValue($value);
        if ($value !== null) {
            $value = $value / 100;
        }

        return $value;
    }

    // }}}
    // {{{ protected function getCSSClassNames()

    /**
     * Gets the array of CSS classes that are applied to this entry
     *
     * @return array the array of CSS classes that are applied to this
     *                entry.
     */
    protected function getCSSClassNames()
    {
        $classes = ['swat-percentage-entry'];
        $classes = array_merge($classes, parent::getCSSClassNames());
        return $classes;
    }

    // }}}
}
