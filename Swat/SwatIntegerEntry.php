<?php

require_once 'Swat/SwatEntry.php';
require_once 'Swat/SwatString.php';

/**
 * An integer entry widget
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatIntegerEntry extends SwatEntry
{
	/**
	 * Creates a new integer entry widget
	 *
	 * Sets the input size to 7 by default.
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->size = 10;
	}

	/**
	 * Checks to make sure value is an integer
	 *
	 * If the value of this widget is not an integer then an error message is
	 * attached to this widget.
	 */
	public function process()
	{
		parent::process();

		if ($this->value !== null) {
			if (is_numeric($this->value) && $this->value == intval($this->value))
				$this->value = intval($this->value);
			else {
				$msg = Swat::_('The %s field must be an integer.');
				$this->addMessage(new SwatMessage($msg, SwatMessage::ERROR));
			}
		}
	}

	protected function getDisplayValue()
	{
		if (is_int($this->value))
			return SwatString::numberFormat($this->value);
		else
			return $this->value;
	}
}

?>
