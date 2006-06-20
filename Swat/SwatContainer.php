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
 * @copyright 2004-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatContainer extends SwatWidget implements SwatUIParent
{
	// {{{ protected properties

	/**
	 * Children widgets
	 *
	 * An array containing the widgets that belong to this container.
	 *
	 * @var array
	 */
	protected $children = array();

	/**
	 * Children widgets indexed by id
	 *
	 * An array containing widgets indexed by their id.  This array only
	 * contains widgets that have a non-null id.
	 *
	 * @var array
	 */
	protected $children_by_id = array();

	// }}}
	// {{{ public function __clone()

	public function __clone()
	{
		$children = $this->children;
		$this->children = array();
		$this->children_by_id = array();

		foreach ($children as $key => $child) {
			$new_child = clone $child;
			$this->children[$key] = $new_child;
			if ($new_child->id !== null)
				$this->children_by_id[$new_child->id] = $new_child;
		}
	}

	// }}}
	// {{{ public function init()

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

	// }}}
	// {{{ public function add()

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

	// }}}
	// {{{ public function replace()

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
				$new_widget->parent = $this;
				$widget->parent = null;

				if ($widget->id !== null)
					unset($this->children_by_id[$widget->id]);

				if ($new_widget->id !== null)
					$this->children_by_id[$new_widget->id] = $new_widget;

				return $widget;
			}
		}
		return null;
	}

	// }}}
	// {{{ public function remove()

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

				if ($widget->id !== null)
					unset($this->children_by_id[$widget->id]);

				return $widget;
			}
		}
		return null;
	}

	// }}}
	// {{{ public function packStart()

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

		if ($widget->id !== null)
				$this->children_by_id[$widget->id] = $widget;

		$this->sendAddNotifySignal($widget);
	}

	// }}}
	// {{{ public function packEnd()

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

		if ($widget->id !== null)
				$this->children_by_id[$widget->id] = $widget;

		$this->sendAddNotifySignal($widget);
	}

	// }}}
	// {{{ public function getChild()

	/**
	 * Gets a child widget
	 *
	 * Retrieves a widget from the list of widgets in the container based on
	 * the unique identifier of the widget.
	 *
	 * @param string $id the unique id of the widget to look for.
	 *
	 * @return SwatWidget the found widget or null not found.
	 */
	public function getChild($id)
	{
		if (array_key_exists($id, $this->children_by_id))
			return $this->children_by_id[$id];
		else
			return null;
	}

	// }}}
	// {{{ public function getFirst()

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

	// }}}
	// {{{ public function getChildren()

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

	// }}}
	// {{{ public function getDescendants()

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

	// }}}
	// {{{ public function getFirstDescendant()

	/**
	 * Gets the first descendent widget of a specific class
	 *
	 * Retrieves the first descendant widget in the subtree that is a 
	 * descendant of the specified class name. This uses a depth-first
	 * traversal to search the tree.
	 *
	 * @param string $class_name class name to look for.
	 *
	 * @return mixed the first descendant widget or null if no matching
	 *                descendant is found.
	 *
	 * @see SwatWidget::getFirstAncestor()
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

	// }}}
	// {{{ public function getDescendantStates()

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

	// }}}
	// {{{ public function setDescendantStates()

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

	// }}}
	// {{{ public function process()

	/**
	 * Processes this container by calling process() on all children
	 */
	public function process()
	{
		foreach ($this->children as $child) {
			if ($child !== null && !$child->isProcessed())
				$child->process();
		}
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this container by calling display() on all children
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$this->displayChildren();
	}

	// }}}
	// {{{ protected function displayChildren()

	/**
	 * Displays the child widgets of this container
	 *
	 * Subclasses that override the display method will typically call this
	 * method to display child widgets.
	 */
	protected function displayChildren()
	{
		foreach ($this->children as &$child)
			$child->display();
	}

	// }}}
	// {{{ public function addMessage()

	/**
	 * Adds a message
	 *
	 * @param SwatMessage the message object to add.
	 *
	 * @see SwatWidget::AddMessage()
	 */
	public function addMessage(SwatMessage $message)
	{
		$this->messages[] = $message;
	}

	// }}}
	// {{{ public function getMessages()

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

	// }}}
	// {{{ public function hasMessage()

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

	// }}}
	// {{{ public function addChild()

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

	// }}}
	// {{{ public function getHtmlHeadEntrySet()

	/**
	 * Gets the SwatHtmlHeadEntry objects needed by this container
	 *
	 * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects needed by
	 *                               this container.
	 *
	 * @see SwatUIObject::getHtmlHeadEntrySet()
	 */
	public function getHtmlHeadEntrySet()
	{
		$set = parent::getHtmlHeadEntrySet();

		foreach ($this->children as $child_widget)
			$set->addEntrySet($child_widget->getHtmlHeadEntrySet());

		return $set;
	}

	// }}}
	// {{{ public function getFocusableHtmlId()

	/**
	 * Gets the id attribute of the XHTML element displayed by this widget
	 * that should receive focus
	 *
	 * @return string the id attribute of the XHTML element displayed by this
	 *                 widget that should receive focus or null if there is
	 *                 no such element.
	 *
	 * @see SwatWidget::getFocusableHtmlId()
	 */
	public function getFocusableHtmlId()
	{
		$focus_id = null;

		$children = $this->getChildren();
		foreach ($children as $child) {
			$child_focus_id = $child->getFocusableHtmlId();
			if ($child_focus_id !== null) {
				$focus_id = $child_focus_id;
				break;
			}
		}

		return $focus_id;
	}

	// }}}
	// {{{ protected function notifyOfAdd()

	/**
	 * Notifies this widget that a widget was added
	 *
	 * This widget may want to adjust itself based on the widget added or
	 * any of the widgets children.
	 *
	 * @param SwatWidget $widget the widget that has been added.
	 */
	protected function notifyOfAdd($widget)
	{
	}

	// }}}
	// {{{ protected function sendAddNotifySignal()

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
		
		if ($this->parent !== null && $this->parent instanceof SwatContainer)
			$this->parent->sendAddNotifySignal($widget);
	}

	// }}}
}

?>
