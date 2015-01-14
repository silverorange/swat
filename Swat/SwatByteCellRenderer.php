<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatCellRenderer.php';
require_once 'Swat/SwatString.php';

/**
 * A cell renderer for rendering base-2 units of information
 *
 * This cell renderer should be used for displaying things such as file and
 * memory sizes.
 *
 * @package   Swat
 * @copyright 2006-2015 silverorange
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
		if (!$this->visible)
			return;

		parent::render();

		echo SwatString::minimizeEntities(
			SwatString::byteFormat($this->value));
	}

	// }}}
}

?>
