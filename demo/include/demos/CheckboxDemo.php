<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Demo.php';

/**
 * A demo using checkboxes
 *
 * @package   SwatDemo
 * @copyright 2005-2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class CheckboxDemo extends Demo
{
	// {{{ public function buildDemoUI()

	public function buildDemoUI(SwatUI $ui)
	{
		// regular checkbox tree
		$tree = new SwatDataTreeNode(null, 'Root');

		$apples = new SwatDataTreeNode(null, 'Apple');
		$apples->addChild(new SwatDataTreeNode(0, 'McIntosh'));
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

		$checkbox_tree = $ui->getWidget('checkbox_tree');
		$checkbox_tree->setTree($tree);

		// expandable checkbox tree
		$tree = new SwatDataTreeNode(null, 'Root');

		$apples = new SwatDataTreeNode(null, 'Apple');
		$apples->addChild(new SwatDataTreeNode(0, 'McIntosh'));
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

		$peaches = new SwatDataTreeNode(15, 'Peach');
		$plums = new SwatDataTreeNode(16, 'Plum');

		$tree->addChild($apples);
		$tree->addChild($oranges);
		$tree->addChild($peaches);
		$tree->addChild($plums);
		$expandable_checkbox_tree = $ui->getWidget('expandable_checkbox_tree');
		$expandable_checkbox_tree->setTree($tree);

		// checkbox list
		$checkbox_list_options = array(
			0 => 'McIntosh',
			1 => 'Courtland',
			2 => 'Golden Delicious',
			3 => 'Fuji',
			4 => 'Granny Smith');

		$checkbox_list = $ui->getWidget('checkbox_list');
		$checkbox_list->addOptionsByArray($checkbox_list_options);

		// checkbox entry list
		$checkbox_entry_list_options = array(
			0 => 'Apple',
			1 => 'Orange',
			2 => 'Banana',
			3 => 'Pear',
			4 => 'Pineapple',
			5 => 'Kiwi');

		$checkbox_entry_list = $ui->getWidget('checkbox_entry_list');
		$checkbox_entry_list->addOptionsByArray($checkbox_entry_list_options);
	}

	// }}}
}

?>
