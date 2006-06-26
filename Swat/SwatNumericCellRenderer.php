<?php

require_once 'Swat/SwatCellRenderer.php';
require_once 'Swat/SwatString.php';

/**
 * A numeric cell renderer
 *
 * @package   Swat
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatNumericCellRenderer extends SwatCellRenderer
{
	// {{{ public properties

	/**
	 * Value can be either a float or an integer
	 * 
	 * @var float
	 */
	public $value;

	/**
	 * Precision
	 *
	 * Optionally round the value to a certain precision
	 * 
	 * @var integer
	 */
	public $precision = null;

	// }}}
	// {{{ public function render()

	/**
	 * Renders the contents of this cell
	 *
	 * @see SwatCellRenderer::render()
	 */
	public function render()
	{
		if (!$this->visible)
			return;

		echo $this->getDisplayValue();
	}

	// }}}
	// {{{ protected function getDisplayValue()

	public function getDisplayValue()
	{
		if (is_numeric($this->value))
			return SwatString::numberFormat($this->value, $this->precision);
		else
			return $this->value;
	}

	// }}}
}

?>
