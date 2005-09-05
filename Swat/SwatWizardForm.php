<?php

require_once 'Swat/SwatForm.php';
require_once 'Swat/SwatWizardStep.php';
require_once 'Swat/SwatWizardPostStateStore.php';

/**
 * A wizard-like form with steps
 *
 * Wizard forms are multi step forms generally used in task based interfaces.
 * Wizard forms require some sort of persistance framework. This framework is
 * provided by the {@link SwatWizardStateStore} class.
 *
 * @package   Swat
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatWizardForm extends SwatForm
{
	// {{{ public properties

	/**
	 * The current step of this wizard form
	 *
	 * @var integer
	 */
	public $step;

	// }}}
	// {{{ private properties

	/**
	 * The persistance object for this wizard form
	 *
	 * The state storage object remembers the state of all wizard pages even
	 * when the user is only viewing a particular page. If no state storage
	 * object is set, a SwatWizardPostStateStore object is created and used.
	 *
	 * @var SwatWizardStateStore
	 */
	private $state_store = null;

	/**
	 * The steps of this wizard
	 *
	 * Wizard steps are container objects that contain all the widgets of a
	 * wizard form to show on a particular step. They may be viewed as
	 * individual forms.
	 *
	 * @var array
	 *
	 * @see SwatWizardStep
	 */
	private $steps = array();

	/**
	 * The navigation of this wizard
	 *
	 * Wizard navigation objects allow users to go forwards and backwards in a
	 * wizard and to jump to specific steps.
	 *
	 * @var array
	 *
	 * @see SwatWizardNavigation
	 */
	private $navigation = array();

	// }}}
	// {{{ public function init()

	/**
	 * Initializes this wizard form
	 *
	 * Sets the steps of this form to all child widgets that are wizard steps
	 * and sets the navigation of this step to all children that are wizard
	 * navigation objects.
	 */
	public function init()
	{
		parent::init();

		foreach ($this->children as $child) {
			if ($child instanceof SwatWizardStep)
				$this->steps[] = $child;
			elseif ($child instanceof SwatWizardNavigation)
				$this->navigation[] = $child;
		}
	}

	// }}}
	// {{{ public function process()

	/**
	 * Processes this wizard form
	 *
	 * Processes each step. For the current step this stores the step state in
	 * the state storage object of this wizard form. For other steps this sets
	 * the state of the step to the stored state in the state storage object.
	 */
	public function process()
	{
		if (!isset($_POST['process']) || $_POST['process'] != $this->id)
			return false;

		// use a POST var type persistance framework by default
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
			} else {
				$step->setWidgetStates($this->state_store->getState());
			}
		}

		$this->step = $this->getNextStep();

		return true;
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this wizard form
	 *
	 * Sets whether each step of this wizard form is visible and then displays
	 * all child widgets. A hidden field with the number of the current step
	 * is added before this wizard form is displayed.
	 *
	 * Usually, only the current step of this wizard form is set to be visible
	 * here. Subclasses may chose to do things differently.
	 */
	public function display()
	{
		foreach ($this->steps as $step)
			$step->visible = ($step->step == $this->step) ? true : false;

		$this->addHiddenField('step', $this->step);

		parent::display();
	}

	// }}}
	// {{{ public function getStepCount()

	/**
	 * Gets the number of steps in this wizard form
	 *
	 * @return integer the number of steps in this wizard form.
	 */
	public function getStepCount()
	{
		return count($this->steps);
	}

	// }}}
	// {{{ public function getStepTitle()

	/**
	 * Gets the title of a step in this wizard form
	 *
	 * If the specified step does not have a title set by the developer, a
	 * generic title of "Step X" is used.
	 *
	 * @param integer $step the number of the step to get the title of.
	 *
	 * @return string the title of the step.
	 */
	public function getStepTitle($step)
	{
		if ($this->steps[$step]->title !== null)
			return $this->steps[$step]->title;
		else
			return sprintf(Swat::_('Step %d'), $step + 1);
	}

	// }}}
	// {{{ public function setStateStore()

	/**
	 * Set this wizard form's state storage object
	 *
	 * @param SwatWizardStateStore $state_store a state storeage object to use
	 *                                           for persistance in this wizard
	 *                                           form.
	 *
	 * @throws SwatException
	 */
	public function setStateStore($state_store)
	{
		if (!$state_store instanceof SwatWizardStateStore)
			throw new SwatException(__CLASS__.': The given state storage '.
				'object is not a SwatWizardStateStore.');

		$this->state_store = $state_store;
	}

	// }}}
	// {{{ private function getNextStep()

	/**
	 * Returns the number of the next step of this wizard form
	 *
	 * The number of the next step is calculated based on the number of the
	 * curent step.
	 *
	 * TODO: Someone with better knowledge of how this works write better
	 *       documentation for this method.
	 *
	 * @return integer the number of the next step in this wizard form.
	 */
	private function getNextStep()
	{
		$next_step = $this->step;

		foreach ($this->navigation as $navigation) {
			$next_step = $navigation->getNextStep();
			if ($next_step !== null)
				break;
		}

		if ($next_step === null ||
			($this->hasMessage() && $next_step > $this->step))
				return $this->step;
		else
			return $next_step;
	}

	// }}}
}

?>
