<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatCellRenderer.php';
require_once 'SwatI18N/SwatI18NLocale.php';

/**
 * A numeric cell renderer
 *
 * @package   Swat
 * @copyright 2006-2007 silverorange
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
	 * Number of digits to display after the decimal point
	 *
	 * If null, the native number of digits displayed by PHP is used. The native
	 * number of digits could be a relatively large number of digits for uneven
	 * fractions.
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

		parent::render();

		echo $this->getDisplayValue();
	}

	// }}}
	// {{{ protected function getDisplayValue()

	public function getDisplayValue()
	{
		$value = $this->value;

		if (is_numeric($this->value)) {
			$locale = SwatI18NLocale::get();
			$value = $locale->formatNumber($this->value, $this->precision);
		}

		return $value;
	}

	// }}}
}

?>
