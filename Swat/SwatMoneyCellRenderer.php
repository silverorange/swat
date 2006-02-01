<?php

require_once 'Swat/SwatCellRenderer.php';
require_once 'Swat/SwatString.php';

/**
 * A currency cell renderer
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatMoneyCellRenderer extends SwatCellRenderer
{
	// {{{ public properties

	/**
	 * Optional locale for currency format
	 *
	 * @var string
	 */
	public $locale = null;

	/**
	 * Monetary value
	 * 
	 * @var float
	 */
	public $value;

	/**
	 * Whether to display currency unit
	 *
	 * If true, displays the international currency unit
	 *
	 * @var boolean
	 */
	public $display_currency = false;

	// }}}
	// {{{ public function render()

	/**
	 * Renders the contents of this cell
	 *
	 * @see SwatCellRenderer::render()
	 */
	public function render()
	{
		echo SwatString::minimizeEntities(
			SwatString::moneyFormat(
				$this->value, $this->locale, $this->display_currency));
	}

	// }}}
	// {{{ public function getTdAttributes()

	/**
	 * Gets TD-tag attributes
	 *
	 * @return array an array of attributes to apply to the TD tag of this cell
	 *                renderer.
	 *
	 * @see SwatCellRenderer::getTdAttributes()
	 */
	public function getTdAttributes()
	{
		return array('class' => 'swat-money-cell-renderer');
	}

	// }}}
	// {{{ public function getThAttributes()

	/**
	 * Gets TH-tag attributes
	 *
	 * @return array an array of attributes to apply to the TH tag in the
	 *                table header for this cell renderer.
	 *
	 * @see SwatCellRenderer::getThAttributes()
	 */
	public function getThAttributes()
	{
		return array('class' => 'swat-money-cell-renderer');
	}

	// }}}
}

?>
