<?php

require_once 'Swat/SwatFieldset.php';
require_once 'Swat/SwatDisplayableContainer.php';
require_once 'Swat/SwatReplicable.php';

/**
 * A fieldset container that replicates itself and its children
 *
 * This widget can dynamically create widgets based on an array of
 * replicator identifiers.
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatReplicatorFieldset extends SwatDisplayableContainer
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
	 * Creates a new replicator fieldset
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
	 * Initilizes this replicable fieldset
	 *
	 * Goes through the internal widgets, clones them, and adds them to the
	 * widget tree.
	 */
	public function init()
	{
		parent::init();

		$children = array();

		if ($this->replicators === null)
			$this->replicators = array(0 => null);

		// first we add each child to the local array, and remove from the
		// widget tree
		foreach ($this->children as $child_widget)
			$children[] = $this->remove($child_widget);

		// then we clone, change the id and add back to the widget tree inside
		// a replicated fieldset
		foreach ($this->replicators as $id => $title) {
			$field_set = new SwatFieldset();
			$field_set->title = $title;
			$suffix = '_'.$this->id.$id;
			$this->widgets[$id] = array();

			foreach ($children as $child) {
				$new_child = clone $child;

				if ($new_child->id !== null) {
					$this->widgets[$id][$new_child->id] = $new_child;
					$new_child->id.= $suffix;
				}

				// update ids of cloned child descendants
				if ($new_child instanceof SwatContainer) {
					foreach ($new_child->getDescendants() as $descendant) {
						if ($descendant->id !== null) {
							$this->widgets[$id][$descendant->id] = $descendant;
							$descendant->id.= $suffix;
						}
					}
				}

				$field_set->add($new_child);
			}
			$this->add($field_set);
		}
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
		$widget = null;

		if (isset($this->widgets[$replicator_id][$widget_id]))
			$widget = $this->widgets[$replicator_id][$widget_id];

		return $widget;
	}

	// }}}
}

?>
