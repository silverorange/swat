<?php

require_once 'DemoPage.php';
require_once 'Swat/SwatTreeNode.php';
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

		$tree = new SwatTreeNode(array('title' => 'test'));

		$apples = new SwatTreeNode(array('id' => 'apple', 'title' => 'Apple'));
		$apples->addChild(new SwatTreeNode(array('id' => 'mackintish', 'title' => 'Mackintosh')));
		$apples->addChild(new SwatTreeNode(array('id' => 'courtland', 'title' => 'Courtland')));
		$apples->addChild(new SwatTreeNode(array('id' => 'golden', 'title' => 'Golden Delicious')));
		$apples->addChild(new SwatTreeNode(array('id' => 'fuji', 'title' => 'Fuji')));
		$apples->addChild(new SwatTreeNode(array('id' => 'granny', 'title' => 'Granny Smith')));
		
		$oranges = new SwatTreeNode(array('id' => 'orange', 'title' => 'Orange'));
		$oranges->addChild(new SwatTreeNode(array('id' => 'navel', 'title' => 'Navel')));
		$oranges->addChild(new SwatTreeNode(array('id' => 'blood', 'title' => 'Blood')));
		$oranges->addChild(new SwatTreeNode(array('id' => 'florida', 'title' => 'Florida')));
		$oranges->addChild(new SwatTreeNode(array('id' => 'california', 'title' => 'California')));
		$oranges->addChild(new SwatTreeNode(array('id' => 'mandarin', 'title' => 'Mandarin')));
		
		$tree->addChild($apples);
		$tree->addChild($oranges);

		$tree_flydown = $this->ui->getWidget('tree_flydown');
		$tree_flydown->tree = $tree;

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
