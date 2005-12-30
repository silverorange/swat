<?php

require_once 'DemoPage.php';
require_once 'Swat/SwatDataTreeNode.php';

/**
 * A demo using checkboxes
 *
 * This page sets up a tree for use in the SwatCheckboxTree demo.
 *
 * @package   SwatDemo
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class Checkbox extends DemoPage
{
	public function initUI()
	{
		$tree = new SwatDataTreeNode('test');

		$apples = new SwatDataTreeNode('Apple');
		$apples->addChild(new SwatDataTreeNode('Mackintosh', 0));
		$apples->addChild(new SwatDataTreeNode('Courtland', 1));
		$apples->addChild(new SwatDataTreeNode('Golden Delicious', 2));
		$apples->addChild(new SwatDataTreeNode('Fuji', 3));
		$apples->addChild(new SwatDataTreeNode('Granny Smith', 4));
		
		$oranges = new SwatDataTreeNode('Orange');
		$oranges->addChild(new SwatDataTreeNode('Navel', 5));

		$blood_oranges = new SwatDataTreeNode('Blood');
		$blood_oranges->addChild(new SwatDataTreeNode('Doble Fina', 6));
		$blood_oranges->addChild(new SwatDataTreeNode('Entrefina', 7));
		$blood_oranges->addChild(new SwatDataTreeNode('Sanguinelli', 8));
		$oranges->addChild($blood_oranges);

		$oranges->addChild(new SwatDataTreeNode('Florida', 9));
		$oranges->addChild(new SwatDataTreeNode('California', 10));
		$oranges->addChild(new SwatDataTreeNode('Mandarin', 11));
		
		$tree->addChild($apples);
		$tree->addChild($oranges);

		$checkbox_tree = $this->ui->getWidget('checkbox_tree');
		$checkbox_tree->setTree($tree);

		$tree = new SwatDataTreeNode('test');

		$apples = new SwatDataTreeNode('Apple', 12);
		$apples->addChild(new SwatDataTreeNode('Mackintosh', 0));
		$apples->addChild(new SwatDataTreeNode('Courtland', 1));
		$apples->addChild(new SwatDataTreeNode('Golden Delicious', 2));
		$apples->addChild(new SwatDataTreeNode('Fuji', 3));
		$apples->addChild(new SwatDataTreeNode('Granny Smith', 4));
		
		$oranges = new SwatDataTreeNode('Orange', 13);
		$oranges->addChild(new SwatDataTreeNode('Navel', 5));

		$blood_oranges = new SwatDataTreeNode('Blood', 14);
		$blood_oranges->addChild(new SwatDataTreeNode('Doble Fina', 6));
		$blood_oranges->addChild(new SwatDataTreeNode('Entrefina', 7));
		$blood_oranges->addChild(new SwatDataTreeNode('Sanguinelli', 8));
		$oranges->addChild($blood_oranges);

		$oranges->addChild(new SwatDataTreeNode('Florida', 9));
		$oranges->addChild(new SwatDataTreeNode('California', 10));
		$oranges->addChild(new SwatDataTreeNode('Mandarin', 11));
		
		$tree->addChild($apples);
		$tree->addChild($oranges);
		$expandable_checkbox_tree = $this->ui->getWidget('expandable_checkbox_tree');
		$expandable_checkbox_tree->setTree($tree);
	}
}

?>
