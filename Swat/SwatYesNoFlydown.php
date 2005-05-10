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

	const NO = 1;
	const YES = 2;
	
	public function display() {
		$this->options = array(self::NO => _S("No"),
		                       self::YES => _S("Yes"));

		parent::display();
	}

	public function getValueAsBoolean() {
		switch ($this->value) {
			case self::NO:
				return false;

			case self::YES:
				return true;

			default:
				return null;
		}
	}

}

?>
