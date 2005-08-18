<?php

ini_set('include_path', '.:/so/packages/swat/work-gauthierm:/usr/lib/php');

require_once '../include/ExampleApplication.php';

$app = new ExampleApplication('example');
$app->title = 'Swat Widget Gallery';
$app->init();

$page = $app->getPage();
$page->process();
$page->build();
$page->layout->display();

?>
