<?php

require_once 'Demo.php';

/**
 * A demo using a navbar widget.
 *
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class NavBarDemo extends Demo
{
    public function buildDemoUI(SwatUI $ui)
    {
        $navbar = $ui->getWidget('navbar_demo');
        $navbar->addEntry(new SwatNavBarEntry('Home', '#'));
        $navbar->addEntry(new SwatNavBarEntry('Demos', '#'));
        $navbar->addEntry(new SwatNavBarEntry('NavBar'));
    }
}
