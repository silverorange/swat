<?php
require_once('Swat/SwatWidget.php');

/**
 * Swat container widget
 *
 * Used as a base class for widgets which contain other widgets.
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatContainer extends SwatWidget {

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
	 * a parent already (parent == null).  The parent of the widget is set to
	 * reference the container.
	 *
	 * @param SwatWidget $widget A reference to a widget to add.
	 */
	public function add(SwatWidget $widget) {
		$this->packEnd($widget);
	}

	/**
	 * Add a widget to start
	 *
	 * Adds a widget to the start of the list of widgets in this container.
	 * @param SwatWidget $widget A reference to a widget to add.
	 */
	public function packStart(SwatWidget $widget) {
		if ($widget->parent != null)
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
		if ($widget->parent != null)
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
	 * @return array An array of all of the child widgets of this container.
	 */
	public function getChildren() {
		return $this->children;
	}

	public function process() {
		foreach ($this->children as &$child) {
			if ($child != null)
				$child->process();
		}		
	}

	public function display() {
		foreach ($this->children as &$child)
			$child->display();
	}

	public function addErrorMessage($msg) {
		$err = new SwatErrorMessage($msg);
		$this->error_messages[] = $err;
	}

	/**
	 * Gather error messages.
	 * Gather all error messages from children of this widget and this widget
	 * itself.
	 * @return array Array of SwatErrorMessage objects.
	 */
	public function gatherErrorMessages() {
		$msgs = array();

		foreach ($this->children as &$child)
			$msgs = array_merge($msgs, $child->gatherErrorMessages());

		return $msgs;
	}

	/**
	 * Check for error messages.
	 * @return boolean True if there is an error message in the subtree.
	 */
	public function hasErrorMessage() {
		$has_error = false;

		foreach ($this->children as &$child) {
			if ($child->hasErrorMessage()) {
				$has_error = true;
				break;
			}
		}
		
		return $has_error;		
	}

}

?>
