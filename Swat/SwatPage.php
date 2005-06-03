<?php

require_once('Swat/SwatObject.php');

/**
 * Base class for a page
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
abstract class SwatPage extends SwatObject {

	/**
	 * Title of the page
	 *
	 * @var string
	 */
	public $title = '';

	/**
	 * Layout to use to display this page
	 *
	 * @var string 
	 */
	public $layout = 'default';

	/**
	 * Application object
	 * 
	 * A reference to the {@link SwatApplication} object that created this page
	 *
	 * @var app
	 */
	public $app = null;

	function __construct() {

	}

}

?>
