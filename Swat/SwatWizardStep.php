<?php

require_once 'Swat/SwatContainer.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * A step container used for wizards
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
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
	public $step;

	/**
	 * Title of the step (optional)
	 *
	 * @var string
	 */
	public $title = null;
	

	public function __construct()
	{
		static $step = 0;
		$this->step = $step;
		$step++;
	}

	public function display()
	{
		if ($this->visible)
			parent::display();
	}

	public function getWidgetStates()
	{
		return $this->getDescendantStates();
	}

	public function setWidgetStates($states)
	{
		$this->setDescendantStates($states);
	}
}

?>
