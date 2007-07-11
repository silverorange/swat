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
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatCellRendererSet extends SwatObject implements Iterator, Countable
{
	// {{{ private properties

	/**
	 * An array of cell renderers.
	 *
	 * @var array
	 */
	private $renderers = array();

	/**
	 * An array of mappings.
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
	 * Whether or not mappings have been applied to this cell-renderer set
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
		$index = $this->findRendererIndex($renderer);
		$this->mappings[$index] = array();
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
		$index = $this->findRendererIndex($renderer);

		foreach ($mappings as $mapping) {
			if ($renderer->isPropertyStatic($mapping->property))
				throw new SwatException(sprintf(
					'The %s property can not be data-mapped',
					$mapping->property));

			$this->mappings[$index][] = $mapping;
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

		$index = $this->findRendererIndex($renderer);
		$this->mappings[$index][] = $mapping;
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
		$index = $this->findRendererIndex($renderer);
		// array to track array properties that we've already seen
		$array_properties = array();
		
		foreach ($this->mappings[$index] as $mapping) {

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
	 * @throws SwatException
	 */
	public function getRendererByPosition($position = 0)
	{
		if ($position < count($this->renderers))
			return $this->renderers[$position];

		throw new SwatException('Set does not contain that many renderers.');
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
	 * @throws SwatObjectNotFoundException
	 */
	public function getRenderer($renderer_id)
	{
		foreach ($this->renderers as $renderer)
			if ($renderer->id === $renderer_id)
				return $renderer;

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
	 * @return array an array of SwatCellRendererMapping objects
	 *                for the specified cell renderer.
	 */
	public function getMappingsByRenderer(SwatCellRenderer $renderer)
	{
		$index = $this->findRendererIndex($renderer);
		
		return $this->mappings[$index];
	}

	// }}}
	// {{{ public function mappingsApplied()

	/**
	 * Whether or not mappings have been applied to this cell-renderer set
	 *
	 * @return boolean whether or not mappings have been applied to this
	 *                  cell-renderer set.
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
		return isset($this->renderers[$this->current_index]);
	}

	// }}}
	// {{{ public function getFirst()

	/**
	 * Retrieves the first renderer in this set
	 *
	 * @return mixed the first renderer in this set or null if there are none.
	 */
	public function getFirst()
	{
		$first = null;

		if (count($this->renderers) > 0)
			$first = $this->renderers[0];

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
		return count($this->renderers);
	}

	// }}}
	// {{{ public function count()

	/**
	 * Gets the number of renderers in this set
	 *
	 * This satisfies the Countable interface.
	 *
	 * @return integer the number of renderers in this set.
	 */
	public function count()
	{
		return count($this->renderers);
	}

	// }}}
	// {{{ private function findRendererIndex()

	/**
	 * Finds the array position of a cell renderer already in this set
	 *
	 * @param SwatCellRenderer $renderer the cell renderer to find.
	 *
	 * @return integer the position in the internal array the cell renderer is
	 *                  located at.
	 *
	 * @throws SwatObjectNotFoundException
	 */
	private function findRendererIndex(SwatCellRenderer $sought_renderer)
	{
		foreach ($this->renderers as $index => $renderer)
			if ($renderer === $sought_renderer)
				return $index;
		
		throw new SwatObjectNotFoundException('Cell renderer not found.');
	}

	// }}}
}

?>
