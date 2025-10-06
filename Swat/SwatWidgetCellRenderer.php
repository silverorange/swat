<?php

/**
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatWidgetCellRenderer extends SwatCellRenderer implements SwatUIParent, SwatTitleable
{
    /**
     * Identifier of this widget cell renderer.
     *
     * Identifier must be unique. This property is required and cannot be a
     * data-mapped value.
     *
     * @var string
     */
    public $id;

    /**
     * Unique value used to uniquely identify the replicated widget.
     * If null, no replicating is done and the prototype widget is used.
     */
    public $replicator_id;

    /**
     * A reference to the prototype widget for this cell renderer.
     *
     * @var SwatWidget
     */
    private $prototype_widget;

    private $mappings = [];
    private $clones = [];
    private $widgets = [];
    private $property_values = [];

    /**
     * Whether or not renderings of this cell renderer are using a dynamic
     * {@link SwatWidgetCellRenderer::$replicator_id} to create cloned widgets.
     *
     * @var bool
     *
     * @see SwatWidgetCellRenderer::render()
     */
    private $using_clone_replication = false;

    /**
     * Whether or not renderings of this cell renderer are using a null
     * {@link SwatWidgetCellRenderer::$replicator_id} and rendering the
     * prototype widget instead of cloned widgets.
     *
     * @var bool
     *
     * @see SwatWidgetCellRenderer::render()
     */
    private $using_null_replication = false;

    /**
     * Creates a new radio button cell renderer.
     */
    public function __construct()
    {
        parent::__construct();

        $this->makePropertyStatic('id');

        // auto-generate an id to use if no id is set
        $this->id = $this->getUniqueId();
    }

    /**
     * Fulfills SwatUIParent::addChild().
     *
     * @param SwatWidget $child
     *
     * @throws SwatException
     */
    public function addChild(SwatObject $child)
    {
        if ($this->prototype_widget === null) {
            $this->setPrototypeWidget($child);
        } else {
            throw new SwatException(
                'Can only add one widget to a widget cell renderer',
            );
        }
    }

    public function getPropertyNameToMap(SwatUIObject $object, $name)
    {
        if ($this === $object) {
            return $name;
        }

        $mangled_name = $name;
        $suffix = 0;

        if (property_exists($this, $name)) {
            $mangled_name = $name . $suffix;
            $suffix++;
        }

        while (array_key_exists($mangled_name, $this->mappings)) {
            $mangled_name = $name . $suffix;
            $suffix++;
        }

        $this->mappings[$mangled_name] = [
            'object'   => $object,
            'property' => $name,
        ];

        return $mangled_name;
    }

    /**
     * Initializes this cell renderer.
     *
     * This calls {@link SwatWidget::init()} on this renderer's widget.
     */
    public function init()
    {
        $replicators = null;

        $form = $this->getForm();
        if ($form !== null && $form->isSubmitted()) {
            $replicators = $form->getHiddenField(
                $this->getReplicatorFieldName(),
            );

            if ($replicators !== null) {
                foreach ($replicators as $replicator) {
                    $this->createClonedWidget($replicator);
                }
            }
        }

        if ($replicators === null) {
            $this->prototype_widget->init();
        }
    }

    public function process()
    {
        $form = $this->getForm();
        if ($form === null) {
            $replicators = null;
        } else {
            $replicators = $form->getHiddenField(
                $this->getReplicatorFieldName(),
            );
        }

        if ($replicators === null) {
            if (
                $this->prototype_widget !== null
                && !$this->prototype_widget->isProcessed()
            ) {
                $this->prototype_widget->process();
            }
        } else {
            foreach ($replicators as $replicator) {
                $widget = $this->getClonedWidget($replicator);
                if (!$widget->isProcessed()) {
                    $widget->process();
                }
            }
        }
    }

    /**
     * @throws SwatException
     */
    public function render()
    {
        if (!$this->visible) {
            return;
        }

        parent::render();

        if ($this->replicator_id === null) {
            if ($this->using_clone_replication) {
                throw new SwatException(
                    'Cannot mix null replicator_id values '
                        . 'with non-null replicator_id values. Make sure this '
                        . 'widget cell renderer\'s replicator_id is set before '
                        . 'rendering this renderer.',
                );
            }

            if ($this->prototype_widget !== null) {
                $this->applyPropertyValuesToPrototypeWidget();
                $this->prototype_widget->display();
            }

            $this->using_null_replication = true;
        } else {
            if ($this->using_null_replication) {
                throw new SwatException(
                    'Cannot mix non-null replicator_id '
                        . 'values with null replicator_id values. All prior '
                        . 'renderings of this widget cell renderer have had a null '
                        . 'value for the replicator_id.',
                );
            }

            if ($this->prototype_widget->id === null) {
                throw new SwatException(
                    'Prototype widget must have a non-null id.',
                );
            }

            $widget = $this->getClonedWidget($this->replicator_id);
            if ($widget === null) {
                return;
            }

            $form = $this->getForm();
            if ($form === null) {
                throw new SwatException(
                    'Cell renderer container must be '
                        . 'inside a SwatForm for SwatWidgetCellRenderer to work.',
                );
            }

            $form->addHiddenField(
                $this->getReplicatorFieldName(),
                array_keys($this->clones),
            );

            $this->applyPropertyValuesToClonedWidget($widget);
            $widget->display();

            $this->using_clone_replication = true;
        }
    }

    public function setPrototypeWidget(SwatWidget $widget)
    {
        $this->prototype_widget = $widget;
        $widget->parent = $this;
    }

    /**
     * Gets the prototype widget of this widget cell renderer.
     *
     * @return SwatWidget the prototype widget of this widget cell renderer
     */
    public function getPrototypeWidget()
    {
        return $this->prototype_widget;
    }

    /**
     * Retrieves a reference to a replicated widget.
     *
     * @param string $replicator_id the replicator id of the replicated widget
     * @param string $widget_id     the unique id of the original widget, or null
     *                              to get the root
     *
     * @return SwatWidget a reference to the replicated widget, or null if the
     *                    widget is not found
     */
    public function getWidget($replicator_id, $widget_id = null)
    {
        $widget = null;

        if ($widget_id === null) {
            if (isset($this->widgets[$replicator_id])) {
                $widget = current($this->widgets[$replicator_id]);
            }
        } else {
            if (isset($this->widgets[$replicator_id][$widget_id])) {
                $widget = $this->widgets[$replicator_id][$widget_id];
            }
        }

        return $widget;
    }

    /**
     * Gets an array of replicated widgets indexed by the replicator_id.
     *
     * If this cell renderer's form is submitted, only cloned widgets that were
     * displayed and processed are returned.
     *
     * @param string $widget_id the unique id of the original widget, or null
     *                          to get the root
     *
     * @return array an array of widgets indexed by replicator_id
     *
     * @throws SwatWidgetNotFoundException if a widget id is specified and no
     *                                     such widget exists in the subtree
     */
    public function getWidgets($widget_id = null)
    {
        $form = $this->getForm();
        if ($form !== null && $form->isSubmitted()) {
            $replicators = $form->getHiddenField(
                $this->getReplicatorFieldName(),
            );

            $widgets = [];
            if (is_array($replicators)) {
                foreach ($this->clones as $replicator_id => $clone) {
                    if (in_array($replicator_id, $replicators)) {
                        if (
                            $widget_id !== null
                            && !isset($this->widgets[$replicator_id][$widget_id])
                        ) {
                            throw new SwatWidgetNotFoundException(
                                sprintf(
                                    'No widget with the id "%s" exists in the '
                                        . 'cloned widget sub-tree of this '
                                        . 'SwatWidgetCellRenderer.',
                                    $widget_id,
                                ),
                                0,
                                $widget_id,
                            );
                        }

                        $widgets[$replicator_id] = $this->getWidget(
                            $replicator_id,
                            $widget_id,
                        );
                    }
                }
            }
        } else {
            $widgets = $this->clones;
        }

        return $widgets;
    }

    /**
     * Gets an array of cloned widgets indexed by the replicator_id.
     *
     * If this cell renderer's form is submitted, only cloned widgets that were
     * displayed and processed are returned.
     *
     * @deprecated Use {@link SwatWidgetCellRenderer::getWidgets()} instead.
     *              Pass null to getWidgets() for the same output as this
     *              method.
     *
     * @return array an array of widgets indexed by replicator_id
     */
    public function &getClonedWidgets()
    {
        $form = $this->getForm();
        if ($form !== null && $form->isSubmitted()) {
            $replicators = $form->getHiddenField(
                $this->getReplicatorFieldName(),
            );

            $clones = [];
            foreach ($this->clones as $replicator_id => $clone) {
                if (
                    is_array($replicators)
                    && in_array($replicator_id, $replicators)
                ) {
                    $clones[$replicator_id] = $clone;
                }
            }
        } else {
            $clones = $this->clones;
        }

        return $clones;
    }

    /**
     * Gets the data specific CSS class names for this widget cell renderer.
     *
     * If the widget within this cell renderer has messages, a CSS class of
     * 'swat-error' is added to the base CSS classes of this cell renderer.
     *
     * @return array the array of data specific CSS class names for this widget
     *               cell-renderer
     */
    public function getDataSpecificCSSClassNames()
    {
        $classes = [];

        if (
            $this->replicator_id !== null
            && $this->hasMessage($this->replicator_id)
        ) {
            $classes[] = 'swat-error';
        }

        return $classes;
    }

    /**
     * Gathers all messages from the widget of this cell renderer for the given
     * replicator id.
     *
     * @param mixed $replicator_id an optional replicator id of the row to
     *                             gather messages from. If no replicator id
     *                             is specified, the current replicator_id is
     *                             used.
     *
     * @return array an array of {@link SwatMessage} objects
     */
    public function getMessages($replicator_id = null)
    {
        $messages = [];

        if ($replicator_id !== null) {
            $messages = $this->getClonedWidget($replicator_id)->getMessages();
        } elseif ($this->replicator_id !== null) {
            $messages = $this->getClonedWidget(
                $this->replicator_id,
            )->getMessages();
        }

        return $messages;
    }

    /**
     * Gets whether or not this widget cell renderer has messages.
     *
     * @param mixed $replicator_id an optional replicator id of the row to
     *                             check for messages. If no replicator id is
     *                             specified, the current replicator_id is
     *                             used.
     *
     * @return bool true if this widget cell renderer has one or more
     *              messages for the given replicator id and false if it
     *              does not
     */
    public function hasMessage($replicator_id = null)
    {
        $has_message = false;

        if ($replicator_id !== null) {
            $has_message = $this->getClonedWidget($replicator_id)->hasMessage();
        } elseif ($this->replicator_id !== null) {
            $has_message = $this->getClonedWidget(
                $this->replicator_id,
            )->hasMessage();
        }

        return $has_message;
    }

    /**
     * Gets the title of this widget cell renderer.
     *
     * The title is taken from this cell renderer's parent.
     * Satisfies the {SwatTitleable::getTitle()} interface.
     *
     * @return string the title of this widget cell renderer
     */
    public function getTitle()
    {
        $title = null;

        if (isset($this->parent->title)) {
            $title = $this->parent->title;
        }

        return $title;
    }

    /**
     * Gets the title content-type of this widget cell renderer.
     *
     * Implements the {@link SwatTitleable::getTitleContentType()} interface.
     *
     * @return string the title content-type of this widget cell renderer
     */
    public function getTitleContentType()
    {
        $title_content_type = 'text/plain';

        if (isset($this->parent->title_content_type)) {
            $title_content_type = $this->parent->title_content_type;
        }

        return $title_content_type;
    }

    /**
     * Gets the SwatHtmlHeadEntry objects needed by this widget cell renderer.
     *
     * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects needed by
     *                              this widget cell renderer
     *
     * @see SwatUIObject::getHtmlHeadEntrySet()
     */
    public function getHtmlHeadEntrySet()
    {
        $set = parent::getHtmlHeadEntrySet();

        if ($this->using_null_replication) {
            $set->addEntrySet(
                $this->getPrototypeWidget()->getHtmlHeadEntrySet(),
            );
        } else {
            $widgets = $this->getWidgets();
            foreach ($widgets as $widget) {
                $set->addEntrySet($widget->getHtmlHeadEntrySet());
            }
        }

        return $set;
    }

    /**
     * Gets the SwatHtmlHeadEntry objects that may be needed by this widget
     * cell renderer.
     *
     * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects that may be
     *                              needed by this widget cell renderer
     *
     * @see SwatUIObject::getAvailableHtmlHeadEntrySet()
     */
    public function getAvailableHtmlHeadEntrySet()
    {
        $set = parent::getAvailableHtmlHeadEntrySet();

        if ($this->using_null_replication) {
            $set->addEntrySet(
                $this->getPrototypeWidget()->getAvailableHtmlHeadEntrySet(),
            );
        } else {
            $widgets = $this->getWidgets();
            foreach ($widgets as $widget) {
                $set->addEntrySet($widget->getAvailableHtmlHeadEntrySet());
            }
        }

        return $set;
    }

    /**
     * Gets descendant UI-objects.
     *
     * The descendant UI-objects of a widget cell renderer are cloned widgets,
     * not the prototype widget.
     *
     * @param string $class_name optional class name. If set, only UI-objects
     *                           that are instances of <i>$class_name</i> are
     *                           returned.
     *
     * @return array the descendant UI-objects of this widget cell renderer. If
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

        foreach ($this->getWidgets() as $cloned_widget) {
            if ($class_name === null || $cloned_widget instanceof $class_name) {
                if ($cloned_widget->id === null) {
                    $out[] = $cloned_widget;
                } else {
                    $out[$cloned_widget->id] = $cloned_widget;
                }
            }

            if ($cloned_widget instanceof SwatUIParent) {
                $out = array_merge(
                    $out,
                    $cloned_widget->getDescendants($class_name),
                );
            }
        }

        return $out;
    }

    /**
     * Gets the first descendant UI-object of a specific class.
     *
     * The descendant UI-objects of a widget cell renderer are cloned widgets,
     * not the prototype widget.
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

        foreach ($this->getWidgets() as $cloned_widget) {
            if ($cloned_widget instanceof $class_name) {
                $out = $cloned_widget;
                break;
            }

            if ($cloned_widget instanceof SwatUIParent) {
                $out = $cloned_widget->getFirstDescendant($class_name);
                if ($out !== null) {
                    break;
                }
            }
        }

        return $out;
    }

    /**
     * Gets descendant states.
     *
     * Retrieves an array of states of all stateful UI-objects in the widget
     * subtree below this widget cell renderer.
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
     * widget cell renderer.
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
     * Maps a data field to a property of a widget in the widget tree.
     *
     * TODO: document me better
     *
     * @param mixed $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->mappings)) {
            $this->property_values[$name] = $value;
        } else {
            // TODO: throw something meaningful
            throw new SwatException();
        }
    }

    protected function getReplicatorFieldName()
    {
        $name = $this->id . '_replicators';

        $widget = $this->getFirstAncestor('SwatWidget');
        if ($widget->id) {
            $name = $widget->id . '_' . $name;
        }

        return $name;
    }

    /**
     * Gets the form.
     *
     * @return SwatForm the form this cell renderer's view is contained in
     */
    private function getForm()
    {
        return $this->getFirstAncestor('SwatForm');
    }

    private function getClonedWidget($replicator)
    {
        if (!isset($this->clones[$replicator])) {
            $this->createClonedWidget($replicator);
        }

        return $this->clones[$replicator];
    }

    private function applyPropertyValuesToPrototypeWidget()
    {
        foreach ($this->property_values as $name => $value) {
            $object = $this->mappings[$name]['object'];
            $property = $this->mappings[$name]['property'];
            $object->{$property} = $value;
        }
    }

    private function applyPropertyValuesToClonedWidget($cloned_widget)
    {
        foreach ($this->property_values as $name => $value) {
            $object = $this->mappings[$name]['object'];
            $property = $this->mappings[$name]['property'];

            $prototype_descendants = [$this->prototype_widget];
            $cloned_descendants = [$cloned_widget];

            if ($this->prototype_widget instanceof SwatUIParent) {
                $prototype_descendants = array_merge(
                    $prototype_descendants,
                    $this->prototype_widget->getDescendants(),
                );

                $cloned_descendants = array_merge(
                    $cloned_descendants,
                    $cloned_widget->getDescendants(),
                );
            }

            $cloned_object = null;
            foreach ($prototype_descendants as $index => $prototype_object) {
                if ($object === $prototype_object) {
                    $cloned_object
                        = $this->widgets[$this->replicator_id][$object->id];

                    break;
                }
            }

            if ($cloned_object === null) {
                throw new SwatException(
                    'Cloned widget tree does not match '
                        . 'prototype widget tree.',
                );
            }

            if ($cloned_object->{$property} instanceof SwatCellRendererMapping) {
                $cloned_object->{$property} = $value;
            }
        }
    }

    private function createClonedWidget($replicator)
    {
        if ($this->prototype_widget === null) {
            return;
        }

        $suffix = '_' . $this->id . '_' . $replicator;
        $new_widget = $this->prototype_widget->copy($suffix);
        $new_widget->parent = $this;
        $this->widgets[$replicator] = [];

        if ($new_widget->id !== null) {
            // lookup array uses original ids
            $old_id = mb_substr($new_widget->id, 0, -mb_strlen($suffix));
            $this->widgets[$replicator][$old_id] = $new_widget;
        }

        if ($new_widget instanceof SwatUIParent) {
            foreach ($new_widget->getDescendants() as $descendant) {
                if ($descendant->id !== null) {
                    // lookup array uses original ids
                    $old_id = mb_substr(
                        $descendant->id,
                        0,
                        -mb_strlen($suffix),
                    );
                    $this->widgets[$replicator][$old_id] = $descendant;
                }
            }
        }

        $new_widget->init();

        $this->clones[$replicator] = $new_widget;
    }
}
