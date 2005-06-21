<?php

require_once 'Swat/SwatCellRendererContainer.php';

/**
 * All containers of cell renders that do data-field-to-renderer-property
 * mapping must implement this interface
 *
 * @package   Swat
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see SwatCellRendererContainer
 */
interface SwatMappingCellRendererContainer extends SwatCellRendererContainer
{
	/**
	 * Adds a cell renderer to this container with a data-field property
	 * mapping array
	 *
	 * The data-field property mapping array is of the form:
	 *    array($renderer_property => $field_name);
	 *
	 * @param SwatCellRenderer $renderer a reference to the cell renderer to
	 *                                    add to this container.
	 * @param array $mappings the data-field property mappings to add to the
	 *                         cell renderer.
	 */
	public function addRendererWithMappings(SwatCellRenderer $renderer,
		$mappings);

	/**
	 * Adds data-field property mappings to a cell renderer in this container
	 *
	 * The data-field property mapping array is of the form:
	 *    array($renderer_property => $field_name);
	 *
	 * @param string $renderer_id the unique identifier of the cell renderer to
	 *                             add data-field property mappings to.
	 * @param array $mappings the data-field property mappings to add to the
	 *                         cell renderer.
	 */
	public function addRendererMappings($renderer_id, $mappings);

	/**
	 * Sets the data-field property mappings for a cell renderer in this
	 * container
	 *
	 * The data-field property mapping array is of the form:
	 *    array($renderer_property => $field_name);
	 *
	 * @param string $renderer_id the unique identifier of the cell renderer to
	 *                             set the data-field property mappings for.
	 * @param array $mappings the data-field property mappings to set for the
	 *                         cell renderer.
	 */
	public function setRendererMappings($renderer_id, $mappings);

	/**
	 * Gets the data-field property mappings of a cell renderer in this
	 * container
	 *
	 * The data-field property mapping array is of the form:
	 *    array($renderer_property => $field_name);
	 *
	 * @param string $renderer_id the unique identifier of the cell renderer to
	 *                             get the data-field property mappings from.
	 *
	 * @return array the data-field property mappings of the cell renderer.
	 */
	public function getRendererMappings($renderer_id);
}

?>
