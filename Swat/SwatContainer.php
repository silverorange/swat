<?php

/**
 * Swat container widget.
 *
 * Used as a base class for widgets which contain other widgets.
 *
 * @copyright 2004-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatContainer extends SwatWidget implements SwatUIParent
{
    // {{{ protected properties

    /**
     * Children widgets.
     *
     * An array containing the widgets that belong to this container.
     *
     * @var array
     */
    protected $children = [];

    /**
     * Children widgets indexed by id.
     *
     * An array containing widgets indexed by their id.  This array only
     * contains widgets that have a non-null id.
     *
     * @var array
     */
    protected $children_by_id = [];

    // }}}
    // {{{ public function init()

    /**
     * Initializes this widget.
     *
     * Recursively initializes children widgets.
     *
     * @see SwatWidget::init()
     */
    public function init()
    {
        parent::init();

        foreach ($this->children as $child_widget) {
            $child_widget->init();
        }
    }

    // }}}
    // {{{ public function add()

    /**
     * Adds a widget.
     *
     * Adds a widget as a child of this container. The widget must not have
     * a parent already. The parent of the added widget is set to
     * reference this container.
     *
     * @param SwatWidget $widget a reference to the widget to add
     */
    public function add(SwatWidget $widget)
    {
        $this->packEnd($widget);
    }

    // }}}
    // {{{ public function replace()

    /**
     * Replace a widget.
     *
     * Replaces a child widget in this container. The parent of the removed
     * widget is set to null.
     *
     * @param SwatWidget $widget a reference to the widget to be replaced
     * @param SwatWidget $widget a reference to the new widget
     *
     * @return SwatWidget a reference to the removed widget, or null if the
     *                    widget is not found
     */
    public function replace(SwatWidget $widget, SwatWidget $new_widget)
    {
        foreach ($this->children as $key => $child_widget) {
            if ($child_widget === $widget) {
                $this->children[$key] = $new_widget;
                $new_widget->parent = $this;
                $widget->parent = null;

                if ($widget->id !== null) {
                    unset($this->children_by_id[$widget->id]);
                }

                if ($new_widget->id !== null) {
                    $this->children_by_id[$new_widget->id] = $new_widget;
                }

                return $widget;
            }
        }

        return null;
    }

    // }}}
    // {{{ public function remove()

    /**
     * Removes a widget.
     *
     * Removes a child widget from this container. The parent of the widget is
     * set to null.
     *
     * @param SwatWidget $widget a reference to the widget to remove
     *
     * @return SwatWidget a reference to the removed widget, or null if the
     *                    widget is not found
     */
    public function remove(SwatWidget $widget)
    {
        foreach ($this->children as $key => $child_widget) {
            if ($child_widget === $widget) {
                // use array_splice here instead of unset in order to re-index
                // the array keys
                array_splice($this->children, $key, 1);
                $widget->parent = null;

                if ($widget->id !== null) {
                    unset($this->children_by_id[$widget->id]);
                }

                return $widget;
            }
        }

        return null;
    }

    // }}}
    // {{{ public function packStart()

    /**
     * Adds a widget to start.
     *
     * Adds a widget to the start of the list of widgets in this container.
     *
     * @param SwatWidget $widget a reference to the widget to add
     */
    public function packStart(SwatWidget $widget)
    {
        if ($widget->parent !== null) {
            throw new SwatException(
                'Attempting to add a widget that already has a parent.',
            );
        }

        array_unshift($this->children, $widget);
        $widget->parent = $this;

        if ($widget->id !== null) {
            $this->children_by_id[$widget->id] = $widget;
        }

        $this->sendAddNotifySignal($widget);
    }

    // }}}
    // {{{ public function insertBefore()

    /**
     * Adds a widget to this container before another widget.
     *
     * @param SwatWidget $widget a reference to the widget to add
     * @param SwatWidget $child  a reference to the widget to add before
     */
    public function insertBefore(SwatWidget $widget, SwatWidget $child)
    {
        if ($widget->parent !== null) {
            throw new SwatException(
                'Attempting to add a widget that already has a parent.',
            );
        }

        if ($child->parent !== $this) {
            throw new SwatException(
                'Attempting to insert before a child '
                    . 'that is not in this container.',
            );
        }

        $index = 0;
        for ($i = 0; $i < count($this->children); $i++) {
            if ($this->children[$i] === $child) {
                $index = $i;
                break;
            }
        }
        array_splice($this->children, $index, 0, [$widget]);

        $widget->parent = $this;

        if ($widget->id !== null) {
            $this->children_by_id[$widget->id] = $widget;
        }

        $this->sendAddNotifySignal($widget);
    }

    // }}}
    // {{{ public function packEnd()

    /**
     * Adds a widget to end.
     *
     * Adds a widget to the end of the list of widgets in this container.
     *
     * @param SwatWidget $widget a reference to the widget to add
     */
    public function packEnd(SwatWidget $widget)
    {
        if ($widget->parent !== null) {
            throw new SwatException(
                'Attempting to add a widget that already has a parent.',
            );
        }

        $this->children[] = $widget;
        $widget->parent = $this;

        if ($widget->id !== null) {
            $this->children_by_id[$widget->id] = $widget;
        }

        $this->sendAddNotifySignal($widget);
    }

    // }}}
    // {{{ public function getChild()

    /**
     * Gets a child widget.
     *
     * Retrieves a widget from the list of widgets in the container based on
     * the unique identifier of the widget.
     *
     * @param string $id the unique id of the widget to look for
     *
     * @return SwatWidget the found widget or null if not found
     */
    public function getChild($id)
    {
        if (array_key_exists($id, $this->children_by_id)) {
            return $this->children_by_id[$id];
        }

        return null;
    }

    // }}}
    // {{{ public function getFirst()

    /**
     * Gets the first child widget.
     *
     * Retrieves the first child widget from the list of widgets in the
     * container.
     *
     * @return SwatWidget the first widget in this container or null if there
     *                    are no widgets in this container
     */
    public function getFirst()
    {
        if (count($this->children)) {
            reset($this->children);

            return current($this->children);
        }

        return null;
    }

    // }}}
    // {{{ public function getChildren()

    /**
     * Gets all child widgets.
     *
     * Retrieves an array of all widgets directly contained by this container.
     *
     * @param string $class_name optional class name. If set, only widgets that
     *                           are instances of <code>$class_name</code> are
     *                           returned.
     *
     * @return array the child widgets of this container
     */
    public function getChildren($class_name = null)
    {
        if ($class_name === null) {
            return $this->children;
        }

        $out = [];

        foreach ($this->children as $child_widget) {
            if ($child_widget instanceof $class_name) {
                $out[] = $child_widget;
            }
        }

        return $out;
    }

    // }}}
    // {{{ public function getDescendants()

    /**
     * Gets descendant UI-objects.
     *
     * @param string $class_name optional class name. If set, only UI-objects
     *                           that are instances of <code>$class_name</code>
     *                           are returned.
     *
     * @return array the descendant UI-objects of this container. If descendant
     *               objects have identifiers, the identifier is used as the
     *               array key.
     *
     * @see SwatUIParent::getDescendants()
     */
    public function getDescendants($class_name = null)
    {
        if (
            !(
                $class_name === null
                || class_exists($class_name)
                || interface_exists($class_name)
            )
        ) {
            return [];
        }

        $out = [];

        foreach ($this->children as $child_widget) {
            if ($class_name === null || $child_widget instanceof $class_name) {
                if ($child_widget->id === null) {
                    $out[] = $child_widget;
                } else {
                    $out[$child_widget->id] = $child_widget;
                }
            }

            if ($child_widget instanceof SwatUIParent) {
                $out = array_merge(
                    $out,
                    $child_widget->getDescendants($class_name),
                );
            }
        }

        return $out;
    }

    // }}}
    // {{{ public function getFirstDescendant()

    /**
     * Gets the first descendant UI-object of a specific class.
     *
     * @param string $class_name class name to look for
     *
     * @return SwatUIObject the first descendant UI-object or null if no
     *                      matching descendant is found
     *
     * @see SwatUIParent::getFirstDescendant()
     */
    public function getFirstDescendant($class_name)
    {
        if (!class_exists($class_name) && !interface_exists($class_name)) {
            return null;
        }

        $out = null;

        foreach ($this->children as $child_widget) {
            if ($child_widget instanceof $class_name) {
                $out = $child_widget;
                break;
            }

            if ($child_widget instanceof SwatUIParent) {
                $out = $child_widget->getFirstDescendant($class_name);
                if ($out !== null) {
                    break;
                }
            }
        }

        return $out;
    }

    // }}}
    // {{{ public function getDescendantStates()

    /**
     * Gets descendant states.
     *
     * Retrieves an array of states of all stateful UI-objects in the widget
     * subtree below this container.
     *
     * @return array an array of UI-object states with UI-object identifiers as
     *               array keys
     */
    public function getDescendantStates()
    {
        $states = [];

        foreach ($this->getDescendants('SwatState') as $id => $object) {
            $states[$id] = $object->getState();
        }

        return $states;
    }

    // }}}
    // {{{ public function setDescendantStates()

    /**
     * Sets descendant states.
     *
     * Sets states on all stateful UI-objects in the widget subtree below this
     * container.
     *
     * @param array $states an array of UI-object states with UI-object
     *                      identifiers as array keys
     */
    public function setDescendantStates(array $states)
    {
        foreach ($this->getDescendants('SwatState') as $id => $object) {
            if (isset($states[$id])) {
                $object->setState($states[$id]);
            }
        }
    }

    // }}}
    // {{{ public function process()

    /**
     * Processes this container by calling {@link SwatWidget::process()} on all
     * children.
     */
    public function process()
    {
        parent::process();

        foreach ($this->children as $child) {
            if ($child !== null && !$child->isProcessed()) {
                $child->process();
            }
        }
    }

    // }}}
    // {{{ public function display()

    /**
     * Displays this container by calling {@link SwatWidget::display()} on all
     * children.
     */
    public function display()
    {
        if (!$this->visible) {
            return;
        }

        parent::display();

        $this->displayChildren();
    }

    // }}}
    // {{{ public function getMessages()

    /**
     * Gets all messages.
     *
     * @return array an array of gathered {@link SwatMessage} objects
     *
     * @see SwatWidget::getMessages()
     */
    public function getMessages()
    {
        $messages = parent::getMessages();

        foreach ($this->children as $child) {
            $messages = array_merge($messages, $child->getMessages());
        }

        return $messages;
    }

    // }}}
    // {{{ public function hasMessage()

    /**
     * Checks for the presence of messages.
     *
     * @return bool true if this container or the subtree below this
     *              container has one or more messages
     *
     * @see SwatWidget::hasMessage()
     */
    public function hasMessage()
    {
        $has_message = parent::hasMessage();

        if (!$has_message) {
            foreach ($this->children as &$child) {
                if ($child->hasMessage()) {
                    $has_message = true;
                    break;
                }
            }
        }

        return $has_message;
    }

    // }}}
    // {{{ public function addChild()

    /**
     * Adds a child object.
     *
     * This method fulfills the {@link SwatUIParent} interface. It is used
     * by {@link SwatUI} when building a widget tree and should not need to be
     * called elsewhere. To add a widget to a container use
     * {@link SwatContainer::add()}.
     *
     * @param SwatWidget $child a reference to the child object to add
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
                'Only SwatWidget objects may be nested within SwatContainer. '
                    . "Attempting to add '{$class_name}'.",
                0,
                $child,
            );
        }
    }

    // }}}
    // {{{ public function getHtmlHeadEntrySet()

    /**
     * Gets the SwatHtmlHeadEntry objects needed by this container.
     *
     * @return SwatHtmlHeadEntrySet the {@link SwatHtmlHeadEntry} objects
     *                              needed by this container
     *
     * @see SwatUIObject::getHtmlHeadEntrySet()
     */
    public function getHtmlHeadEntrySet()
    {
        $set = parent::getHtmlHeadEntrySet();

        foreach ($this->children as $child_widget) {
            $set->addEntrySet($child_widget->getHtmlHeadEntrySet());
        }

        return $set;
    }

    // }}}
    // {{{ public function getAvailableHtmlHeadEntrySet()

    /**
     * Gets the SwatHtmlHeadEntry objects that may be needed by this container.
     *
     * @return SwatHtmlHeadEntrySet the {@link SwatHtmlHeadEntry} objects that
     *                              may be needed by this container
     *
     * @see SwatUIObject::getAvailableHtmlHeadEntrySet()
     */
    public function getAvailableHtmlHeadEntrySet()
    {
        $set = parent::getAvailableHtmlHeadEntrySet();

        foreach ($this->children as $child_widget) {
            $set->addEntrySet($child_widget->getAvailableHtmlHeadEntrySet());
        }

        return $set;
    }

    // }}}
    // {{{ public function getFocusableHtmlId()

    /**
     * Gets the id attribute of the XHTML element displayed by this widget
     * that should receive focus.
     *
     * @return string the id attribute of the XHTML element displayed by this
     *                widget that should receive focus or null if there is
     *                no such element
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
    // {{{ public function printWidgetTree()

    public function printWidgetTree()
    {
        echo get_class($this), ' ', $this->id;

        $children = $this->getChildren();
        if (count($children) > 0) {
            echo '<ul>';
            foreach ($children as $child) {
                echo '<li>';
                $child->printWidgetTree();
                echo '</li>';
            }
            echo '</ul>';
        }
    }

    // }}}
    // {{{ public function copy()

    /**
     * Performs a deep copy of the UI tree starting with this UI object.
     *
     * @param string $id_suffix optional. A suffix to append to copied UI
     *                          objects in the UI tree.
     *
     * @return SwatUIObject a deep copy of the UI tree starting with this UI
     *                      object
     *
     * @see SwatUIObject::copy()
     */
    public function copy($id_suffix = '')
    {
        $copy = parent::copy($id_suffix);
        $copy->children_by_id = [];

        foreach ($this->children as $key => $child_widget) {
            $copy_child = $child_widget->copy($id_suffix);
            $copy_child->parent = $copy;
            $copy->children[$key] = $copy_child;
            if ($copy_child->id !== null) {
                $copy->children_by_id[$copy_child->id] = $copy_child;
            }
        }

        return $copy;
    }

    // }}}
    // {{{ protected function displayChildren()

    /**
     * Displays the child widgets of this container.
     *
     * Subclasses that override the display method will typically call this
     * method to display child widgets.
     */
    protected function displayChildren()
    {
        foreach ($this->children as &$child) {
            $child->display();
        }
    }

    // }}}
    // {{{ protected function notifyOfAdd()

    /**
     * Notifies this widget that a widget was added.
     *
     * This widget may want to adjust itself based on the widget added or
     * any of the widgets children.
     *
     * @param SwatWidget $widget the widget that has been added
     */
    protected function notifyOfAdd($widget) {}

    // }}}
    // {{{ protected function sendAddNotifySignal()

    /**
     * Sends the notification signal up the widget tree.
     *
     * This container is notified of the added widget and then this
     * method is called on the container parent.
     *
     * @param SwatWidget $widget the widget that has been added
     */
    protected function sendAddNotifySignal($widget)
    {
        $this->notifyOfAdd($widget);

        if ($this->parent !== null && $this->parent instanceof SwatContainer) {
            $this->parent->sendAddNotifySignal($widget);
        }
    }

    // }}}
}
