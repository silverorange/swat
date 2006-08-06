<?php

require_once 'Swat/SwatReplicable.php';
require_once 'Swat/SwatFormField.php';
require_once 'Swat/SwatFieldset.php';

/**
 * A magic form field container that replicates its children. It can 
 * dynamically create widgets based on an array of replicators.
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatReplicatorFormField extends SwatFormField implements SwatReplicable
{
	// {{{ public properties

	/**
	 * An array of unique id => title pairs, one for each replication.
	 * The id is used to suffix the original widget id to create a unique
	 * id for the replicated widget.
	 *
	 * @var array
	 */
	public $replicators = null; 

	// }}}
	// {{{ private properies

	private $widgets = array();

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new replicator formfield
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
	 * Initilizes the form field
	 *
	 * Goes through the child widgets, clones them, and adds them to the
	 * widget tree.
	 */
	public function init()
	{
		parent::init();
		
		$local_children = array();

		if ($this->replicators === null)
			return;
		
		//first we add each child to the local array, and remove from the widget tree
		foreach ($this->children as $child_widget)
			$local_children[] = $this->remove($child_widget);

		$container = new SwatFieldset();
		$container->id = $this->id;
		$container->title = $this->title;

		//then we clone, change the id and add back to the widget tree
		foreach ($this->replicators as $id => $title) {
			$form_field = new SwatFormField();
			$form_field->title = $title;
			$container->add($form_field);
			$suffix = '_'.$this->id.$id;
			
			$this->widgets[$id] = array();
			
			foreach ($local_children as $child) {
				$new_child = clone $child;

				if ($child->id !== null) {
					$this->widgets[$id][$new_child->id] = $new_child;
					$new_child->id.= $suffix;
				}

				if ($new_child instanceof SwatContainer) {
					foreach ($new_child->getDescendants() as $descendant) {
						if ($descendant->id !== null) {
							$this->widgets[$id][$descendant->id] = $descendant;
							$descendant->id.= $suffix;
						}
					}
				}

				$form_field->add($new_child);
			}
		}
		$container->init();
		$this->parent->replace($this, $container);
	}

	// }}}
	// {{{ public function getWidget()

	/**
	 * Retrive a reference to a replicated widget
	 *
	 * @param string $widget_id the unique id of the original widget
	 * @param string $replicator_id the replicator id of the replicated widget
	 *
	 * @returns SwatWidget a reference to the replicated widget, or null if the
	 *                      widget is not found.
	 */
	public function getWidget($widget_id, $replicator_id)
	{
		if (isset($this->widgets[$replicator_id][$widget_id])) {
			return $this->widgets[$replicator_id][$widget_id];
		} else {
			return null;
		}
	}

	// }}}
}

?>
