<?php

require_once 'DemoPage.php';

/**
 * A demo using a navbar widget
 *
 * This page sets the entries in the navbar widget
 *
 * @package   SwatDemo
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class NavBar extends DemoPage
{
	// {{{ public function initUI()

	public function initUI()
	{
		$navbar = $this->ui->getWidget('navbar');
		$navbar->addEntry(new SwatNavBarEntry('Home', '#'));
		$navbar->addEntry(new SwatNavBarEntry('Demos', '#'));
		$navbar->addEntry(new SwatNavBarEntry('NavBar'));
	}

	// }}}
}

?>
