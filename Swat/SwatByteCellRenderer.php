<?php

require_once 'Swat/SwatCellRenderer.php';
require_once 'Swat/SwatString.php';

/**
 * A byte cell renderer
 *
 * @package   Swat
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatByteCellRenderer extends SwatCellRenderer
{
	// {{{ public properties

	/**
	 * Value in bytes
	 * 
	 * @var float
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
		echo SwatString::minimizeEntities(
			SwatString::byteFormat($this->value));
	}

	// }}}
}

?>
