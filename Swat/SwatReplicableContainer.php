<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatDisplayableContainer.php';
require_once 'Swat/SwatReplicable.php';

/**
 * A container that replicates itself and its children
 *
 * @package   Swat
 * @copyright 2005-2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatReplicableContainer extends SwatDisplayableContainer
	implements SwatReplicable
{
	// {{{ public properties

	/**
	 * An array of unique id => title pairs, one for each replication
	 *
	 * The ids are used to suffix the original widget ids to create unique
	 * ids for the replicated widgets. The titles are displayed as the titles
	 * of the fieldsets surrounding the replicated widgets.
	 *
	 * @var array
	 */
	public $replicators = null;

	// }}}
	// {{{ protected properties

	protected $widgets = array();

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new replicator container
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);
		$this->requires_id = true;
	}

	// }}}
	// {{{ public function init()

	/**
	 * Initilizes this replicable container
	 *
	 * Goes through the internal widgets, clones them, and adds them to the
	 * widget tree.
	 */
	public function init()
	{
		// Make sure this replicator has a unique id before replicating
		// chidren. This is because replicated children use the id of this
		// replicable container as a suffix.
		if ($this->id === null)
			$this->id = $this->getUniqueId();

		$container_parent = $this->getContainerParent();
		if ($container_parent === null)
			$container_parent = $this;

		$children = array();

		if ($this->replicators === null)
			$this->replicators = array(0 => null);

		// first we add each child to the local array, and remove from the
		// widget tree
		foreach ($this->children as $child_widget)
			$children[] = $this->remove($child_widget);

		// then we clone, change the id and add back to the widget tree inside
		// a replicated container
		foreach ($this->replicators as $id => $title) {
			$container = $this->getContainer($id, $title);
			$this->widgets[$id] = array();
			$this->widgets[$id][$this->id] = $container;
			$suffix = '_'.$this->id.'_'.$id;

			foreach ($children as $child) {
				$copy_child = $child->copy($suffix);

				if ($copy_child->id !== null) {
					// lookup array uses original ids
					$old_id = substr($copy_child->id, 0, -strlen($suffix));
					$this->widgets[$id][$old_id] = $copy_child;
				}

				if ($copy_child instanceof SwatUIParent) {
					foreach ($copy_child->getDescendants() as $descendant) {
						if ($descendant->id !== null) {
							// lookup array uses original ids
							$old_id = substr($descendant->id, 0,
								-strlen($suffix));

							$this->widgets[$id][$old_id] = $descendant;
						}
					}
				}

				if ($container === null)
					$container_parent->addChild($copy_child);
				else
					$container->add($copy_child);
			}

			if ($container !== null)
				$container_parent->addChild($container);
		}

		if ($container_parent !== $this)
			$this->add($container_parent);

		parent::init();
	}

	// }}}
	// {{{ public function getWidget()

	/**
	 * Retrives a reference to a replicated widget
	 *
	 * @param string $widget_id the unique id of the original widget
	 * @param string $replicator_id the replicator id of the replicated widget
	 *
	 * @return SwatWidget a reference to the replicated widget, or null if the
	 *                     widget is not found.
	 */
	public function getWidget($widget_id, $replicator_id)
	{
		// TODO: reverse the params of this function and default $widget_id to null
		$widget = null;

		if (isset($this->widgets[$replicator_id][$widget_id]))
			$widget = $this->widgets[$replicator_id][$widget_id];

		return $widget;
	}

	// }}}
	// {{{ protected function getContainerParent()

	/**
	 * Gets an optional UI-parent class that contains the replicated containers
	 *
	 * This is useful if the containers only belong in a certain type of parent
	 * object but the parent object is not a SwatDisplayableContainer.
	 *
	 * @return SwatUIParent the parent object of replicated containers. If null,
	 *                       replicated containers are added directly to this
	 *                       container.
	 */
	protected function getContainerParent()
	{
		return null;
	}

	// }}}
	// {{{ abstract protected function getContainer()

	/**
	 * Gets a container to contain replicated widgets for this replicable
	 * container
	 *
	 * @param string $id the replicator id for the container.
	 * @param stirng $title the title of the container. The container may or
	 *                       may not use this title.
	 *
	 * @return SwatContainer the container object to which replciated widgets
	 *                        are added. The container is added to the widget
	 *                        tree after adding the replicated widgets to the
	 *                        container. If null is returned, the widgets are
	 *                        replicated directly in the widget tree.
	 */
	protected function getContainer($id, $title)
	{
		return new SwatDisplayableContainer($id);
	}

	// }}}
}

?>
