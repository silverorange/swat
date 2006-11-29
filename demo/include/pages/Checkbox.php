<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'DemoPage.php';
require_once 'Swat/SwatDataTreeNode.php';

/**
 * A demo using checkboxes
 *
 * This page sets up a tree for use in the SwatCheckboxTree demo.
 *
 * @package   SwatDemo
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class Checkbox extends DemoPage
{
	// {{{ public function initUI()

	public function initUI()
	{
		// regular checkbox tree
		$tree = new SwatDataTreeNode(null, 'Root');

		$apples = new SwatDataTreeNode(null, 'Apple');
		$apples->addChild(new SwatDataTreeNode(0, 'Mackintosh'));
		$apples->addChild(new SwatDataTreeNode(1, 'Courtland'));
		$apples->addChild(new SwatDataTreeNode(2, 'Golden Delicious'));
		$apples->addChild(new SwatDataTreeNode(3, 'Fuji'));
		$apples->addChild(new SwatDataTreeNode(4, 'Granny Smith'));
		
		$oranges = new SwatDataTreeNode(null, 'Orange');
		$oranges->addChild(new SwatDataTreeNode(5, 'Navel'));

		$blood_oranges = new SwatDataTreeNode(null, 'Blood');
		$blood_oranges->addChild(new SwatDataTreeNode(6, 'Doble Fina'));
		$blood_oranges->addChild(new SwatDataTreeNode(7, 'Entrefina'));
		$blood_oranges->addChild(new SwatDataTreeNode(8, 'Sanguinelli'));
		$oranges->addChild($blood_oranges);

		$oranges->addChild(new SwatDataTreeNode(9,  'Florida'));
		$oranges->addChild(new SwatDataTreeNode(10, 'California'));
		$oranges->addChild(new SwatDataTreeNode(11, 'Mandarin'));
		
		$tree->addChild($apples);
		$tree->addChild($oranges);

		$checkbox_tree = $this->ui->getWidget('checkbox_tree');
		$checkbox_tree->setTree($tree);

		// expandable checkbox tree
		$tree = new SwatDataTreeNode(null, 'Root');

		$apples = new SwatDataTreeNode(12, 'Apple');
		$apples->addChild(new SwatDataTreeNode(0, 'Mackintosh'));
		$apples->addChild(new SwatDataTreeNode(1, 'Courtland'));
		$apples->addChild(new SwatDataTreeNode(2, 'Golden Delicious'));
		$apples->addChild(new SwatDataTreeNode(3, 'Fuji'));
		$apples->addChild(new SwatDataTreeNode(4, 'Granny Smith'));
		
		$oranges = new SwatDataTreeNode(13, 'Orange');
		$oranges->addChild(new SwatDataTreeNode(5, 'Navel'));

		$blood_oranges = new SwatDataTreeNode(14, 'Blood');
		$blood_oranges->addChild(new SwatDataTreeNode(6, 'Doble Fina'));
		$blood_oranges->addChild(new SwatDataTreeNode(7, 'Entrefina'));
		$blood_oranges->addChild(new SwatDataTreeNode(8, 'Sanguinelli'));
		$oranges->addChild($blood_oranges);

		$oranges->addChild(new SwatDataTreeNode(9,  'Florida'));
		$oranges->addChild(new SwatDataTreeNode(10, 'California'));
		$oranges->addChild(new SwatDataTreeNode(11, 'Mandarin'));
		
		$tree->addChild($apples);
		$tree->addChild($oranges);
		$expandable_checkbox_tree = $this->ui->getWidget('expandable_checkbox_tree');
		$expandable_checkbox_tree->setTree($tree);
	}

	// }}}
}

?>
