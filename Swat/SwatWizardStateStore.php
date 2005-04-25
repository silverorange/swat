<?php
require_once('Swat/SwatObject.php');

/**
 * A class to store the state of a wizard form
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
abstract class SwatWizardStateStore extends SwatObject {

	protected $state = array();

	public function __construct() {

	}

	abstract function init();

	abstract function getState();
	
	abstract function updateState();
}
