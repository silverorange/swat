<?php

require_once 'Swat/SwatNumericEntry.php';
require_once 'Swat/SwatString.php';

/**
 * An integer entry widget
 *
 * @package   Swat
 * @copyright 2004-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatIntegerEntry extends SwatNumericEntry
{
	// {{{ public function process()

	/**
	 * Checks to make sure value is an integer
	 *
	 * If the value of this widget is not an integer then an error message is
	 * attached to this widget.
	 */
	public function process()
	{
		parent::process();

		if ($this->value === null)
			return;

		$int_value = $this->getNumericValue();

		if ($int_value === null) {
			$msg = Swat::_('The %s field must be an integer.');
			$this->addMessage(new SwatMessage($msg, SwatMessage::ERROR));
		} else {
			$this->value = $int_value;
		}
	}

	// }}}
	// {{{ protected function getDisplayValue()

	protected function getDisplayValue()
	{
		if (is_int($this->value))
			return SwatString::numberFormat($this->value, 0, null,
				$this->show_thousands_separator);
		else
			return $this->value;
	}

	// }}}
	// {{{  protected function getNumericValue()
	
	/**
	 * Gets the numeric value of this widget
	 *
	 * This allows each widget to parse raw values how they want to get numeric
	 * values.
	 *
	 * @return mixed the numeric value of this entry widget of null if no
	 *                numeric value is available.
	 */
	 protected function getNumericValue()
	 {
		 return SwatString::toInteger($this->value);
	 }

	// }}}
}

?>
