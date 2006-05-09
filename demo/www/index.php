<?php
 
require_once 'Swat/SwatAutoloader.php';
SwatAutoloader::addRule('/^Site(.*)/', 'Site/Site$1.php');
SwatAutoloader::addRule('/^Site(.*)Exception$/', 'Site/exceptions/Site$1Exception.php');

require_once '../include/DemoApplication.php';

$app = new DemoApplication('demo');
$app->title = 'Swat Demo Site';
$app->init();

$page = $app->getPage();
$page->process();
$page->build();
$page->layout->display();

?>
