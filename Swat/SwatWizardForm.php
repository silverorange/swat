<?php
require_once('Swat/SwatForm.php');
require_once('Swat/SwatStep.php');

/**
 * A wizard-like form with steps
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatFormSteps extends SwatForm {

	public $step;

	public function init() {
		parent::init();

		$step = $this->getHiddenField('step');
		$this->step = ($step === null) ? 0 : intval($step);
	}

	public function display() {
		foreach ($this->children as $child) {
			if ($child instanceof SwatStep && $child->step == $this->step)
				$child->visible = true;
			elseif ($child instanceof SwatStep) {
				$child->visible = false;
				$this->addHiddenFields($child);
			}
		}
		
		$this->addHiddenField('step', $this->step);
		
		parent::display();	
	}

	public function process() {
		if (!isset($_POST['process']) || $_POST['process'] != $this->name)
			return false;

		$this->processed = true;
		
		$this->processHiddenFields();

		foreach ($this->children as &$child)
			if ($child instanceof SwatStep && $child->step == $this->step)
				$child->process();
			elseif ($child instanceof SwatStep)
				$this->initHiddenFields($child);
		

		if (!$this->hasMessage()) 
			$this->step++;
			//TODO: make this work with back/forward functionality
		
		return true;
	}

	private function addHiddenFields($step) {
		$step_state = $step->getDescendantStates();
		
		foreach ($step_state as $name => $value)
			$this->addHiddenField($name, serialize($value));	
	}

	private function initHiddenFields($step) {
		$step_state = $step->getDescendantStates();
	
		print_r($this->hidden_fields);
	
		$values = array();
		foreach ($step_state as $name => $value)
			echo $this->getHiddenField($name);
			//$values['name'] = unserialize($this->getHiddenField($name));
		
		$step->setDescendantStates($values);
	}
}
?>
