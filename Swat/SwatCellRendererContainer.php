<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatUIObject.php';
require_once 'Swat/SwatUIParent.php';
require_once 'Swat/SwatCellRendererSet.php';
require_once 'Swat/SwatCellRendererMapping.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';

/**
 * Abstract base class for objects which contain cell renderers.
 *
 * @package   Swat
 * @copyright 2006-2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatCellRendererContainer extends SwatUIObject implements
	SwatUIParent
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
	 * by this cell renderer container.
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
	// {{{ public function getDescendants()

	/**
	 * Gets descendant UI-objects
	 *
	 * @param string $class_name optional class name. If set, only UI-objects
	 *                            that are instances of <i>$class_name</i> are
	 *                            returned.
	 *
	 * @return array the descendant UI-objects of this cell renderer container.
	 *                If descendent objects have identifiers, the identifier is
	 *                used as the array key.
	 *
	 * @see SwatUIParent::getDescendants()
	 */
	public function getDescendants($class_name = null)
	{
		if ($class !== null && !class_exists($class_name))
			return array();

		$out = array();

		foreach ($this->getRenderers() as $renderer) {
			if ($class_name === null || $renderer instanceof $class_name) {
				if ($renderer->id === null)
					$out[] = $renderer;
				else
					$out[$renderer->id] = $renderer;
			}

			if ($renderer instanceof SwatUIParent)
				$out = array_merge($out,
					$renderer->getDescendants($class_name));
		}

		return $out;
	}

	// }}}
	// {{{ public function getFirstDescendant()

	/**
	 * Gets the first descendent UI-object of a specific class
	 *
	 * @param string $class_name class name to look for.
	 *
	 * @return SwatUIObject the first descendant UI-object or null if no
	 *                       matching descendant is found.
	 *
	 * @see SwatUIParent::getFirstDescendant()
	 */
	public function getFirstDescendant($class_name)
	{
		if (!class_exists($class_name))
			return null;

		$out = null;

		$renderers = $this->getRenderers();

		foreach ($renderers as $renderer) {
			if ($renderer instanceof SwatUIParent) {
				$out = $renderer->getFirstDescendant($class_name);
				if ($out !== null)
					break;
			}
		}

		if ($out === null) {
			foreach ($renderers as $renderer) {
				if ($renderer instanceof $class_name) {
					$out = $renderer;
					break;
				}
			}
		}

		return $out;
	}

	// }}}
	// {{{ public function getDescendantStates()

	/**
	 * Gets descendant states
	 *
	 * Retrieves an array of states of all stateful UI-objects in the widget
	 * subtree below this cell renderer container.
	 *
	 * @return array an array of UI-object states with UI-object identifiers as
	 *                array keys.
	 */
	public function getDescendantStates()
	{
		$states = array();

		foreach ($this->getDescendants('SwatState') as $id => $object)
			$states[$id] = $object->getState();

		return $states;
	}

	// }}}
	// {{{ public function setDescendantStates()

	/**
	 * Sets descendant states
	 *
	 * Sets states on all stateful UI-objects in the widget subtree below this
	 * cell renderer container.
	 *
	 * @param array $states an array of UI-object states with UI-object
	 *                       identifiers as array keys.
	 */
	public function setDescendantStates($states)
	{
		foreach ($this->getDescendants('SwatState') as $id => $object)
			if (isset($states[$id]))
				$object->setState($states[$id]);
	}

	// }}}
}

?>
