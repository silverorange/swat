<?php

/**
 * A single entry in a {@link SwatActions} widget.
 *
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see SwatActions
 */
class SwatActionItem extends SwatControl implements SwatUIParent
{
    /**
     * A unique identifier for this action item.
     *
     * @var string
     */
    public $id;

    /**
     * A human readable title displayed for this item.
     *
     * @var string
     */
    public $title = '';

    /**
     * A SwatWidget associated with this action item.
     *
     * @var SwatWidget
     */
    public $widget;

    /**
     * Initializes this action item.
     *
     * This initializes the widget contained in this action item if there is
     * one.
     */
    public function init()
    {
        parent::init();

        if ($this->widget !== null) {
            $this->widget->init();
        }
    }

    /**
     * Displays this item.
     *
     * Calls this item's widget display method.
     */
    public function display()
    {
        if (!$this->visible) {
            return;
        }

        parent::display();

        $this->widget->display();
    }

    /**
     * Sets the widget to use for this item.
     *
     * Each SwatActionItem can have one associated SwatWidget. This method
     * sets the widget for this item.
     *
     * @param SwatWidget $widget the widget associated with this action
     *
     * @throws SwatException
     */
    public function setWidget(SwatWidget $widget)
    {
        if ($this->widget !== null) {
            throw new SwatException(
                'SwatUI: Only one widget can be nested ' .
                    'within a SwatActionItem',
            );
        }

        $this->widget = $widget;
        $widget->parent = $this;
    }

    /**
     * Adds a child object.
     *
     * This method fulfills the {@link SwatUIParent} interface. It is used
     * by {@link SwatUI} when building a widget tree and should not need to be
     * called elsewhere. To set the a widget in an action item, use
     * {@link SwatActionItem::setWidget()}.
     *
     * @param SwatWidget $child a reference to a child object to add
     *
     * @throws SwatInvalidClassException
     *
     * @see SwatUIParent
     * @see SwatActionItem::setWidget()
     */
    public function addChild(SwatObject $child)
    {
        if ($child instanceof SwatWidget) {
            $this->setWidget($child);
        } else {
            throw new SwatInvalidClassException(
                'Only SwatWidget objects may be nested within a ' .
                    'SwatActionItem object.',
                0,
                $child,
            );
        }
    }

    /**
     * Gets the SwatHtmlHeadEntry objects needed by this action item.
     *
     * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects needed by
     *                              this action item
     *
     * @see SwatWidget::getHtmlHeadEntrySet()
     */
    public function getHtmlHeadEntrySet()
    {
        $set = parent::getHtmlHeadEntrySet();

        if ($this->widget !== null) {
            $set->addEntrySet($this->widget->getHtmlHeadEntrySet());
        }

        return $set;
    }

    /**
     * Gets the SwatHtmlHeadEntry objects that may be needed by this action item.
     *
     * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects that may be
     *                              neededthis action item
     *
     * @see SwatWidget::getAvailableHtmlHeadEntrySet()
     */
    public function getAvailableHtmlHeadEntrySet()
    {
        $set = parent::getAvailableHtmlHeadEntrySet();

        if ($this->widget !== null) {
            $set->addEntrySet($this->widget->getAvailableHtmlHeadEntrySet());
        }

        return $set;
    }

    /**
     * Gets descendant UI-objects.
     *
     * @param string $class_name optional class name. If set, only UI-objects
     *                           that are instances of <i>$class_name</i> are
     *                           returned.
     *
     * @return array the descendant UI-objects of this action item. If
     *               descendant objects have identifiers, the identifier is
     *               used as the array key.
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

        if ($this->widget !== null) {
            if ($class_name === null || $this->widget instanceof $class_name) {
                if ($this->widget->id === null) {
                    $out[] = $this->widget;
                } else {
                    $out[$this->widget->id] = $this->widget;
                }
            }

            if ($this->widget instanceof SwatUIParent) {
                $out = array_merge(
                    $out,
                    $this->widget->getDescendants($class_name),
                );
            }
        }

        return $out;
    }

    /**
     * Gets the first descendant UI-object of a specific class.
     *
     * @param string $class_name class name to look for
     *
     * @return SwatUIObject the first descendant widget or null if no matching
     *                      descendant is found
     *
     * @see SwatUIParent::getFirstDescendant()
     */
    public function getFirstDescendant($class_name)
    {
        if (!class_exists($class_name) && !interface_exists($class_name)) {
            return null;
        }

        $out = null;

        if ($this->widget instanceof $class_name) {
            $out = $this->widget;
        }

        if ($out === null && $this->widget instanceof SwatUIParent) {
            $out = $this->widget->getFirstDescendant($class_name);
        }

        return $out;
    }

    /**
     * Gets descendant states.
     *
     * Retrieves an array of states of all stateful UI-objects in the widget
     * subtree below this action item.
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

    /**
     * Sets descendant states.
     *
     * Sets states on all stateful UI-objects in the widget subtree below this
     * action item.
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

        if ($this->widget !== null) {
            $copy_widget = $this->widget->copy($id_suffix);
            $copy_widget->parent = $copy;
            $copy->widget = $copy_widget;
        }

        return $copy;
    }
}
