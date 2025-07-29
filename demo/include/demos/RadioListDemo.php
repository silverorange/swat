<?php

require_once 'Demo.php';

/**
 * A demo using a radiolist.
 *
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class RadioListDemo extends Demo
{
    public function buildDemoUI(SwatUI $ui)
    {
        $radiolist = $ui->getWidget('radiolist');

        $radiolist->addOption(
            new SwatOption('mackintosh', 'McIntosh'),
            ['classes' => 'apple']
        );

        $radiolist->addOption(
            new SwatOption('courtland', 'Courtland'),
            ['classes' => 'apple']
        );

        $radiolist->addOption(
            new SwatOption('golden', 'Golden Delicious'),
            ['classes' => 'apple']
        );

        $radiolist->addOption(
            new SwatOption('fuji', 'Fuji'),
            ['classes' => 'apple']
        );

        $radiolist->addOption(
            new SwatOption('smith', 'Granny Smith'),
            ['classes' => 'apple']
        );

        $radiolist->addOption(
            new SwatOption('navel', 'Navel'),
            ['classes' => 'orange']
        );

        $radiolist->addOption(
            new SwatOption('blood', 'Blood'),
            ['classes' => 'orange']
        );

        $radiolist->addOption(
            new SwatOption('florida', 'Florida'),
            ['classes' => 'orange']
        );

        $radiolist->addOption(
            new SwatOption('california', 'California'),
            ['classes' => 'orange']
        );

        $radiolist->addOption(
            new SwatOption('mandarin', 'Mandarin'),
            ['classes' => 'orange']
        );

        $radiolist->addDivider();
        $radiolist->addOption(new SwatOption(9, 'I don\'t like fruit'));

        $radiotable = $ui->getWidget('radiotable');
        $radiotable->addOptionsByArray([
            0 => 'Apple',
            1 => 'Orange',
            2 => 'Banana',
            3 => 'Pear',
            4 => 'Pineapple',
            5 => 'Kiwi',
            6 => 'Tangerine',
            7 => 'Grapefruit',
            8 => 'Strawberry']);
        $radiotable->addDivider();
        $radiotable->addOption(new SwatOption(9, 'I don\'t like fruit'));
    }
}
