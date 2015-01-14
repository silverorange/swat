<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'SwatObject.php';
require_once 'SwatCellRenderer.php';
require_once 'SwatCellRendererMapping.php';
require_once 'Swat/exceptions/SwatException.php';
require_once 'Swat/exceptions/SwatObjectNotFoundException.php';

/**
 * A collection of cell renderers with associated datafield-property mappings
 *
 * @package   Swat
 * @copyright 2005-2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatCellRendererSet extends SwatObject implements Iterator, Countable
{
	// {{{ private properties

	/**
	 * Cell renderers of this set indexed numerically
	 *
	 * @var array
	 */
	private $renderers = array();

	/**
	 * Cell renderers of this set indexed by id
	 *
	 * @var array
	 */
	private $renderers_by_id = array();

	/**
	 * Cell renderer data-mappings of the renderers of this set
	 *
	 * This array is indexed by cell renderer object hashes for quick retrieval.
	 * Array values are numerically indexed arrays of
	 * {@link SwatCellRendererMapping} objects.
	 *
	 * @var array
	 */
	private $mappings = array();

	/**
	 * The current index of the iterator interface
	 *
	 * @var integer
	 */
	private $current_index = 0;

	/**
	 * Whether or not data-mappings have been applied to this cell-renderer set
	 *
	 * @var boolean
	 */
	private $mappings_applied = false;

	// }}}
	// {{{ public function addRenderer()

	/**
	 * Adds a cell renderer to this set
	 *
	 * An empty datafield-property mapping array is created for the added
	 * renderer.
	 *
	 * @param SwatCellRenderer $renderer the renderer to add.
	 */
	public function addRenderer(SwatCellRenderer $renderer)
	{
		$this->renderers[] = $renderer;

		$renderer_key = spl_object_hash($renderer);
		$this->mappings[$renderer_key] = array();

		if ($renderer->id !== null)
			$this->renderers_by_id[$renderer->id] = $renderer;
	}

	// }}}
	// {{{ public function addRendererWithMappings()

	/**
	 * Adds a cell renderer to this set with a predefined set of
	 * datafield-property mappings
	 *
	 * @param SwatCellRenderer $renderer the renderer to add.
	 * @param array $mappings an array of SwatCellRendererMapping objects.
	 *
	 * @see SwatCellRendererSet::addRenderer()
	 * @see SwatCellRendererSet::addMappingsToRenderer()
	 */
	public function addRendererWithMappings(SwatCellRenderer $renderer,
		array $mappings = array())
	{
		$this->addRenderer($renderer);
		$this->addMappingsToRenderer($renderer, $mappings);
	}

	// }}}
	// {{{ public function addMappingsToRenderer()

	/**
	 * Adds a set of datafield-property mappings to a cell renderer already in
	 * this set
	 *
	 * @param SwatCellRenderer $renderer the cell renderer to add the mappings
	 *                                    to.
	 * @param array $mappings an array of SwatCellRendererMapping objects.
	 *
	 * @throws SwatException if an attepmt to map a static cell renderer
	 *                        property is made.
	 */
	public function addMappingsToRenderer(SwatCellRenderer $renderer,
		array $mappings = array())
	{
		$renderer_key = spl_object_hash($renderer);

		foreach ($mappings as $mapping) {
			if ($renderer->isPropertyStatic($mapping->property))
				throw new SwatException(sprintf(
					'The %s property can not be data-mapped',
					$mapping->property));

			$this->mappings[$renderer_key][] = $mapping;
		}
	}

	// }}}
	// {{{ public function addMappingToRenderer()

	/**
	 * Adds a single property-datafield mapping to a cell renderer already in
	 * this set
	 *
	 * @param SwatCellRenderer $renderer the cell renderer to add the mapping
	 *                                    to.
	 * @param SwatCellRendererMapping $mapping the mapping to add.
	 *
	 * @throws SwatException if an attepmt to map a static cell renderer
	 *                        property is made.
	 */
	public function addMappingToRenderer(SwatCellRenderer $renderer,
		SwatCellRendererMapping $mapping)
	{
		if ($renderer->isPropertyStatic($mapping->property))
			throw new SwatException(sprintf(
				'The %s property can not be data-mapped', $mapping->property));

		$renderer_key = spl_object_hash($renderer);
		$this->mappings[$renderer_key][] = $mapping;
	}

	// }}}
	// {{{ public function applyMappingsToRenderer()

	/**
	 * Applies the property-datafield mappings to a cell renderer already in
	 * this set using a specified data object
	 *
	 * @param SwatCellRenderer $renderer the cell renderer to apply the
	 *                                    mappings to.
	 * @param mixed $data_object an object containg datafields to be
	 *                            mapped onto the cell renderer.
	 */
	public function applyMappingsToRenderer(SwatCellRenderer $renderer,
		$data_object)
	{
		// array to track array properties that we've already seen
		$array_properties = array();

		$renderer_hash = spl_object_hash($renderer);
		foreach ($this->mappings[$renderer_hash] as $mapping) {

			// set local variables
			$property = $mapping->property;
			$field = $mapping->field;

			if ($mapping->is_array) {
				if (in_array($property, $array_properties)) {
					// already have an array
					$array_ref = &$renderer->$property;

					if ($mapping->array_key === null)
						$array_ref[] = $data_object->$field;
					else
						$array_ref[$mapping->array_key] = $data_object->$field;

				} else {
					// starting a new array
					$array_properties[] = $mapping->property;

					if ($mapping->array_key === null)
						$renderer->$property = array($data_object->$field);
					else
						$renderer->$property =
							array($mapping->array_key => $data_object->$field);
				}
			} else {
				// look for leading '!' and inverse value if found
				if (strncmp($field , '!', 1) === 0) {
					$field = substr($field, 1);
					$renderer->$property = !($data_object->$field);
				} else {
					$renderer->$property = $data_object->$field;
				}
			}
		}

		$this->mappings_applied = true;
	}

	// }}}
	// {{{ public function getRendererByPosition()

	/**
	 * Gets a cell renderer in this set by its ordinal position
	 *
	 * @param integer $position the ordinal position of the renderer.
	 *
	 * @return SwatCellRenderer the cell renderer at the specified position.
	 *
	 * @throws SwatObjectNotFoundException if the requested <i>$position</i> is
	 *                                      greater than the number of cell
	 *                                      renderers in this set.
	 */
	public function getRendererByPosition($position = 0)
	{
		if ($position < count($this->renderers))
			return $this->renderers[$position];

		throw new SwatObjectNotFoundException(
			'Set does not contain that many renderers.',
			0, $position);
	}

	// }}}
	// {{{ public function getRenderer()

	/**
	 * Gets a renderer in this set by its id
	 *
	 * @param string $renderer_id the id of the renderer to get.
	 *
	 * @return SwatCellRenderer the cell renderer from this set with the given
	 *                           id.
	 *
	 * @throws SwatObjectNotFoundException if a renderer with the given
	 *                                      <i>$renderer_id</i> does not exist
	 *                                      in this set.
	 */
	public function getRenderer($renderer_id)
	{
		if (array_key_exists($renderer_id, $this->renderers_by_id))
			return $this->renderers_by_id[$renderer_id];

		throw new SwatObjectNotFoundException(
			"Cell renderer with an id of '{$renderer_id}' not found.",
			0, $renderer_id);
	}

	// }}}
	// {{{ public function getMappingsByRenderer()

	/**
	 * Gets the mappings of a cell renderer already in this set
	 *
	 * @param SwatCellRenderer $renderer the cell renderer to get the mappings
	 *                                    for.
	 *
	 * @return array an array of SwatCellRendererMapping objects for the
	 *                specified cell renderer.
	 */
	public function getMappingsByRenderer(SwatCellRenderer $renderer)
	{
		$renderer_key = spl_object_hash($renderer);
		return $this->mappings[$renderer_key];
	}

	// }}}
	// {{{ public function mappingsApplied()

	/**
	 * Whether or not mappings have been applied to this cell-renderer set
	 *
	 * @return boolean true if mappings have been applied to this cell renderer
	 *                  set and false if not.
	 *
	 * @todo This method doesn't make sense since mappings are applied to
	 *       renderers one at a time.
	 */
	public function mappingsApplied()
	{
		return $this->mappings_applied;
	}

	// }}}
	// {{{ public function current()

	/**
	 * Returns the current renderer
	 *
	 * @return SwatCellRenderer the current renderer.
	 */
	public function current()
	{
		return $this->renderers[$this->current_index];
	}

	// }}}
	// {{{ public function key()

	/**
	 * Returns the key of the current renderer
	 *
	 * @return integer the key of the current renderer
	 */
	public function key()
	{
		return $this->current_index;
	}

	// }}}
	// {{{ public function next()

	/**
	 * Moves forward to the next renderer
	 */
	public function next()
	{
		$this->current_index++;
	}

	// }}}
	// {{{ public function rewind()

	/**
	 * Rewinds this iterator to the first renderer
	 */
	public function rewind()
	{
		$this->current_index = 0;
	}

	// }}}
	// {{{ public function valid()

	/**
	 * Checks is there is a current renderer after calls to rewind() and next()
	 *
	 * @return boolean true if there is a current renderer and false if there
	 *                  is not.
	 */
	public function valid()
	{
		return array_key_exists($this->current_index, $this->renderers);
	}

	// }}}
	// {{{ public function getFirst()

	/**
	 * Gets the first renderer in this set
	 *
	 * @return SwatCellRenderer the first cell renderer in this set or null if
	 *                           there are no cell renderers in this set.
	 */
	public function getFirst()
	{
		$first = null;

		if (count($this->renderers) > 0)
			$first = reset($this->renderers);

		return $first;
	}

	// }}}
	// {{{ public function getCount()

	/**
	 * Gets the number of renderers in this set
	 *
	 * @return integer the number of renderers in this set.
	 *
	 * @deprecated this class now implements Countable. Use count($object)
	 *              instead of $object->getCount().
	 */
	public function getCount()
	{
		return count($this);
	}

	// }}}
	// {{{ public function count()

	/**
	 * Gets the number of cell renderers in this set
	 *
	 * This satisfies the Countable interface.
	 *
	 * @return integer the number of cell renderers in this set.
	 */
	public function count()
	{
		return count($this->renderers);
	}

	// }}}
}

?>
