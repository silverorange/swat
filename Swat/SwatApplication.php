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

}
