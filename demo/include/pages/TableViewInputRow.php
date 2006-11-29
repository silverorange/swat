<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'DemoPage.php';

/**
 * A demo using a table view
 *
 * @package   SwatDemo
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class TableViewInputRow extends DemoPage
{
	// private function initUI();

	public function initUI()
	{
		$data = array(
			array('Apple', false, true),
			array('Orange', false, false),
			array('Strawberry', true, false),
		);

		$table_view = $this->ui->getWidget('table_view');
		$table_store = new SwatTableStore();

		foreach ($data as $datum) {
			$fruit = new FruitObject();
			$fruit->title = $datum[0];
			$fruit->makes_jam = $datum[1];
			$fruit->makes_pie = $datum[2];

			$table_store->addRow($fruit);
		}

		$table_view->model = $table_store;
	}

	// }}}
}

/**
 * A demo using a table view
 *
 * @package   SwatDemo
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class FruitObject
{
	// {{{ public properties

	public $title = '';
	public $makes_jam = false;
	public $makes_pie = false;

	// }}}
}

?>
