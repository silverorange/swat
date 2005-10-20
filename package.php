<?php

require_once 'PEAR/PackageFileManager2.php';

$version = '0.0.1';
$notes = <<<EOT
- initial test package
EOT;

$description =<<<EOT
Swat is a web application toolkit.

* An OO-style API
* A set of user-interface widgets
EOT;

$package = new PEAR_PackageFileManager2();
PEAR::setErrorHandling(PEAR_ERROR_DIE);

$result = $package->setOptions(
	array(
		'filelistgenerator' => 'svn',
		'simpleoutput'      => true,
		'baseinstalldir'    => '/',
		'packagedirectory'  => './',
		'dir_roles'         => array(
			'www' => 'data',
			'demo' => 'data',
		),
	)
);

$package->setPackage('Swat');
$package->setSummary('swat web application toolkit');
$package->setDescription($description);
$package->setChannel('pear.silverorange.com');
$package->setPackageType('php');
$package->setLicense('LGPL', 'http://www.gnu.org/copyleft/lesser.html');

$package->setReleaseVersion($version);
$package->setReleaseStability('beta');
$package->setAPIVersion('0.0.1');
$package->setAPIStability('beta');
$package->setNotes($notes);

$package->addIgnore('package*.php');
$package->addIgnore('package*.xml');
$package->addIgnore('demo');

$package->addMaintainer('lead', 'nrf', 'Nathan Fredrickson', 'nathan@silverorange.com');
$package->addMaintainer('lead', 'gauthierm', 'Mike Gauthier', 'mike@silverorange.com');

$package->setPhpDep('5.0.5');
$package->setPearinstallerDep('1.4.0');
$package->generateContents();

//$package->addReplacement('package-info', 'pear-config', '@package_version@', 'version');

if (isset($_GET['make']) || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')) {
	$package->writePackageFile();
} else {
	$package->debugPackageFile();
}

?>
