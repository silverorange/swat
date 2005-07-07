<?php

require_once 'Swat/SwatCellRenderer.php';

/**
 * A text cell renderer
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTextCellRenderer extends SwatCellRenderer
{
	/**
	 * Cell value
	 *
	 * The textual content to place within this cell.
	 *
	 * @var string
	 */
	public $value = '';

	/**
	 * Renders the contents of this cell
	 *
	 * @see swatcellrenderer::render()
	 */
	public function render()
	{
		echo $this->value;
	}
}

?>
