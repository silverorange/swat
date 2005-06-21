<?php

/**
 * All containers of cell renders must implement this interface
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see SwatDetailsViewField, SwatTableViewColumn
 */
interface SwatCellRendererContainer
{
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

	/**
	 * Adds a cell renderer to this container with a data-field property
	 * mapping array
	 *
	 * The data-field property mapping array is of the form:
	 *    array($renderer_property => $field_name);
	 *
	 * @param SwatCellRenderer $renderer a reference to the cell renderer to
	 *                                    add to this container.
	 * @param array $fields the data-field property mappings to add to the cell
	 *                       renderer.
	 */
	public function addRendererWithMappings(SwatCellRenderer $renderer,
		$mappings = null);

	/**
	 * Adds data-field property mappings to a cell renderer in this container
	 *
	 * The data-field property mapping array is of the form:
	 *    array($renderer_property => $field_name);
	 *
	 * @param string $renderer_id the unique identifier of the cell renderer to
	 *                             add data-field property mappings to.
	 * @param array $fields the data-field property mappings to add to the cell
	 *                       renderer.
	 */
	public function addRendererMappings($renderer_id, $mappings = null);

	/**
	 * Sets the data-field property mappings for a cell renderer in this
	 * container
	 *
	 * The data-field property mapping array is of the form:
	 *    array($renderer_property => $field_name);
	 *
	 * @param string $renderer_id the unique identifier of the cell renderer to
	 *                             add data-field property mappings to.
	 * @param array $fields the data-field property mappings to add to the cell
	 *                       renderer.
	 */
	public function setRendererMappings($renderer_id, $mappings = null);
}

?>
