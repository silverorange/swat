<?php

$uri_array = explode('/', $_SERVER['REQUEST_URI']);
$work_dir = $uri_array[3];

ini_set('include_path', ".:/so/packages/swat/{$work_dir}:/usr/lib/php");
require_once '../include/DemoApplication.php';

$app = new DemoApplication('example');
$app->title = 'Swat Widget Gallery';
$app->init();

$page = $app->getPage();
$page->process();
$page->build();
$page->layout->display();

?>
