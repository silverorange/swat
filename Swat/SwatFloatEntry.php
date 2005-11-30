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
	 * Creates a new float entry widget
	 *
	 * Sets the input size to 10 by default.
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
	 * Checks to make sure value is a number
	 *
	 * If the value of this widget is not a number then an error message is
	 * attached to this widget.
	 */
	public function process()
	{
		parent::process();

		if ($this->value !== null) {
			$float_value = SwatString::toFloat($this->value);

			if ($float_value === null) {
				$msg = Swat::_('The %s field must be a number.');
				$this->addMessage(new SwatMessage($msg, SwatMessage::ERROR));
			} else {
				$this->value = $float_value;
			}
		}
	}

	protected function getDisplayValue()
	{
		$lc = localeconv();
		$decimal_pos = strpos($this->value, $lc['decimal_point']);
		$decimals = ($decimal_pos !== false) ?
			strlen($this->value) - $decimal_pos - strlen($lc['decimal_point']) : 0;

		if (is_numeric($this->value))
			return SwatString::numberFormat($this->value, $decimals);
		else
			return $this->value;
	}
}

?>
