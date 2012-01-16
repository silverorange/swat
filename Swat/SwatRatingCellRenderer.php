<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatNumericCellRenderer.php';
require_once 'Swat/SwatString.php';

/**
 * A rating cell renderer
 *
 * @package   Swat
 * @copyright 2010 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatRatingCellRenderer extends SwatNumericCellRenderer
{
	// {{{ constants

	const ROUND_FLOOR = 1;
	const ROUND_CEIL  = 2;
	const ROUND_UP    = 3;

	// }}}
	// {{{ public properties

	/**
	 * Maximum value a rating can be.
	 *
	 * @var integer
	 */
	public $maximum_value = 5;

	/**
	 * Number of digits to display after the decimal point
	 *
	 * If null, the native number of digits displayed by PHP is used. The native
	 * number of digits could be a relatively large number of digits for uneven
	 * fractions.
	 *
	 * @var integer
	 */
	public $round_mode = self::ROUND_FLOOR;

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

		SwatCellRenderer::render();

		if ($this->value !== null) {
			$value      = $this->getDisplayValue();
			$difference = $this->maximum_value-$value;

			echo str_repeat('★', $value);

			if ($difference > 0) {
				echo str_repeat('☆', $difference);
			}
		}
	}

	// }}}
	// {{{ protected function getDisplayValue()

	public function getDisplayValue()
	{
		switch ($this->round_mode) {
		case self::ROUND_FLOOR:
			$value = floor($this->value);
			break;

		case self::ROUND_CEIL:
			$value = ceil($this->value);
			break;

		case self::ROUND_UP:
			$value = round($this->value, $this->precision);
			break;
		}

		return $value;
	}

	// }}}
}
