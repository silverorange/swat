<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatObject.php');

/**
 * Base class for a web application.
 */
abstract class SwatApplication extends SwatObject {

	/**
	 * Get the page object.
	 * Subclasses should implement logic here to decide which page subclass to
	 * instantiate, then return a SwatPage descenedant.
	 * @return SwatPage A subclass of SwatPage is returned.
	 */
	abstract public function getPage();

	function __construct() {

	}

	const VAR_POST    = 1;
	const VAR_GET     = 2;
	const VAR_REQUEST = 4;
	const VAR_COOKIE  = 8;
	const VAR_SERVER  = 16;
	const VAR_SESSION = 32;
	const VAR_FILES   = 64;
	const VAR_ENV     = 128;

	/**
	 * Initialize a variable.
	 * Initilizes a local variable with a value from one of the PHP
	 * global arrays.
	 * @param $name string The name of the variable to lookup.
	 * @param $types int Bitwise combination of SwatApplication::VAR_*
	 * constansts.
	 * @param $default mixed Value to return if variable is not found in
	 * the global arrays.
	 * @return mixed The value of the variable.
	 */
	function initVar($name, $types = 0, $default = 0) {
		if ($types == 0)
			$types = SwatApplication::VAR_POST | SwatApplication::VAR_GET;
	
		if (($types & SwatApplication::VAR_POST != 0)
			&& isset($_POST[$name]))
				return $_POST[$name];

		elseif (($types & SwatApplication::VAR_GET != 0) 
			&& isset($_GET[$name]))
				return $_GET[$name];

		/*
		elseif ((intval($types)&GA_REQUEST) && isset($_REQUEST[$var])) return $_REQUEST[$var];
		elseif ((intval($types)&GA_COOKIE) && isset($_COOKIE[$var])) return $_COOKIE[$var];
		elseif ((intval($types)&GA_SERVER) && isset($_SERVER[$var])) return $_SERVER[$var];
		elseif ((intval($types)&GA_SESSION) && isset($_SESSION[$var])) return $_SESSION[$var];
		elseif ((intval($types)&GA_FILES) && isset($_FILES[$var])) return $_FILES[$var];
		elseif ((intval($types)&GA_ENV) && isset($_ENV[$var])) return $_ENV[$var];
		*/
		else return $default;
	}

}
