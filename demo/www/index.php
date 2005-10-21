<?php

require_once '../include/DemoApplication.php';

$app = new DemoApplication('demo');
$app->title = 'Swat Demo Site';
$app->init();

$page = $app->getPage();
$page->process();
$page->build();
$page->layout->display();

?>
