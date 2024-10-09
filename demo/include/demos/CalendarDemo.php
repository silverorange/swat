<?php

require_once 'Demo.php';

/**
 * A demo using a calendar.
 *
 * This demo sets up a flydown widget to demonstrate how SwatCalendar handles
 * displaying a pop-up over a select list in IE6.
 *
 * @copyright 2007-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class CalendarDemo extends Demo
{
    public function buildDemoUI(SwatUI $ui)
    {
        $flydown = $ui->getWidget('flydown');
        $flydown->options = [
            new SwatOption(0, 'Apple'),
            new SwatOption(1, 'Orange'),
            new SwatOption(2, 'Banana'),
            new SwatOption(3, 'Pear'),
            new SwatOption(4, 'Pineapple'),
            new SwatOption(5, 'Kiwi'),
            new SwatOption(6, 'Tangerine'),
            new SwatOption(7, 'Grapefruit'),
            new SwatOption(8, 'Strawberry'),
        ];
    }
}
