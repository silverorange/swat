<?php

require_once('Swat/SwatWidget.php');
require_once('Swat/SwatUIParent.php');

/**
 * Swat container widget
 *
 * Used as a base class for widgets which contain other widgets.
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatContainer extends SwatWidget implements SwatUIParent {

	/**
	 * Children widgets
	 *
	 * An array containing the widgets that belong to this box,
	 * or null.
	 * @var array
	 */
	protected $children = array();

	/**
	 * Add a widget
	 * 
	 * Adds a widget as a child of this container. The widget must not have
	 * a parent already (parent === null).  The parent of the widget is set to
	 * reference the container.
	 *
	 * @param SwatWidget $widget A reference to a widget to add.
	 */
	public function add(SwatWidget $widget) {
		$this->packEnd($widget);
	}

	/**
	 * Remove a widget
	 * 
	 * Removes a child widget from container. The parent of the widget is set 
	 * to null.
	 *
	 * @param SwatWidget $widget A reference to a widget to remove.
	 */
	public function remove(SwatWidget $widget) {
		foreach ($this->children as $key => $child_widget) {
			if ($child_widget === $widget)
				unset($this->children[$key]);
				$widget->parent = null;
		}
	}

	/**
	 * Add a widget to start
	 *
	 * Adds a widget to the start of the list of widgets in this container.
	 * @param SwatWidget $widget A reference to a widget to add.
	 */
	public function packStart(SwatWidget $widget) {
		if ($widget->parent !== null)
			throw new SwatException("Attempting to add a widget that already ".
				"has a parent.");

		array_unshift($this->children, $widget);
		$widget->parent = $this;
	}

	/**
	 * Add a widget to end
	 *
	 * Adds a widget to the end of the list of widgets in this container.
	 * @param SwatWidget $widget A reference to a widget to add.
	 */
	public function packEnd(SwatWidget $widget) {
		if ($widget->parent !== null)
			throw new SwatException("Attempting to add a widget that already ".
				"has a parent.");

		$this->children[] = $widget;
		$widget->parent = $this;
	}

	/**
	 * Get Child Widget
	 *
	 * Used to retrieve a widget from the list of widgets in the container
	 *
	 * @param int $id The id of the widget to look for.
	 * @return SwatWidget Returns the corresponding widget to id or null
	 *         if none found
	 */
	public function getChild($id = 0) {
		if (array_key_exists($id, $this->children))
			return $this->children[$id];
		else
			return null;
	}

	/**
	 * Get children widgets
	 *
	 * Retrieve an array of all widgets directly contained by this container.
	 *
	 * @param string $class_name Optional class name. If not null, only widgets that 
	 *        are instances of $class_name are returned.
	 * @return array An array of the child widgets in this container.
	 */
	public function getChildren($class_name = null) {
		if ($class_name === null)
			return $this->children;

		$out = array();

		foreach($this->children as $child_widget)
			if ($child_widget instanceof $class_name)
				$out[] = $child_widget;

		return $out;
	}

	/**
	 * Get descendant widgets
	 *
	 * Retrieve an array of all widgets in the widget subtree below this container.
	 *
	 * @param string $class_name Optional class name. If not null, only widgets that 
	 *        are instances of $class_name are returned.
	 * @return array An array of descendant widgets in this container.
	 */
	public function getDescendants($class_name = null) {
		$out = array();

		foreach($this->children as $child_widget) {
			if ($class_name === null || $child_widget instanceof $class_name) {

				if ($child_widget->id === null)
					$out[] = $child_widget;
				else
					$out[$child_widget->id] = $child_widget;
			}

			if ($child_widget instanceof SwatContainer)
				$out = array_merge($out, $child_widget->getDescendants($class_name));
		}

		return $out;
	}

	/**
	 * Get descendant states
	 *
	 * Retrieve an array of states of all SwatControl widgets in the widget 
	 * subtree below this container.
	 *
	 * @return array $states Array of states keyed by widget id.
	 */
	public function getDescendantStates() {
		$states = array();

		foreach ($this->getDescendants('SwatState') as $id => $widget)
			$states[$id] = $widget->getState();

		return $states;
	}

	/**
	 * Set descendant states
	 *
	 * Set states on all SwatControl widgets in the widget subtree below this 
	 * container.
	 *
	 * @param array $states Array of states keyed by widget id.
	 */
	public function setDescendantStates($states) {

		foreach ($this->getDescendants('SwatState') as $id => $widget)
			if (isset($states[$id]))
				$widget->setState($states[$id]);
	}

	public function process() {
		foreach ($this->children as &$child) {
			if ($child !== null)
				$child->process();
		}		
	}

	public function display() {
		foreach ($this->children as &$child)
			$child->display();
	}

	public function addMessage($msg) {
		$this->messages[] = $msg;
	}

	/**
	 * Gather messages.
	 * Gather all messages from children of this widget and this widget
	 * itself.
	 * @param bool $all If true return all messages from child widgets.
	 * @return array Array of SwatErrorMessage objects.
	 */
	public function gatherMessages($all = true) {
		$msgs = $this->messages;

		if ($all)
			foreach ($this->children as &$child)
				$msgs = array_merge($msgs, $child->gatherMessages());

		return $msgs;
	}

	/**
	 * Check for messages.
	 * @return boolean True if there is an message in the subtree.
	 */
	public function hasMessage() {
		$has_msg = false;

		foreach ($this->children as &$child) {
			if ($child->hasMessage()) {
				$has_msg = true;
				break;
			}
		}
		
		return $has_msg;		
	}

	/**
	 * Add a child object
	 * 
	 * This method fulfills the {@link SwatUIParent} interface.  It is used 
	 * by {@link SwatUI} when building a widget tree and should not need to be
	 * called elsewhere.  To add a widget to a container use 
	 * {@link SwatContainer::add()}.
	 *
	 * @param $child A reference to a child object to add.
	 */
	public function addChild($child) {

		if ($child instanceof SwatWidget)
			$this->add($child);
		else
			throw new SwatException('SwatContainer: Only '.
				'SwatWidgets can be nested within SwatContainer');
	}

}

?>
