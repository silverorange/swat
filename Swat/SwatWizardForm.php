<?php
require_once('Swat/SwatForm.php');
require_once('Swat/SwatWizardStep.php');

/**
 * A wizard-like form with steps
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatWizardForm extends SwatForm {

	public $step;
	private $wizard_state = array();

	public function display() {
		foreach ($this->children as $child) {
			if ($child instanceof SwatWizardStep && $child->step == $this->step)
				$child->visible = true;
			elseif ($child instanceof SwatWizardStep)
				$child->visible = false;
		}
		
		$this->addHiddenField('wizard_state', serialize($this->wizard_state));
		$this->addHiddenField('step', $this->step);
		
		parent::display();	
	}

	public function process() {
		if (!isset($_POST['process']) || $_POST['process'] != $this->name)
			return false;

		if (isset($_POST['wizard_state']))
			$this->wizard_state = unserialize($_POST['wizard_state']);

		$this->processed = true;
		$this->processHiddenFields();

		$step = $this->getHiddenField('step');
		$this->step = ($step === null) ? 0 : intval($step);
		
		foreach ($this->children as &$child) {
			if ($child instanceof SwatWizardStep && $child->step == $this->step) {
				$child->process();
				$this->wizard_state = array_merge($this->wizard_state, $child->getStepStates());
			} elseif ($child instanceof SwatWizardStep)
				$child->setStepStates($this->wizard_state);
		}

		if (!$this->hasMessage()) 
			if ($this->step == 1)
				$this->step--;
			else
				$this->step++;
			//TODO: make this work with back/forward functionality
		
		return true;
	}

}
?>
