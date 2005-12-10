<?php

require_once 'SwatObject.php';
require_once 'SwatCellRenderer.php';
require_once 'Swat/exceptions/SwatObjectNotFoundException.php';

/**
 * A collection of cell renderers with associated datafield-property mappings
 *
 * @package   Swat
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatCellRendererSet extends SwatObject implements Iterator
{
	// {{{ private properties

	/**
	 * A two dimentional array containing a list of cell renderers and
	 * associated datafield-property mappings
	 *
	 * The array is of the form:
	 *
	 * <code>
	 * array(
	 *     0 => array(
	 *         'renderer' => new SwatCellRenderer(),
	 *         'map' => array(
	 *             'field_name_1' => 'property_name_2'
	 *             'field_name_2' => 'property_name_2'
	 *         )
	 *     )
	 * );
	 * </code>
	 *
	 * @var array
	 */
	private $renderers = array();

	/**
	 * The current index of the iterator interface
	 *
	 * @var integer
	 */
	private $current_index = 0;
	
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
		$this->renderers[] =
			array('renderer' => $renderer, 'mappings' => array());
	}

	// }}}
	// {{{ public function addRendererWithMappings()

	/**
	 * Adds a cell renderer to this set with a predefined set of
	 * datafield-property mappings
	 *
	 * @param SwatCellRenderer $renderer the renderer to add.
	 * @param array $mappings an associative array of datafield-property
	 *                         mappings for the added renderer.
	 */
	public function addRendererWithMappings(SwatCellRenderer $renderer,
		$mappings = array())
	{
		$this->renderers[] =
			array('renderer' => $renderer, 'mappings' => $mappings);
	}

	// }}}
	// {{{ public function addMappingsToRenderer()

	/**
	 * Adds a set of datafield-property mappings to a cell renderer already in
	 * this set
	 *
	 * @param SwatCellRenderer $renderer the cell renderer to add the mappings
	 *                                    to.
	 * @param array $mappings an associative array of datafield-property
	 *                         mappings to add to the specified cell renderer.
	 */
	public function addMappingsToRenderer(SwatCellRenderer $renderer,
		$mappings = array())
	{
		$position = $this->findRendererIndex($renderer);
		
		array_merge($this->renderers[$position]['mappings'], $mappings);
	}

	// }}}
	// {{{ public function addMappingToRenderer()

	/**
	 * Adds a single datafield-property mapping to a cell renderer already in
	 * this set
	 *
	 * @param SwatCellRenderer $renderer the cell renderer to add the mapping
	 *                                    to.
	 * @param string $datafield the field in the data object that is to be
	 *                           mapped.
	 * @param string property the property of the cell renderer the datafield
	 *                         is to be mapped to.
	 */
	public function addMappingToRenderer(SwatCellRenderer $renderer,
		$datafield, $property)
	{
		$position = $this->findRendererIndex($renderer);
		
		$this->renderers[$position]['mappings'][$datafield] = $property;
	}

	// }}}
	// {{{ public function applyMappingsToRenderer()

	/**
	 * Applys the datafield-property mappings to a cell renderer already in
	 * this set using a specified data object
	 *
	 * @param SwatCellRenderer $renderer the cell renderer to apply the
	 *                                    mappings to.
	 * @param mixed $data_object an object containg datafields to be
	 *                            mapped onto the cell renderer.
	 */
	public function applyMappingsToRenderer($renderer, $data_object)
	{
		$position = $this->findRendererIndex($renderer);
		
		foreach ($this->renderers[$position]['mappings'] as
			$datafield => $property) {
			
			$renderer->$property = $data_object->$datafield;
		}
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
			return $this->renderers[$position]['renderer'];

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
		foreach ($this->renderers as $content)
			if ($content['renderer']->id == $renderer_id)
				return $content['renderer'];

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
	 * @return array an associative array containing the datafield-property
	 *                mappings of the specified cell renderer.
	 */
	public function getMappingsByRenderer(SwatCellRenderer $renderer)
	{
		$position = $this->findRendererIndex($renderer);
		
		return $this->renderers[$position]['mappings'];
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
		return $this->renderers[$this->current_index]['renderer'];
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
			$first = $this->renderers[0]['renderer'];

		return $first;
	}

	// }}}
	// {{{ public function getCount()

	/**
	 * Gets the number of renderers in this set
	 *
	 * @return integer the number of renderers in this set.
	 */
	public function getCount()
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
	private function findRendererIndex(SwatCellRenderer $renderer)
	{
		foreach ($this->renderers as $position => $content)
			if ($content['renderer'] === $renderer)
				return $position;
		
		throw new SwatObjectNotFoundException('Cell renderer not found.');
	}

	// }}}
}

?>
