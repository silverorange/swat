<?php

/**
 * Base class for all widgets
 *
 * <strong>Widget composition:</strong>
 *
 * Complicated widgets composed of multiple individual widgets can be easily
 * built using <code>SwatWidget</code>'s composite features. The main methods
 * used for widget composition are:
 * {@link SwatWidget::createCompositeWidgets()},
 * {@link SwatWidget::addCompositeWidget()} and
 * {@link SwatWidget::getCompositeWidget()}.
 *
 * Developers should implement the <code>createCompositeWidgets()</code> method
 * by creating composite widgets and adding them to this widget by calling
 * <code>addCompositeWidget()</code>. As long as the parent implemtations of
 * {@link SwatWidget::init()} and {@link SwatWidget::process()} are called,
 * nothing further needs to be done for <code>init()</code> and
 * <code>process()</code>. For the {@link SwatWidget::display()} method,
 * developers can use the <code>getCompositeWidget()</code> method to retrieve
 * a specific composite widget for display. Composite widgets are <i>not</i>
 * displayed by the default implementation of <code>display()</code>.
 *
 * In keeping with object-oriented composition theory, none of the composite
 * widgets are publicly accessible. Methods could be added to make composite
 * widgets available publicly, but in that case it would be better to just
 * extend {@link SwatContainer}.
 *
 * @package   Swat
 * @copyright 2004-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatWidget extends SwatUIObject
{


    /**
     * A non-visible unique id for this widget, or null
     *
     * @var string
     */
    public $id = null;

    /**
     * Sensitive
     *
     * Whether the widget is sensitive. If a widget is sensitive it reacts to
     * user input. Insensitive widgets should display "grayed-out" to inform
     * the user they are not sensitive. All widgets that the user can interact
     * with should respect this property.
     *
     * @var boolean
     */
    public $sensitive = true;

    /**
     * Stylesheet
     *
     * The URI of a stylesheet for use with this widget. If this property is
     * set before {@link SwatWidget::init()} then the
     * {@link SwatUIObject::addStyleSheet()} method will be called to add this
     * stylesheet to the header entries. Primarily this should be used by
     * SwatUI to set a stylesheet in SwatML. To set a stylesheet in PHP code,
     * it is recommended to call <code>addStyleSheet()</code> directly.
     *
     * @var string
     */
    public $stylesheet = null;



    /**
     * Composite widgets of this widget
     *
     * Array is of the form 'key' => widget.
     *
     * @var array
     */
    private $composite_widgets = [];

    /**
     * Whether or not composite widgets have been created
     *
     * This flag is used by {@link SwatWidget::confirmCompositeWidgets()} to
     * ensure composite widgets are only created once.
     *
     * @var boolean
     */
    private $composite_widgets_created = false;



    /**
     * Messages affixed to this widget
     *
     * @var array
     */
    protected $messages = [];

    /**
     * Specifies that this widget requires an id
     *
     * If an id is required then the init() method sets a unique id if an id
     * is not already set manually.
     *
     * @var boolean
     *
     * @see SwatWidget::init()
     */
    protected $requires_id = false;

    /**
     * Whether or not this widget has been initialized
     *
     * @var boolean
     *
     * @see SwatWidget::init()
     */
    protected $initialized = false;

    /**
     * Whether or not this widget has been processed
     *
     * @var boolean
     *
     * @see SwatWidget::process()
     */
    protected $processed = false;

    /**
     * Whether or not this widget has been displayed
     *
     * @var boolean
     *
     * @see SwatWidget::display()
     */
    protected $displayed = false;



    /**
     * Creates a new widget
     *
     * @param string $id a non-visible unique id for this widget.
     */
    public function __construct($id = null)
    {
        parent::__construct();

        $this->id = $id;
        $this->addStylesheet('packages/swat/styles/swat.css');
    }



    /**
     * Initializes this widget
     *
     * Initialization is done post-construction. Initilization may be done
     * manually by calling <code>init()</code> on the UI tree at any time. If a
     * call to {@link SwatWidget::process()} or {@link SwatWidget::display()}
     * is made  before the tree is initialized, this method is called
     * automatically. As a result, you often do not need to worry about calling
     * <code>init()</code>.
     *
     * Having an initialization method separate from the constructor allows
     * properties to be manually set on widgets after construction but before
     * initilization.
     *
     * Composite widgets of this widget are automatically initialized as well.
     */
    public function init()
    {
        if ($this->requires_id && $this->id === null) {
            $this->id = $this->getUniqueId();
        }

        if ($this->stylesheet !== null) {
            $this->addStyleSheet($this->stylesheet);
        }

        foreach ($this->getCompositeWidgets() as $widget) {
            $widget->init();
        }

        $this->initialized = true;
    }



    /**
     * Processes this widget
     *
     * After a form submit, this widget processes itself and its dependencies
     * and then recursively processes  any of its child widgets.
     *
     * Composite widgets of this widget are automatically processed as well.
     *
     * If this widget has not been initialized, it is automatically initialized
     * before processing.
     */
    public function process()
    {
        if (!$this->isInitialized()) {
            $this->init();
        }

        foreach ($this->getCompositeWidgets() as $widget) {
            $widget->process();
        }

        $this->processed = true;
    }



    /**
     * Displays this widget
     *
     * Displays this widget displays as well as recursively displays any child
     * widgets of this widget.
     *
     * If this widget has not been initialized, it is automatically initialized
     * before displaying.
     */
    public function display()
    {
        if (!$this->isInitialized()) {
            $this->init();
        }

        $this->displayed = true;
    }



    /**
     * Displays the HTML head entries for this widget
     *
     * Each entry is displayed on its own line. This method should
     * be called inside the <head /> element of the layout.
     */
    public function displayHtmlHeadEntries()
    {
        $set = $this->getHtmlHeadEntrySet();
        $set->display();
    }



    /**
     * Gets the SwatHtmlHeadEntry objects needed by this widget
     *
     * If this widget has not been displayed, an empty set is returned to
     * reduce the number of required HTTP requests.
     *
     * @return SwatHtmlHeadEntrySet the {@link SwatHtmlHeadEntry} objects
     *                               needed by this widget.
     */
    public function getHtmlHeadEntrySet()
    {
        if ($this->isDisplayed()) {
            $set = new SwatHtmlHeadEntrySet($this->html_head_entry_set);
        } else {
            $set = new SwatHtmlHeadEntrySet();
        }

        foreach ($this->getCompositeWidgets() as $widget) {
            $set->addEntrySet($widget->getHtmlHeadEntrySet());
        }

        return $set;
    }



    /**
     * Gets the SwatHtmlHeadEntry objects that may be needed by this widget
     *
     * @return SwatHtmlHeadEntrySet the {@link SwatHtmlHeadEntry} objects that
     *                               may be needed by this widget.
     */
    public function getAvailableHtmlHeadEntrySet()
    {
        $set = new SwatHtmlHeadEntrySet($this->html_head_entry_set);

        foreach ($this->getCompositeWidgets() as $widget) {
            $set->addEntrySet($widget->getAvailableHtmlHeadEntrySet());
        }

        return $set;
    }



    /**
     * Adds a message to this widget
     *
     * The message may be shown by the {@link SwatWidget::display()} method and
     * will as cause {@link SwatWidget::hasMessage()} to return as true.
     *
     * @param SwatMessage $message the message to add.
     */
    public function addMessage(SwatMessage $message)
    {
        $this->messages[] = $message;
    }



    /**
     * Gets all messages
     *
     * Gathers all messages from children of this widget and this widget
     * itself.
     *
     * Messages from composite widgets of this widget are included by default.
     *
     * @return array an array of {@link SwatMessage} objects.
     */
    public function getMessages()
    {
        $messages = $this->messages;
        foreach ($this->getCompositeWidgets() as $widget) {
            $messages = array_merge($messages, $widget->getMessages());
        }

        return $messages;
    }



    /**
     * Checks for the presence of messages
     *
     * @return boolean true if this widget or the subtree below this widget has
     *                  one or more messages.
     */
    public function hasMessage()
    {
        $has_message = count($this->messages) > 0;

        if (!$has_message) {
            foreach ($this->getCompositeWidgets() as $widget) {
                if ($widget->hasMessage()) {
                    $has_message = true;
                    break;
                }
            }
        }

        return $has_message;
    }



    /**
     * Determines the sensitivity of this widget.
     *
     * Looks at the sensitive property of the ancestors of this widget to
     * determine if this widget is sensitive.
     *
     * @return boolean whether this widget is sensitive.
     *
     * @see SwatWidget::$sensitive
     */
    public function isSensitive()
    {
        if ($this->parent !== null && $this->parent instanceof SwatWidget) {
            return $this->parent->isSensitive() && $this->sensitive;
        } else {
            return $this->sensitive;
        }
    }



    /**
     * Whether or not this widget is initialized
     *
     * @return boolean whether or not this widget is initialized.
     */
    public function isInitialized()
    {
        return $this->initialized;
    }



    /**
     * Whether or not this widget is processed
     *
     * @return boolean whether or not this widget is processed.
     */
    public function isProcessed()
    {
        return $this->processed;
    }



    /**
     * Whether or not this widget is displayed
     *
     * @return boolean whether or not this widget is displayed.
     */
    public function isDisplayed()
    {
        return $this->displayed;
    }



    /**
     * Gets the id attribute of the XHTML element displayed by this widget
     * that should receive focus
     *
     * Elements receive focus either through JavaScript methods or by clicking
     * on label elements with their for attribute set. If there is no such
     * element (for example, there are several elements and none is more
     * important than the others) then null is returned.
     *
     * By default, widgets return null and are un-focusable. Sub-classes that
     * are focusable should override this method to return the appripriate
     * XHTML id.
     *
     * @return string the id attribute of the XHTML element displayed by this
     *                 widget that should receive focus or null if there is
     *                 no such element.
     */
    public function getFocusableHtmlId()
    {
        return null;
    }



    /**
     * Replace this widget with a new container
     *
     * Replaces this widget in the widget tree with a new {@link SwatContainer},
     * then adds this widget to the new container.
     *
     * @param SwatContainer $container optional container to use
     *
     * @throws SwatException
     *
     * @return SwatContainer a reference to the new container.
     */
    public function replaceWithContainer(SwatContainer $container = null)
    {
        if ($this->parent === null) {
            throw new SwatException(
                'Widget does not have a parent, unable ' .
                    'to replace this widget with a container.',
            );
        }

        if ($container === null) {
            $container = new SwatContainer();
        }

        $parent = $this->parent;
        $parent->replace($this, $container);
        $container->add($this);

        return $container;
    }



    /**
     * Performs a deep copy of the UI tree starting with this UI object
     *
     * @param string $id_suffix optional. A suffix to append to copied UI
     *                           objects in the UI tree.
     *
     * @return SwatUIObject a deep copy of the UI tree starting with this UI
     *                       object.
     *
     * @see SwatUIObject::copy()
     */
    public function copy($id_suffix = '')
    {
        $copy = parent::copy($id_suffix);

        if ($id_suffix != '' && $copy->id !== null) {
            $copy->id = $copy->id . $id_suffix;
        }

        // We can't copy composite widgets here because the widget id of a
        // composite widget often uses a specific suffix. Copying and appending
        // a separate suffix here can break the composite widgets. Instead,
        // just mark the composite widgets as needing to be created for the
        // copy. Composite widgets will be instantiated on-demand with the
        // correct id values.
        $copy->composite_widgets = [];
        $copy->composite_widgets_created = false;

        return $copy;
    }



    /**
     * @todo document me
     */
    abstract public function printWidgetTree();



    /**
     * Gets the array of CSS  classes that are applied  to this widget
     *
     * @return array the array of CSS  classes that are applied to this widget.
     */
    protected function getCSSClassNames()
    {
        $classes = [];

        if (!$this->isSensitive()) {
            $classes[] = 'swat-insensitive';
        }

        $classes = array_merge($classes, parent::getCSSClassNames());

        return $classes;
    }



    /**
     * Creates and adds composite widgets of this widget
     *
     * Created composite widgets should be added in this method using
     * {@link SwatWidget::addCompositeWidget()}.
     */
    protected function createCompositeWidgets()
    {
    }



    /**
     * Adds a composite a widget to this widget
     *
     * @param SwatWidget $widget the composite widget to add.
     * @param string $key a key identifying the widget so it may be retrieved
     *                     later. The key does not have to be the widget's id
     *                     but the key does have to be unique within this
     *                     widget relative to the keys of other composite
     *                     widgets.
     *
     * @throws SwatDuplicateIdException if a composite widget with the
     *                                   specified key is already added to this
     *                                   widget.
     * @throws SwatException if the specified widget is already the child of
     *                        another object.
     */
    final protected function addCompositeWidget(SwatWidget $widget, $key)
    {
        if (array_key_exists($key, $this->composite_widgets)) {
            throw new SwatDuplicateIdException(
                sprintf(
                    "A composite widget with the key '%s' already exists in this " .
                        'widget.',
                    $key,
                ),
                0,
                $key,
            );
        }

        if ($widget->parent !== null) {
            throw new SwatException(
                'Cannot add a composite widget that already has a parent.',
            );
        }

        $this->composite_widgets[$key] = $widget;
        $widget->parent = $this;
    }



    /**
     * Gets a composite widget of this widget by the composite widget's key
     *
     * This is used by other methods to retrieve a specific composite widget.
     * This method ensures composite widgets are created before trying to
     * retrieve the specified widget.
     *
     * @param string $key the key of the composite widget to get.
     *
     * @return SwatWidget the specified composite widget.
     *
     * @throws SwatWidgetNotFoundException if no composite widget with the
     *                                     specified key exists in this widget.
     */
    final protected function getCompositeWidget($key)
    {
        $this->confirmCompositeWidgets();

        if (!array_key_exists($key, $this->composite_widgets)) {
            throw new SwatWidgetNotFoundException(
                sprintf(
                    "Composite widget with key of '%s' not found in %s. Make sure " .
                        'the composite widget was created and added to this widget.',
                    $key,
                    get_class($this),
                ),
                0,
                $key,
            );
        }

        return $this->composite_widgets[$key];
    }



    /**
     * Gets all composite widgets added to this widget
     *
     * This method ensures composite widgets are created before retrieving the
     * widgets.
     *
     * @param string $class_name optional class name. If set, only widgets
     *                            that are instances of <code>$class_name</code>
     *                            are returned.
     *
     * @return array all composite wigets added to this widget. The array is
     *                indexed by the composite widget keys.
     *
     * @see SwatWidget::addCompositeWidget()
     */
    final protected function getCompositeWidgets($class_name = null)
    {
        $this->confirmCompositeWidgets();

        if (
            !(
                $class_name === null ||
                class_exists($class_name) ||
                interface_exists($class_name)
            )
        ) {
            return [];
        }

        $out = [];

        foreach ($this->composite_widgets as $key => $widget) {
            if ($class_name === null || $widget instanceof $class_name) {
                $out[$key] = $widget;
            }
        }

        return $out;
    }



    /**
     * Confirms composite widgets have been created
     *
     * Widgets are only created once. This method may be called multiple times
     * in different places to ensure composite widgets are available. In general,
     * it is best to call this method before attempting to use composite
     * widgets.
     *
     * This method is called by the default implementations of init(),
     * process() and is called any time {@link SwatWidget::getCompositeWidget()}
     * is called so it rarely needs to be called manually.
     */
    final protected function confirmCompositeWidgets()
    {
        if (!$this->composite_widgets_created) {
            $this->createCompositeWidgets();
            $this->composite_widgets_created = true;
        }
    }

}
