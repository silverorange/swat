<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatObject.php');

/**
 * Base class for a page.
 */
abstract class SwatPage extends SwatObject {

	/**
	 * @var title Title of the page.
	 */
	public $title = '';

	function __construct() {

	}

}
