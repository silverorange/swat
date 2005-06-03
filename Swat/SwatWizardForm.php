<?php

require_once('Swat/SwatForm.php');
require_once('Swat/SwatWizardStep.php');
require_once('Swat/SwatWizardPostStateStore.php');

/**
 * A wizard-like form with steps
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatWizardForm extends SwatForm {

	/**
	 * The current step of the wizard
	 *
	 * @var integer
	 */
	public $step;

	private $state_store = null;
	private $steps = array();
	private $navigation = array();

	public function init() {
		foreach ($this->children as $child) {
			if ($child instanceof SwatWizardStep)
				$this->steps[] = $child;
			elseif ($child instanceof SwatWizardNavigation)
				$this->navigation[] = $child;
		}
	}

	public function process() {
		if (!isset($_POST['process']) || $_POST['process'] != $this->id)
			return false;

		if ($this->state_store === null)
			$this->state_store = new SwatWizardPostStateStore($this);
		
		$this->state_store->init();
	
		$this->processed = true;
		$this->processHiddenFields();

		$step = $this->getHiddenField('step');
		$this->step = ($step === null) ? 0 : intval($step);
		
		foreach ($this->steps as $step) {
			if ($step->step == $this->step) {
				$step->process();
				$this->state_store->updateState($step->getWidgetStates());
			} else
				$step->setWidgetStates($this->state_store->getState());
		}
	
		$this->step = $this->getNextStep();
		
		return true;
	}

	public function display() {
		foreach ($this->steps as $step)
			$step->visible = ($step->step == $this->step) ? true : false;
		
		$this->addHiddenField('step', $this->step);
		
		parent::display();
	}

	private function getNextStep() {
		$next_step = $this->step;

		foreach ($this->navigation as $navigation) {
			$next_step = $navigation->getNextStep();
			if ($next_step !== null)
				break;
		}

		if ($next_step === null || ($this->hasMessage() && $next_step > $this->step))
			return $this->step;
		else
			return $next_step;
	}
	
	/**
	 * Get the total number of steps in the wizard
	 *
	 * @return int Total steps
	 */
	public function getStepCount() {
		return count($this->steps);
	}
	
	/**
	 * Get the step title (if not set, "Step X")
	 *
	 * @param int $step Step number
	 *
	 * @return int Step title
	 */
	public function getStepTitle($step) {
		if ($this->steps[$step]->title !== null)
			return $this->steps[$step]->title;
		else
			return sprintf(_S("Step %d"), $step + 1);
	}

	/**
	 * Set state storage method
	 *
	 * @param int $state_store A {@link SwatWizardStateStore} that specifies
	 *        how the data is stored between steps.
	 */
	public function setStateStore($state_store) {
		if (!$state_store instanceof SwatWizardStateStore)
			throw new SwatException('SwatWizardForm: A state store must be a type '.
				'of SwatWizardStateStore');
		
		$this->state_store = $state_store;	
	}
}

?>
