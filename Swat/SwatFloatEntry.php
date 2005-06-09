<?php

require_once 'Swat/SwatEntry.php';

/**
 * A float entry widget
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatFloatEntry extends SwatEntry
{
	/**
	 * Initializes this widget
	 *
	 * Sets the input size to 10 by default.
	 */
	public function init()
	{
		$this->size = 10;
	}
	
	/**
	 * Checks to make sure value is a number
	 *
	 * If the value of this widget is not a number then an error message is
	 * attached to this widget.
	 */
	public function process()
	{
		parent::process();

		if (is_numeric($this->value))
			$this->value = floatval($this->value);
		else {
			$msg = _S("The %s field must be a number.");
			$this->addMessage(new SwatMessage($msg, SwatMessage::USER_ERROR));
		}
	}
}

?>
