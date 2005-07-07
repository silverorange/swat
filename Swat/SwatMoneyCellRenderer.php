<?php

require_once 'Swat/SwatCellRenderer.php';
require_once 'Swat/SwatCurrency.php';

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
	 * @var string
	 */
	public $value;

	// }}}
	// {{{ public function render()
	
	/**
	 * Renders the contents of this cell
	 *
	 * @see SwatCellRenderer::render()
	 */
	public function render()
	{
		echo SwatString::moneyFormat($this->value, $this->locale);	
	}

	// }}}
}

?>
