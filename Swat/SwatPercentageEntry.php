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
		if (is_float($this->value))
			return ($this->value * 100).'%';
		else
			return $this->value;
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
		$value = str_replace('%','',$value);
		$value = parent::getNumericValue($value);
		if ($value != null)
			$value = $value / 100;
		return $value;
	}

	// }}}
}

?>
