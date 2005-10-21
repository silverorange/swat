<?php

require_once 'DemoPage.php';

/**
 * A demo using a radiolist
 *
 * This page sets up the a radiolist.
 *
 * @package   SwatDemo
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class RadioList extends DemoPage
{
	public function initUI()
	{
		$radiolist = $this->ui->getWidget('radiolist');
		$radiolist->addOptionsByArray(array(
			0=>'Apple',
			1=>'Orange',
			2=>'Banana',
			3=>'Pear',
			4=>'Pineapple',
			5=>'Kiwi',
			6=>'Tangerine',
			7=>'Grapefruit',
			8=>'Strawberry'));
	}
}

?>
