<?php

require_once 'Swat/SwatNumericEntry.php';
require_once 'Swat/SwatString.php';

/**
 * A float entry widget
 *
 * @package   Swat
 * @copyright 2004-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatFloatEntry extends SwatNumericEntry
{
	// {{{ public function process()

	/**
	 * Checks to make sure value is a number
	 *
	 * If the value of this widget is not a number then an error message is
	 * attached to this widget.
	 */
	public function process()
	{
		parent::process();

		if ($this->value === null)
			return;

		$float_value = $this->getNumericValue();

		if ($float_value === null) {
			$msg = Swat::_('The %s field must be a number.');
			$this->addMessage(new SwatMessage($msg, SwatMessage::ERROR));
		} else {
			$this->value = $float_value;
		}
	}

	// }}}
	// {{{ protected function getDisplayValue()

	protected function getDisplayValue()
	{
		$lc = localeconv();
		$decimal_pos = strpos($this->value, $lc['decimal_point']);
		$decimals = ($decimal_pos !== false) ?
			strlen($this->value) - $decimal_pos - strlen($lc['decimal_point']) : 0;

		if (is_numeric($this->value))
			return SwatString::numberFormat($this->value, $decimals, null,
				$this->show_thousands_separator);
		else
			return $this->value;
	}

	// }}}
	// {{{ protected function getNumericValue()

	/**
	 * Gets the float value of this widget
	 *
	 * This allows each widget to parse raw values how they want to get numeric
	 * values.
	 *
	 * @return mixed the numeric value of this entry widget of null if no
	 *                numeric value is available.
	 */
	protected function getNumericValue()
	{
		$value = trim($this->value);
		return SwatString::toFloat($value);
	}

	// }}}
}

?>
