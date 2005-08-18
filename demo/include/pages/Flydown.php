<?php

require_once 'ExamplePage.php';
require_once 'Swat/SwatTreeNode.php';
require_once 'Swat/SwatFlydownOption.php';

class Flydown extends ExamplePage
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

		$apples = new SwatTreeNode(array('title' => 'Apple'));
		$apples->addChild(new SwatTreeNode(array('title' => 'Mackintosh')));
		$apples->addChild(new SwatTreeNode(array('title' => 'Courtland')));
		$apples->addChild(new SwatTreeNode(array('title' => 'Golden Delicious')));
		$apples->addChild(new SwatTreeNode(array('title' => 'Fuji')));
		$apples->addChild(new SwatTreeNode(array('title' => 'Granny Smith')));
		
		$oranges = new SwatTreeNode(array('title' => 'Orange'));
		$oranges->addChild(new SwatTreeNode(array('title' => 'Navel')));
		$oranges->addChild(new SwatTreeNode(array('title' => 'Blood')));
		$oranges->addChild(new SwatTreeNode(array('title' => 'Florida')));
		$oranges->addChild(new SwatTreeNode(array('title' => 'California')));
		$oranges->addChild(new SwatTreeNode(array('title' => 'Mandarin')));
		
		$tree->addChild($apples);
		$tree->addChild($oranges);

		$tree_flydown = $this->ui->getWidget('tree_flydown');
		$tree_flydown->tree = $tree;
	}
}

?>
