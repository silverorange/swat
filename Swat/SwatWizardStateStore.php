<?php

require_once 'Swat/SwatObject.php';

/**
 * A class to store the state of a wizard form
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatWizardStateStore extends SwatObject
{
	protected $state = array();

	public function __construct()
	{
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
	 *
	 * @param array The current state of the wizard widgets
	 */
	abstract function updateState($state);
}

?>
