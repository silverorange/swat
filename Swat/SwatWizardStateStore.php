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

	/**
	 * Initiate the states of the wizard steps
	 */
	abstract function init();

	/**
	 * Return the current state of the wizard 
	 */
	abstract function getState();
	
	/**
	 * Update the state of the wizard steps
	 */
	abstract function updateState($state);
}

?>
