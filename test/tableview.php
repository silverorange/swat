<?php

require('header.php');

require_once('Swat/SwatLayout.php');
require_once('Swat/SwatTableView.php');
require_once('Swat/SwatTableStore.php');
require_once('Swat/SwatCellRendererText.php');
require_once('Swat/SwatCellRendererImage.php');
require_once('Swat/SwatCellRendererLink.php');

$layout = new SwatLayout('tableview.xml');

// TODO: not sure about this notation:
$frame = $layout->getWidget('frame1');
$view = $layout->getWidget('view1');

$store = new SwatTableStore();
$row1->field1 = 'Row A';
$row1->field2 = 'Canoe Cove';
$store->addRow($row1, 1);
$row2->field1 = 'Row B';
$row2->field2 = 'Argyle Shore';
$store->addRow($row2, 2);

$view->model = $store;

$renderer = new SwatCellRendererText();
$column = new SwatTableViewColumn('First Column', $renderer);
$column->linkField('field1', 'text');
$view->appendColumn($column);

$link_renderer = new SwatCellRendererLink();
$link_renderer->href = 'details.php?id=%s';
$column = new SwatTableViewColumn('Second Column', $link_renderer);
$column->linkField('field2', 'text');
$column->linkField('field1', 'href_value');
$view->appendColumn($column);

$image_renderer = new SwatCellRendererImage();
$column = new SwatTableViewColumn('Image Column', $image_renderer);
$column->linkField('field1', 'src');
$view->appendColumn($column);

$pager = $layout->getWidget('pager');
$pager->total_records = 33;
$pager->page_size = 10;

$frame->process();
$frame->displayTidy();

print_r($_GET);
require('footer.php');
?>

