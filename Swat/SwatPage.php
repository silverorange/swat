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

	/**
	 * @var app A reference to the SwatApplication object that created this page.
	 */
	public $app = null;

	function __construct() {

	}

	/**
	 * Get the layout.
	 * @return string Name of the layout to use.
	 */
	public function getLayout() {
		return 'default';
	}

}
