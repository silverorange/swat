<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Demo.php';

/**
 * A demo using a radiolist
 *
 * @package   SwatDemo
 * @copyright 2005-2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class RadioListDemo extends Demo
{
	// {{{ public function buildDemoUI()

	public function buildDemoUI(SwatUI $ui)
	{
		$radiolist = $ui->getWidget('radiolist');

		$radiolist->addOption(
			new SwatOption('mackintosh', 'McIntosh'),
			array('classes' => 'apple'));

		$radiolist->addOption(
			new SwatOption('courtland', 'Courtland'),
			array('classes' => 'apple'));

		$radiolist->addOption(
			new SwatOption('golden', 'Golden Delicious'),
			array('classes' => 'apple'));

		$radiolist->addOption(
			new SwatOption('fuji', 'Fuji'),
			array('classes' => 'apple'));

		$radiolist->addOption(
			new SwatOption('smith', 'Granny Smith'),
			array('classes' => 'apple'));

		$radiolist->addOption(
			new SwatOption('navel', 'Navel'),
			array('classes' => 'orange'));

		$radiolist->addOption(
			new SwatOption('blood', 'Blood'),
			array('classes' => 'orange'));

		$radiolist->addOption(
			new SwatOption('florida', 'Florida'),
			array('classes' => 'orange'));

		$radiolist->addOption(
			new SwatOption('california', 'California'),
			array('classes' => 'orange'));

		$radiolist->addOption(
			new SwatOption('mandarin', 'Mandarin'),
			array('classes' => 'orange'));

		$radiolist->addDivider();
		$radiolist->addOption(new SwatOption(9, 'I don\'t like fruit'));

		$radiotable = $ui->getWidget('radiotable');
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
		$radiotable->addDivider();
		$radiotable->addOption(new SwatOption(9, 'I don\'t like fruit'));
	}

	// }}}
}

?>
