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
		if ($this->precision === null) {
			$lc = localeconv();
			$decimal_pos = strpos($this->value, $lc['decimal_point']);
			$decimals = ($decimal_pos !== false) ?
				strlen($this->value) - $decimal_pos - strlen($lc['decimal_point']) : 0;
		} else {
			$decimals = $this->precision;
		}

		if (is_numeric($this->value))
			return SwatString::numberFormat($this->value, $decimals);
		else
			return $this->value;
	}

	// }}}
}

?>
