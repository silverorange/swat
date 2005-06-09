<?php

require_once 'Swat/SwatFlydown.php';

/**
 * A flydown (aka combo-box) selection widget for a Yes/No option.
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2005
 */
class SwatYesNoFlydown extends SwatFlydown
{
	const NO = 1;
	const YES = 2;
	
	/**
	 * Initializes this yes/no flydown
	 *
	 * Sets the options of this flydown to be yes and no.
	 */
	public function init() {
		$this->options = array(self::NO => _S("No"),
		                       self::YES => _S("Yes"));

		parent::init();
	}

	/**
	 * Gets the value of this yes/no flywodn as a boolean
	 *
	 * If the value is not set, this methods returns null.
	 *
	 * @return boolean the value of this yes/no flywdown.
	 */
	public function getValueAsBoolean()
	{
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
