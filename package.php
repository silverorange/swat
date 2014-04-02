<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'PEAR/PackageFileManager2.php';

$version = '2.1.0';
$notes = <<<EOT
No release notes for you!
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
		'filelistgenerator' => 'file',
		'simpleoutput'      => true,
		'baseinstalldir'    => '/',
		'packagedirectory'  => './',
		'dir_roles'         => array(
			'Swat'         => 'php',
			'SwatDB'       => 'php',
			'SwatI18N'     => 'php',
			'www'          => 'data',
			'demo'         => 'data',
			'system'       => 'data',
			'dependencies' => 'data',
			'/'            => 'data',
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
$package->addReplacement('Swat/SwatUI.php', 'pear-config', '@DATA-DIR@', 'data_dir');

$package->setPhpDep('5.3.0');
$package->setPearinstallerDep('1.4.0');
$package->addExtensionDep('required', 'iconv');
$package->addExtensionDep('required', 'mbstring');
$package->addExtensionDep('required', 'intl');
$package->addPackageDepWithChannel('optional', 'Yui',         'pear.silverorange.com', '1.0.10');
$package->addPackageDepWithChannel('optional', 'MDB2',        'pear.php.net',          '2.2.2');
$package->addPackageDepWithChannel('optional', 'ReCaptcha',   'pear.silverorange.com', '1.0.0');
$package->addPackageDepWithChannel('optional', 'Net_IDNA',    'pear.silverorange.com', '0.7.2so1');
$package->addPackageDepWithChannel('required', 'Concentrate', 'pear.silverorange.com', '0.0.13');
$package->generateContents();

if (isset($_GET['make']) || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')) {
	$package->writePackageFile();
} else {
	$package->debugPackageFile();
}

?>
