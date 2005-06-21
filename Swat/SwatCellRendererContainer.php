<?php

/**
 * All containers of cell renders must implement this interface
 *
 * @package   Swat
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
interface SwatCellRendererContainer
{
	/**
	 * Adds a cell renderer to this container
	 *
	 * @param SwatCellRenderer $renderer a reference to the cell renderer to
	 *                                    add to this container.
	 */
	public function addRenderer($renderer);

	/**
	 * Gets all cell renderers in this container as a flat array
	 *
	 * @return array all the cell renderers in this container.
	 */
	public function getRenderers();

	/**
	 * Gets a cell renderer by it's unique identifier
	 *
	 * @param string $renderer_id the unique identifier of the cell renderer to
	 *                             get from this container.
	 *
	 * @return SwatCellRenderer a reference to the cell renderer.
	 */
	public function getRenderer($renderer_id);
}

?>
