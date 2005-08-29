<?php

require_once 'Swat/SwatEntry.php';
require_once 'Swat/SwatString.php';

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

		$value = SwatString::toFloat($this->value);

		if ($value === null) {
			$msg = Swat::_('The %s field must be a number.');
			$this->addMessage(new SwatMessage($msg, SwatMessage::ERROR));
		} else
			$this->value = $value;	
	}

	protected function getDisplayValue()
	{
		$lc = localeconv();

		$decimal_pos = strpos($this->value, '.');
		$decimals = ($decimal_pos !== false) ?
			strlen($this->value) - $decimal_pos - 1 : 0;

		if (is_numeric($this->value))
			return number_format($this->value, $decimals,
				$lc['decimal_point'], $lc['thousands_sep']);
		else
			return $this->value;
	}
}

?>
