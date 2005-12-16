<?php

require_once 'Swat/SwatWidget.php';
require_once 'Swat/SwatUIParent.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';

/**
 * Swat container widget
 *
 * Used as a base class for widgets which contain other widgets.
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatContainer extends SwatWidget implements SwatUIParent
{
	/**
	 * Children widgets
	 *
	 * An array containing the widgets that belong to this container.
	 *
	 * @var array
	 */
	protected $children = array();

	public function __clone() {
		$children = $this->children;
		$this->children = array();

		foreach ($children as $key => $child)
			$this->children[$key] = clone $child;
	}

	/**
	 * Initializes this widget
	 *
	 * Recursively initializes children widgets.
	 *
	 * @see SwatWidget::init()
	 */
	public function init()
	{
		parent::init();

		foreach($this->children as $child_widget)
			$child_widget->init();
	}

	/**
	 * Adds a widget
	 * 
	 * Adds a widget as a child of this container. The widget must not have
	 * a parent already. The parent of the added widget is set to
	 * reference this container.
	 *
	 * @param SwatWidget $widget a reference to the widget to add.
	 */
	public function add(SwatWidget $widget)
	{
		$this->packEnd($widget);
	}

	/**
	 * Replace a widget
	 * 
	 * Replaces a child widget in this container. The parent of the removed 
	 * widget is set to null.
	 *
	 * @param SwatWidget $widget a reference to the widget to be replaced.
	 * @param SwatWidget $widget a reference to the new widget.
	 *
	 * @return SwatWidget a reference to the removed widget, or null if the
	 *                     widget is not found.
	 */
	public function replace(SwatWidget $widget, SwatWidget $new_widget)
	{
		foreach ($this->children as $key => $child_widget) {
			if ($child_widget === $widget) {
				$this->children[$key] = $new_widget;
				$widget->parent = null;
				return $widget;
			}
		}
		return null;
	}

	/**
	 * Removes a widget
	 * 
	 * Removes a child widget from this container. The parent of the widget is
	 * set to null.
	 *
	 * @param SwatWidget $widget a reference to the widget to remove.
	 *
	 * @return SwatWidget a reference to the removed widget, or null if the
	 *                     widget is not found.
	 */
	public function remove(SwatWidget $widget)
	{
		foreach ($this->children as $key => $child_widget) {
			if ($child_widget === $widget) {
				unset($this->children[$key]);
				$widget->parent = null;
				return $widget;
			}
		}
		return null;
	}

	/**
	 * Adds a widget to start
	 *
	 * Adds a widget to the start of the list of widgets in this container.
	 *
	 * @param SwatWidget $widget a reference to the widget to add.
	 */
	public function packStart(SwatWidget $widget)
	{
		if ($widget->parent !== null)
			throw new SwatException('Attempting to add a widget that already '.
				'has a parent.');

		array_unshift($this->children, $widget);
		$widget->parent = $this;

		$this->sendAddNotifySignal($widget);
	}

	/**
	 * Adds a widget to end
	 *
	 * Adds a widget to the end of the list of widgets in this container.
	 *
	 * @param SwatWidget $widget a reference to the widget to add.
	 */
	public function packEnd(SwatWidget $widget)
	{
		if ($widget->parent !== null)
			throw new SwatException('Attempting to add a widget that already '.
				'has a parent.');

		$this->children[] = $widget;
		$widget->parent = $this;

		$this->sendAddNotifySignal($widget);
	}

	/**
	 * Gets a child widget
	 *
	 * TODO: this method is broken, id is unpredictable and unknown
	 * Retrieves a widget from the list of widgets in the container based on
	 * the unique identifier of the widget.
	 *
	 * @param string $id the unique id of the widget to look for.
	 *
	 * @return SwatWidget the found widget or null not found.
	 */
	public function getChild($id = 0)
	{
		if (array_key_exists($id, $this->children))
			return $this->children[$id];
		else
			return null;
	}

	/**
	 * Gets the first child widget
	 *
	 * Retrieves the first child widget from the list of widgets in the 
	 * container.
	 *
	 * @return SwatWidget the first widget or null.
	 */
	public function getFirst()
	{
		if (count($this->children)) {
			reset($this->children);
			return current($this->children);
		} else {
			return null;
		}
	}

	/**
	 * Gets all child widgets
	 *
	 * Retrieves an array of all widgets directly contained by this container.
	 *
	 * @param string $class_name optional class name. If set, only widgets that
	 *                            are instances of $class_name are returned.
	 *
	 * @return array the child widgets of this container.
	 */
	public function getChildren($class_name = null)
	{
		if ($class_name === null)
			return $this->children;

		$out = array();

		foreach($this->children as $child_widget)
			if ($child_widget instanceof $class_name)
				$out[] = $child_widget;

		return $out;
	}

	/**
	 * Gets descendant widgets
	 *
	 * Retrieves an ordered array of all widgets in the widget subtree below 
	 * this container. Widgets are ordered in the array as they are found in 
	 * a breadth-first traversal of the subtree.
	 *
	 * @param string $class_name optional class name. If set, only widgets that
	 *                            are instances of $class_name are returned.
	 *
	 * @return array the descendant widgets of this container.
	 */
	public function getDescendants($class_name = null)
	{
		$out = array();

		foreach ($this->children as $child_widget) {
			if ($class_name === null || $child_widget instanceof $class_name) {
				if ($child_widget->id === null)
					$out[] = $child_widget;
				else
					$out[$child_widget->id] = $child_widget;
			}

			if ($child_widget instanceof SwatContainer)
				$out = array_merge($out,
					$child_widget->getDescendants($class_name));
		}

		return $out;
	}

	/**
	 * Gets the first descendent widget of a specific class
	 *
	 * Retrieves the first descendant widget in the subtree that is a 
	 * descendant of the specified class name. This uses a depth-first
	 * traversal to search the tree.
	 *
	 * @param string $class_name class name to look for.
	 *
	 * @return mixed the first descendant widget of null if no matching
	 *                descendant is found.
	 */
	public function getFirstDescendant($class_name)
	{
		if (!class_exists($class_name))
			return null;

		$out = null;

		foreach ($this->children as $child_widget) {
			if ($child_widget instanceof SwatContainer) {
				$out = $child_widget->getFirstDescendant($class_name);
				if ($out !== null)
					break;
			}
		}

		if ($out === null) {
			foreach ($this->children as $child_widget) {
				if ($child_widget instanceof $class_name) {
					$out = $child_widget;
					break;
				}
			}
		}

		return $out;
	}

	/**
	 * Gets descendant states
	 *
	 * Retrieves an array of states of all SwatControl widgets in the widget 
	 * subtree below this container.
	 *
	 * @return array the widget states keyed by widget id.
	 */
	public function getDescendantStates()
	{
		$states = array();

		foreach ($this->getDescendants('SwatState') as $id => $widget)
			$states[$id] = $widget->getState();

		return $states;
	}

	/**
	 * Sets descendant states
	 *
	 * Sets states on all SwatControl widgets in the widget subtree below this 
	 * container.
	 *
	 * @param array $states the widget states keyed by widget id.
	 */
	public function setDescendantStates($states)
	{
		foreach ($this->getDescendants('SwatState') as $id => $widget)
			if (isset($states[$id]))
				$widget->setState($states[$id]);
	}

	/**
	 * Processes this container by calling process() on all children
	 */
	public function process()
	{
		foreach ($this->children as &$child) {
			if ($child !== null)
				$child->process();
		}
	}

	/**
	 * Displays this container by calling display() on all children
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		foreach ($this->children as &$child)
			$child->display();
	}

	/**
	 * Adds a message
	 *
	 * @param SwatMessage the message object to add.
	 *
	 * @see SwatWidget::AddMessage()
	 */
	public function addMessage($message)
	{
		$this->messages[] = $message;
	}

	/**
	 * Gets all messages
	 *
	 * @return array the gathered SwatMessage objects.
	 *
	 * @see SwatWidget::getMessages()
	 */
	public function getMessages()
	{
		$msgs = $this->messages;

		foreach ($this->children as &$child)
			$msgs = array_merge($msgs, $child->getMessages());

		return $msgs;
	}

	/**
	 * Checks for the presence of messages
	 *
	 * @return boolean true if there is an message in the subtree.
	 *
	 * @see SwatWidget::hasMessages()
	 */
	public function hasMessage()
	{
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
	 * Adds a child object
	 * 
	 * This method fulfills the {@link SwatUIParent} interface. It is used 
	 * by {@link SwatUI} when building a widget tree and should not need to be
	 * called elsewhere. To add a widget to a container use 
	 * {@link SwatContainer::add()}.
	 *
	 * @param SwatWidget $child a reference to the child object to add.
	 *
	 * @throws SwatInvalidClassException
	 */
	public function addChild(SwatObject $child)
	{
		if ($child instanceof SwatWidget) {
			$this->add($child);
		} else {
			$class_name = get_class($child);
			throw new SwatInvalidClassException(
				'Only SwatWidget objects may be nested within SwatContainer. '.
				"Attempting to add '{$class_name}'.", 0, $child);
		}
	}

	/**
	 * Gathers the SwatHtmlHeadEntry objects needed by this widget
	 *
	 * @return array the SwatHtmlHeadEntry objects needed by this widget.
	 *
	 * @see SwatWidget::gatherSwatHtmlHeadEntries()
	 */
	public function getHtmlHeadEntries()
	{
		$out = $this->html_head_entries;

		foreach ($this->children as $child_widget)
			$out = array_merge($out, $child_widget->getHtmlHeadEntries());

		return $out;
	}

	/**
	 * Notifies this widget that a widget was added
	 *
	 * This widget may want to asjust itself based on the widget added or
	 * any of the widgets children.
	 *
	 * @param SwatWidget $widget the widget that has been added.
	 */
	protected function notifyOfAdd($widget)
	{
	}

	/**
	 * Sends the notification signal up the widget tree
	 *
	 * This container is notified of the added widget and then this
	 * method is called on the container parent.
	 *
	 * @param SwatWidget $widget the widget that has been added.
	 */
	protected function sendAddNotifySignal($widget)
	{
		$this->notifyOfAdd($widget);
		
		if ($this->parent != null)
			$this->parent->sendAddNotifySignal($widget);
	}
}

?>
