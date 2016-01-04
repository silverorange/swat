<?php

namespace Silverorange\Autoloader;

$package = new Package('silverorange/swat');

$package->addRule(new Rule('exceptions', 'SwatDB', 'Exception'));
$package->addRule(new Rule('', 'SwatDB'));
$package->addRule(new Rule('exceptions', 'SwatI18N', 'Exception'));
$package->addRule(new Rule('', 'SwatI18N'));
$package->addRule(new Rule('exceptions', 'Swat', 'Exception'));
$package->addRule(new Rule('', 'Swat'));

Autoloader::addPackage($package);

?>
