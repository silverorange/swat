<?php

setlocale(LC_ALL, 'fr_FR.utf8');

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

/*
 * PackageConfig is a silverorange package used for configuring our internal
 * development environment. It can be installed using the public silverorange
 * PEAR channel but is not required for either Swat or the Swat demo.
 */
set_include_path('/so/packages/swat/work-display-context:/so/sites/horton3/work-gauthierm/pear/lib:'.get_include_path());
@include_once 'PackageConfig.php';
if (class_exists('PackageConfig')) {
	PackageConfig::setWorkDirPosition(3);
	PackageConfig::addPackage('site');
	PackageConfig::addPackage('swat');
	PackageConfig::addPackage('jquery');
	PackageConfig::addPackage('recaptcha');
	PackageConfig::addPackage('hot-date');
}

require_once 'Swat/SwatAutoloader.php';
SwatAutoloader::loadRules(dirname(__FILE__).'/../autoloader-rules.conf');
require_once '../include/DemoApplication.php';

SwatException::setupHandler();

$app = new DemoApplication('demo');

try {
	$app->run();
} catch (SwatException $exception) {
	$exception->process(false);
}

?>
