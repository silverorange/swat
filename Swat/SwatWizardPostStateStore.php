<?php

require_once('Swat/SwatWizardStateStore.php');

/**
 * A class to store the state of a wizard form in Post vars
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatWizardPostStateStore extends SwatWizardStateStore {

	private $form;

	public function __construct($form) {
		$this->form = $form;
	}

	public function init() {
		if (isset($_POST['_wizard_state']))
			$this->state = unserialize($_POST['_wizard_state']);
	}

	public function getState() {
		return $this->state;
	}
	
	public function updateState($state) {
		$this->state = array_merge($this->state, $state);

		$this->form->addHiddenField('_wizard_state', serialize($this->state));
	}
}

?>
