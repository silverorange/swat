<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatWidget.php');

/**
 * Base class for widgets which contain other widgets.
 */
class SwatContainer extends SwatWidget {

	/**
	 * An array containing the widgets that belong to this box,
	 * or null.
	 * @var array
	 */
	protected $children = array();

	/**
	 * Add a widget.
	 * 
	 * Adds a widget as a child of this container. The widget must not have
	 * a parent already (parent == null).  The parent of the widget is set to
	 * reference the container.
	 *
	 * @param $widget SwatWidget A reference to a widget to add.
	 */
	public function add(SwatWidget $widget) {
		$this->packEnd($widget);
	}

	public function packStart(SwatWidget $widget) {
		if ($widget->parent != null)
			throw new SwatException("Attempting to add a widget that already ".
				"has a parent.");

		array_unshift($this->children, $widget);
		$widget->parent = $this;
	}

	public function packEnd(SwatWidget $widget) {
		if ($widget->parent != null)
			throw new SwatException("Attempting to add a widget that already ".
				"has a parent.");

		$this->children[] = $widget;
		$widget->parent = $this;
	}

	public function getChild($id = 0) {
		if (array_key_exists($id, $this->children))
			return $this->children[$id];
		else
			return null;
	}

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

	/**
	 * Gather error messages.
	 *
	 * Gather all error messages from children of this widget and this widget
	 * itself.
	 *
	 * @return array Array of SwatErrorMessage objects.
	 */
	public function gatherErrorMessages() {
		$msgs = array();

		foreach ($this->children as &$child)
			$msgs = array_merge($msgs, $child->gatherErrorMessages());

		return $msgs;
	}
}

?>
