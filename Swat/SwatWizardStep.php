<?php

require_once('Swat/SwatContainer.php');
require_once('Swat/SwatHtmlTag.php');

/**
 * A step container used for wizards
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatWizardStep extends SwatContainer {

	/**
	 * Visibility of the step
	 *
	 * @var boolean
	 */
	public $visible = false;

	/**
	 * Step number (read only)
	 *
	 * @var integer
	 */
	public $step; //read only

	/**
	 * Title of the step (optional)
	 *
	 * @var string
	 */
	public $title = null;
	
	public function __construct() {
		static $step = 0;
		$this->step = $step;
		$step++;
	}

	public function display() {
		if ($this->visible)
			parent::display();
	}
	
	public function getWidgetStates() {
		return $this->getDescendantStates();
	}
	
	public function setWidgetStates($states) {
		$this->setDescendantStates($states);
	}
}

?>
