<?php

require('header.php');

require_once('Swat/SwatLayout.php');
require_once('Swat/SwatTableView.php');
require_once('Swat/SwatTableStore.php');
require_once('Swat/SwatCellRendererText.php');
require_once('Swat/SwatCellRendererImage.php');
require_once('Swat/SwatCellRendererLink.php');

require_once("MDB2.php");

//class SliceIndexPage extends AdminPage {
class SliceIndexPage {

	private $layout;

	public function init() {

		$this->layout = new SwatLayout('tableview.xml');

		$view = $this->layout->getWidget('view1');

		$column = new SwatTableViewColumn();
		$column->title = 'First Column';
		$column->renderer = new SwatCellRendererText();
		$column->linkField('title', 'text');
		$view->appendColumn($column);

		$column = new SwatTableViewColumn();
		$column->title = 'Second Column';
		$column->renderer = new SwatCellRendererLink();
		$column->renderer->href = 'details.php?id=%s';
		$column->linkField('createdate', 'text');
		$column->linkField('sliceid', 'href_value');
		$view->appendColumn($column);

		$pager = $this->layout->getWidget('pager');
		$pager->total_records = 33;
		$pager->page_size = 10;

	}

	public function display() {
		$dsn = 'mssql://web:test@192.168.0.20/silverorange2';
		$db = MDB2::connect($dsn);

		if (MDB2::isError($db))
			throw new Exception('Unable to connect to database.');

		$sql = 'SELECT sliceid, title, createdate, hidden FROM slices ORDER BY createdate ASC';
		$types = array('integer', 'text', 'timestamp', 'boolean');
		$result = $db->query($sql, $types);

		if (MDB2::isError($result)) 
			throw new Exception($result->getMessage());

		$store = new SwatTableStore();

		while ($row = $result->fetchRow(MDB2_FETCHMODE_OBJECT))
			$store->addRow($row, $row->sliceid);

		$view = $this->layout->getWidget('view1');
		$view->model = $store;

		$frame = $this->layout->getWidget('frame1');
		$frame->displayTidy();
	}

	public function process() {
		$frame = $this->layout->getWidget('frame1');
		$frame->process();

	}
}


$page = new SliceIndexPage();

$page->init();
$page->process();
$page->display();

require('footer.php');
?>

