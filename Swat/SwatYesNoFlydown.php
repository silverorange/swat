<?php
require_once('Swat/SwatFlydown.php');

/**
 * A flydown (aka combo-box) selection widget for a Yes/No option.
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2005
 */
class SwatYesNoFlydown extends SwatFlydown {

	const BLANK = 0;
	const NO = 1;
	const YES = 2;
	
	public function display() {
		$this->options = array(SwatYesNoFlydown::BLANK => '',
		                       SwatYesNoFlydown::NO => _S("No"),
		                       SwatYesNoFlydown::YES => _S("Yes"));

		parent::display();
	}

	public function getValueAsBoolean() {
		switch ($this->value) {
			case SwatYesNoFlydown::NO:
				return false;

			case SwatYesNoFlydown::YES:
				return true;

			default:
				return null;
		}
	}

}

?>
