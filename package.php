<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'PEAR/PackageFileManager2.php';

$version = '1.2.12';
$notes = <<<EOT
see ChangeLog
EOT;

$description =<<<EOT
Swat is a web application toolkit.

* An object oriented API
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
$package->setSummary('Swat web application toolkit');
$package->setDescription($description);
$package->setChannel('pear.silverorange.com');
$package->setPackageType('php');
$package->setLicense('LGPL', 'http://www.gnu.org/copyleft/lesser.html');

$package->setReleaseVersion($version);
$package->setReleaseStability('stable');
$package->setAPIVersion('0.0.1');
$package->setAPIStability('stable');
$package->setNotes($notes);

$package->addIgnore('package.php');
$package->addIgnore('yui/');

$package->addMaintainer('lead', 'nrf', 'Nathan Fredrickson', 'nathan@silverorange.com');
$package->addMaintainer('lead', 'gauthierm', 'Mike Gauthier', 'mike@silverorange.com');

$package->addReplacement('Swat/Swat.php', 'pear-config', '@DATA-DIR@', 'data_dir');

$package->setPhpDep('5.1.5');
$package->setPearinstallerDep('1.4.0');
$package->addPackageDepWithChannel('required', 'Date', 'pear.silverorange.com', '1.4.7so1');
$package->addPackageDepWithChannel('optional', 'MDB2', 'pear.php.net', '2.2.2');
$package->generateContents();

if (isset($_GET['make']) || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')) {
	$package->writePackageFile();
} else {
	$package->debugPackageFile();
}

?>
