<?php

require_once 'DemoPage.php';
require_once 'Swat/SwatDataTreeNode.php';
require_once 'Swat/SwatFlydownOption.php';

/**
 * A demo using flydowns
 *
 * This page sets up the various flydown widgets. All flydown widgets currently
 * must be set up manually as they contain SwatFlyDown options rather than
 * an array.
 *
 * @package   SwatDemo
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class Flydown extends DemoPage
{
	public function initUI()
	{
		$flydown = $this->ui->getWidget('flydown');
		$flydown->options = array(
			new SwatFlydownOption(0, 'Apple'),
			new SwatFlydownOption(1, 'Orange'),
			new SwatFlydownOption(2, 'Banana'),
			new SwatFlydownOption(3, 'Pear'),
			new SwatFlydownOption(4, 'Pineapple'),
			new SwatFlydownOption(5, 'Kiwi'),
			new SwatFlydownOption(6, 'Tangerine'),
			new SwatFlydownOption(7, 'Grapefruit'),
			new SwatFlydownOption(8, 'Strawberry')
		);

		$tree = new SwatDataTreeNode('test');

		$apples = new SwatDataTreeNode('Apple', 'apple');
		$apples->addChild(new SwatDataTreeNode('Mackintish', 'mackintosh'));
		$apples->addChild(new SwatDataTreeNode('Courtland', 'courtland'));
		$apples->addChild(new SwatDataTreeNode('Golden Delicious', 'golden'));
		$apples->addChild(new SwatDataTreeNode('Fuji', 'fuji'));
		$apples->addChild(new SwatDataTreeNode('Granny Smith', 'granny'));

		$oranges = new SwatDataTreeNode('Orange', 'orange');
		$oranges->addChild(new SwatDataTreeNode('Navel', 'navel'));
		$oranges->addChild(new SwatDataTreeNode('Blood', 'blood'));
		$oranges->addChild(new SwatDataTreeNode('Florida', 'florida'));
		$oranges->addChild(new SwatDataTreeNode('California', 'california'));
		$oranges->addChild(new SwatDataTreeNode('Mandarin', 'mandarin'));

		$tree->addChild($apples);
		$tree->addChild($oranges);

		$tree_flydown = $this->ui->getWidget('tree_flydown');
		$tree_flydown->setTree($tree);

		$cascade_from = $this->ui->getWidget('cascade_from');
		$cascade_from->options = array(
			new SwatFlydownOption(0, 'Apple'),
			new SwatFlydownOption(1, 'Orange')
		);

		$cascade_to = $this->ui->getWidget('cascade_to');
		$cascade_to->cascade_from = $cascade_from;

		$cascade_to->options = array(
			/*
			0 => array(
				0 => 'Mackintosh',
				1 => 'Courtland',
				2 => 'Golden Delicious',
				3 => 'Fuji',
				4 => 'Granny Smith'
			),
			1 => array(
				0 => 'Navel',
				1 => 'Blood',
				2 => 'Florida',
				3 => 'California',
				4 => 'Mandarin'
			)
			*/
			0 => array(
				new SwatFlydownOption(0, 'Mackintosh'),
				new SwatFlydownOption(1, 'Courtland'),
				new SwatFlydownOption(2, 'Golden Delicious'),
				new SwatFlydownOption(3, 'Fuji'),
				new SwatFlydownOption(4, 'Granny Smith')
			),
			1 => array(
				new SwatFlydownOption(0, 'Navel'),
				new SwatFlydownOption(1, 'Blood'),
				new SwatFlydownOption(2, 'Florida'),
				new SwatFlydownOption(3, 'California'),
				new SwatFlydownOption(4, 'Mandarin')
			)
		);
	}
}

?>
