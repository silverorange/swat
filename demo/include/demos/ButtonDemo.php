<?php

require_once 'Demo.php';

/**
 * A demo using a button widgets
 *
 * This PHP is used to add a delay when submitting the form using throbber
 * buttons.
 *
 * @package   SwatDemo
 * @copyright 2007-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class ButtonDemo extends Demo
{


	public function buildDemoUI(SwatUI $ui)
	{
		$submit  = $ui->getWidget('submit_throbber_button');
		$confirm = $ui->getWidget('confirm_throbber_button');

		$submit->process();
		$confirm->process();

		if ($submit->hasBeenClicked() || $confirm->hasBeenClicked()) {
			sleep(2);
		}
	}

}

?>
