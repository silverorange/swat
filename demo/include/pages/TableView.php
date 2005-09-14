<?php

require_once 'DemoPage.php';
require_once 'Swat/SwatTableStore.php';
require_once 'Swat/SwatDate.php';

/**
 * A demo using a table view
 *
 * @package   SwatDemo
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class TableView extends DemoPage
{
	public function initUI()
	{
		$data = array(
			array('Apple', 'red', false, true, new SwatDate('2005-09-01'), 0.5),
			array('Orange', 'orange', false, false, new SwatDate('2005-04-20'), 0.75),
			array('Strawberry', 'red', true, false, new SwatDate('2005-07-05'), 0.6)
		);
		
		$table_view = $this->ui->getWidget('table_view');
		$table_store = new SwatTableStore();
		
		foreach ($data as $datum) {
			$fruit = new FruitObject();
			$fruit->title = $datum[0];
			$fruit->color = $datum[1];
			$fruit->makes_jam = $datum[2];
			$fruit->makes_pie = $datum[3];
			$fruit->harvest_date = $datum[4];
			$fruit->cost = $datum[5];

			$table_store->addRow($fruit);
		}

		$table_view->model = $table_store;

	}
}

/**
 * A demo using a table view
 *
 * @package   SwatDemo
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class FruitObject
{
	public $title = '';
	public $color = '';
	public $makes_jam = false;
	public $makes_pie = false;
	public $harvest_date = null;
	public $cost = 0;
}

?>
