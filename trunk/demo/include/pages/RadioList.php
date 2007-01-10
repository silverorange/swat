<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'DemoPage.php';

/**
 * A demo using a radiolist
 *
 * This page sets up the radiolist.
 *
 * @package   SwatDemo
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class RadioList extends DemoPage
{
	// {{{ public function initUI()

	public function initUI()
	{
		$radiolist = $this->ui->getWidget('radiolist');
		$radiolist->addOptionsByArray(array(
			0 => 'Apple',
			1 => 'Orange',
			2 => 'Banana',
			3 => 'Pear',
			4 => 'Pineapple',
			5 => 'Kiwi',
			6 => 'Tangerine',
			7 => 'Grapefruit',
			8 => 'Strawberry'));

		$radiotable = $this->ui->getWidget('radiotable');
		$radiotable->addOptionsByArray(array(
			0 => 'Apple',
			1 => 'Orange',
			2 => 'Banana',
			3 => 'Pear',
			4 => 'Pineapple',
			5 => 'Kiwi',
			6 => 'Tangerine',
			7 => 'Grapefruit',
			8 => 'Strawberry'));
	}

	// }}}
}

?>
