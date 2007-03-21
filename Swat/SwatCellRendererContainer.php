<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatUIObject.php';
require_once 'Swat/SwatCellRendererSet.php';
require_once 'Swat/SwatCellRendererMapping.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';

/**
 * Abstract base class for objects which contain cell renderers.
 *
 * @package   Swat
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatCellRendererContainer extends SwatUIObject
{
	// {{{ protected properties

	/**
	 * A set of SwatCellRenderer objects
	 *
	 * This object contains all the cell renderers for this column.
	 *
	 * @var SwatCellRendererSet
	 */
	protected $renderers = null;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new cell renderer container
	 */
	public function __construct()
	{
		parent::__construct();
		$this->renderers = new SwatCellRendererSet();
	}

	// }}}
	// {{{ public function addMappingToRenderer()

	/**
	 * Links a datafield to a renderer property
	 *
	 * @param SwatCellRenderer $renderer the cell renderer in this column that
	 *                                    the datafield is mapped onto.
	 * @param string $datafield the field of the data model to map to the
	 *                           renderer property.
	 * @param string $property the property of the cell renderer that the
	 *                          datafield is mapped to.
	 * @param SwatUIObject $object optional object containing the property to
	 *                              map when the property does not belong to
	 *                              the cell renderer itself.
	 *
	 * @return SwatCellRendererMapping a new mapping object that has been
	 *                                  added to the renderer.
	 */
	public function addMappingToRenderer($renderer, $datafield, $property,
		$object = null)
	{
		if ($object !== null)
			$property = $renderer->getPropertyNameToMap($object, $property);

		$mapping = new SwatCellRendererMapping($property, $datafield);
		$this->renderers->addMappingToRenderer($renderer, $mapping);

		if ($object !== null)
			$object->$property = $mapping;

		return $mapping;
	}

	// }}}
	// {{{ public function addRenderer()

	/**
	 * Adds a cell renderer to this column's set of renderers
	 *
	 * @param SwatCellRenderer $renderer the renderer to add.
	 */
	public function addRenderer(SwatCellRenderer $renderer)
	{
		$this->renderers->addRenderer($renderer);
		$renderer->parent = $this;
	}

	// }}}
	// {{{ public function getRenderers()

	/**
	 * Gets the cell renderers of this column
	 * 
	 * Returns an the array of {@link SwatCellRenderer} objects contained
	 * by this column.
	 *
	 * @return array the cell renderers contained by this column.
	 */
	public function getRenderers()
	{
		$out = array();
		foreach ($this->renderers as $renderer)
			$out[] = $renderer;

		return $out;
	}

	// }}}
	// {{{ public function getRenderer()

	/**
	 * Gets a cell renderers of this column by its unique identifier
	 *
	 * @param string the unique identifier of the cell renderer to get.
	 * 
	 * @return SwatCellRenderer the cell renderer of this column with the
	 *                           provided unique identifier.
	 */
	public function getRenderer($renderer_id)
	{
		return $this->renderers->getRenderer($renderer_id);
	}

	// }}}
	// {{{ public function getRendererByPosition()

	/**
	 * Gets a cell renderer in this column based on its ordinal position
	 *
	 * @param $position the ordinal position of the cell renderer to get. The
	 *                   position is zero-based.
	 *
	 * @return SwatCellRenderer the renderer at the specified ordinal position.
	 */
	public function getRendererByPosition($position = 0)
	{
		return $this->renderers->getRendererByPosition($position);
	}

	// }}}
	// {{{ public function getFirstRenderer()

	/**
	 * Gets the first cell renderer in this column
	 *
	 * @return SwatCellRenderer the first renderer.
	 */
	public function getFirstRenderer()
	{
		return $this->renderers->getFirst();
	}

	// }}}
	// {{{ public function addChild()

	/**
	 * Add a child object to this object
	 * 
	 * @param SwatCellRenderer $child the reference to the child object to add.
	 *
	 * @throws SwatInvalidClassException
	 *
	 * @see SwatUIParent::addChild()
	 */
	public function addChild(SwatObject $child)
	{
		if ($child instanceof SwatCellRenderer)
			$this->addRenderer($child);
		else
			throw new SwatInvalidClassException(
				'Only SwatCellRender objects may be nested within '.
				get_class($this).' objects.', 0, $child);
	}

	// }}}
}

?>
