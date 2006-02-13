<?php

require_once 'Swat/SwatWizardNavigation.php';
require_once 'Swat/SwatString.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';

/**
 * A proto-type test wizard navigation class that shows navigation buttons to
 * switch steps.
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatWizardNavigationSteps extends SwatWizardNavigation
{
	private $step_buttons;

	public function getNextStep()
	{
		$return = null;

		for ($i = 0; $i < $this->parent->getStepCount(); $i++) {
			$this->step_buttons[$i] = new SwatButton('nav_step'.$i);
			$this->step_buttons[$i]->process();
			$this->step_buttons[$i]->title = $this->parent->getStepTitle($i);
			if ($this->step_buttons[$i]->clicked)
				$return = $i;
		}

		return $return;
	}

	/**
	 * @throws SwatInvalidClassException
	 */
	public function display()
	{
		if (!$this->parent instanceof SwatWizardForm)
			throw new SwatInvalidClassException(
				'Must be a child of a SwatWizardForm',
				0, $this);

		$div = new SwatHtmlTag('div');
		$div->style = 'float:right;';
		$div->open();

		for ($i = 0; $i < $this->parent->getStepCount(); $i++)
			if ($i == $this->parent->step)
				echo SwatString::minimizeEntities($this->step_buttons[$i]->title), '<br />';
			elseif ($i < $this->parent->step || $i == ($this->parent->step + 1)) {
				$this->step_buttons[$i]->display();
				echo '<br />';
			}

		$div->close();
	}
}

?>
