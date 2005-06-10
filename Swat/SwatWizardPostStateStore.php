<?php

require_once 'Swat/SwatWizardStateStore.php';

/**
 * A class to store the state of a wizard form in HTTP Post vars
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatWizardPostStateStore extends SwatWizardStateStore
{

	private $form;

	public function __construct($form)
	{
		$this->form = $form;
	}

	public function init()
	{
		if (isset($_POST['_wizard_state']))
			$this->state = unserialize($_POST['_wizard_state']);
	}

	public function getState()
	{
		return $this->state;
	}

	public function updateState($state)
	{
		$this->state = array_merge($this->state, $state);

		$this->form->addHiddenField('_wizard_state', serialize($this->state));
	}
}

?>
