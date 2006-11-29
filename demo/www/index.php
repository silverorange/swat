<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

@include_once 'PackageConfig.php';
if (class_exists('PackageConfig')) {
	PackageConfig::setWorkDirPosition(3);
	PackageConfig::addPackage('site');
	PackageConfig::addPackage('swat');
}

require_once 'Swat/SwatAutoloader.php';
SwatAutoloader::loadRules(dirname(__FILE__).'/../autoloader-rules.conf');
require_once '../include/DemoApplication.php';

$app = new DemoApplication('demo');
$app->title = 'Swat Demo Site';
$app->uri_prefix_length = 4;
$app->run();

?>
