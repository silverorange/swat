<?php

require_once 'ExamplePage.php';
require_once 'Swat/SwatTreeNode.php';

class Checkbox extends ExamplePage
{
	public function initUI()
	{
		$tree = new SwatTreeNode(array('title' => 'test'));

		$apples = new SwatTreeNode(array('title' => 'Apple'));
		$apples->addChild(new SwatTreeNode(array('title' => 'Mackintosh', 'value' => 0)));
		$apples->addChild(new SwatTreeNode(array('title' => 'Courtland', 'value' => 1)));
		$apples->addChild(new SwatTreeNode(array('title' => 'Golden Delicious', 'value' => 2)));
		$apples->addChild(new SwatTreeNode(array('title' => 'Fuji', 'value' => 3)));
		$apples->addChild(new SwatTreeNode(array('title' => 'Granny Smith', 'value' => 4)));
		
		$oranges = new SwatTreeNode(array('title' => 'Orange'));
		$oranges->addChild(new SwatTreeNode(array('title' => 'Navel', 'value' => 5)));
		$oranges->addChild(new SwatTreeNode(array('title' => 'Blood', 'value' => 6)));
		$oranges->addChild(new SwatTreeNode(array('title' => 'Florida', 'value' => 7)));
		$oranges->addChild(new SwatTreeNode(array('title' => 'California', 'value' => 8)));
		$oranges->addChild(new SwatTreeNode(array('title' => 'Mandarin', 'value' => 9)));
		
		$tree->addChild($apples);
		$tree->addChild($oranges);

		$checkbox_tree = $this->ui->getWidget('checkbox_tree');
		$checkbox_tree->tree = $tree;
	}
}

?>
