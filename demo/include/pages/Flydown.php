<?php

require_once 'DemoPage.php';
require_once 'Swat/SwatTreeFlydownNode.php';
require_once 'Swat/SwatOption.php';

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
			new SwatOption(0, 'Apple'),
			new SwatOption(1, 'Orange'),
			new SwatOption(2, 'Banana'),
			new SwatOption(3, 'Pear'),
			new SwatOption(4, 'Pineapple'),
			new SwatOption(5, 'Kiwi'),
			new SwatOption(6, 'Tangerine'),
			new SwatOption(7, 'Grapefruit'),
			new SwatOption(8, 'Strawberry')
		);

		// tree flydown
		$tree = new SwatTreeFlydownNode(null, 'Root');

		$apples = new SwatTreeFlydownNode('apple', 'Apple');
		$apples->addChild(new SwatTreeFlydownNode('mackintosh', 'Mackintish'));
		$apples->addChild(new SwatTreeFlydownNode('courtland', 'Courtland'));
		$apples->addChild(new SwatTreeFlydownNode('golden', 'Golden Delicious'));
		$apples->addChild(new SwatTreeFlydownNode('fuji', 'Fuji'));
		$apples->addChild(new SwatTreeFlydownNode('smith', 'Granny Smith'));

		$oranges = new SwatTreeFlydownNode('orange', 'Orange');
		$oranges->addChild(new SwatTreeFlydownNode('navel', 'Navel'));
		$oranges->addChild(new SwatTreeFlydownNode('blood', 'Blood'));
		$oranges->addChild(new SwatTreeFlydownNode('florida', 'Florida'));
		$oranges->addChild(new SwatTreeFlydownNode('california', 'California'));
		$oranges->addChild(new SwatTreeFlydownNode('mandarin', 'Mandarin'));

		$tree->addChild($apples);
		$tree->addChild($oranges);

		$tree_flydown = $this->ui->getWidget('tree_flydown');
		$tree_flydown->setTree($tree);

		$cascade_from = $this->ui->getWidget('cascade_from');
		$cascade_from->options = array(
			new SwatOption(0, 'Apple'),
			new SwatOption(1, 'Orange')
		);

		$cascade_to = $this->ui->getWidget('cascade_to');
		$cascade_to->cascade_from = $cascade_from;

		$cascade_to->options = array(
			0 => array(
				new SwatOption(0, 'Mackintosh'),
				new SwatOption(1, 'Courtland'),
				new SwatOption(2, 'Golden Delicious'),
				new SwatOption(3, 'Fuji'),
				new SwatOption(4, 'Granny Smith')
			),
			1 => array(
				new SwatOption(0, 'Navel'),
				new SwatOption(1, 'Blood'),
				new SwatOption(2, 'Florida'),
				new SwatOption(3, 'California'),
				new SwatOption(4, 'Mandarin')
			)
		);
	}
}

?>
