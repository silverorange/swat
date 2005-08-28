<?php

require_once 'Swat/SwatEntry.php';

/**
 * A float entry widget
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
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
			$msg = Swat::_('The %s field must be a number.');
			$this->addMessage(new SwatMessage($msg, SwatMessage::ERROR));
		}
	}
}

?>
