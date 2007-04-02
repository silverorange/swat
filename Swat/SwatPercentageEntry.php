<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatFloatEntry.php';

/**
 * A percentage entry widget
 *
 * @package    Swat
 * @copyright  2007 silverorange
 * @lisence    http://www.gnu.org/copyleft/lesser.html LGPL Lisence 2.1
 */ 
class SwatPercentageEntry extends SwatFloatEntry
{
	// {{{ public function __construct()
	
	/**
	 * Constructs the widget
	 *
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->size = 5;
	}

	// }}}
	// {{{ public function process()

	/**
	 * Checks to make sure that the value is a percentage value
	 *
	 * If the value of the widget is not valid then a message will be
	 * displayed showing the user the type of error that took place.
	 */
	public function process()
	{
	parent::process();

		if (($this->value >= 0) and ($this->value <= 100))
			$this->value = $this->value / 100;
		else {
			$message = Swat::_('Please use a number between 0 and 100');
			$this->addMessage(new SwatMessage($message, SwatMessage::ERROR));
		}
		$this->value = $this->value;
	}

	// }}}
	// {{{ protected function getDisplayValue

	/**
	 * Returns a value for this widget
	 *
	 * The method returns a value to be displayed in the widget
	 *
	 * @return string the final percentage value
	 */
	protected function getDisplayValue()
	{
		if (is_float($this->value) and ($this->value >= 0) and ($this->value <= 100))
			return ($this->value * 100).'%';
	}

	// }}}
	// {{{ protected function getNumericValue()
	
	/**
	 * Gets the float value of this widget
	 * 
	 * This allows each widget to parse raw values and turn them into floats
	 *
	 * @param string $value the raw value to use to get the numeric value.
	 *
	 * @return mixed the numeric value of this entry widget or null if no 
	 *							no numeric value is available.
	 */ 
	protected function getNumericValue($value)
	{
		$value = trim($value);
		$value = str_replace('%','',$this->value);
		return SwatString::toFloat($value);
	}

	// }}}
}

?>	
