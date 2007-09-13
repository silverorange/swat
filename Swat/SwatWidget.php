<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatUIObject.php';
require_once 'Swat/SwatMessage.php';

/**
 * Base class for all widgets
 *
 * @package   Swat
 * @copyright 2004-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatWidget extends SwatUIObject
{
	// {{{ public properties

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
	 * user input. Unsensitive widgets should display "grayed-out" to inform
	 * the user they are not sensitive. All widgets that the user can interact
	 * with should respect this.
	 *
	 * @var boolean
	 */
	public $sensitive = true;

	/**
	 * Stylesheet
	 *
	 * The URI of a stylesheet for use with this widget. If this property is 
	 * set before {@link SwatWidget::init()} then the
	 * {@link SwatUIObject::addStyleSheet() method will be called to add this 
	 * stylesheet to the header entries. Primarily this should be used by
	 * SwatUI to set a stylesheet in SwatML. To set a stylesheet in PHP code,
	 * it is recommended to call addStyleSheet() directly.
	 *
	 * @var string
	 */
	public $stylesheet = null;

	// }}}
	// {{{ protected properties

	/**
	 * Messages affixed to this widget
	 *
	 * @var array
	 */
	protected $messages = array();

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

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new widget
	 *
	 * @param string $id a non-visible unique id for this widget.
	 */
	public function __construct($id = null)
	{
		parent::__construct();

		$this->id = $id;
		$this->addStylesheet('packages/swat/styles/swat.css',
			Swat::PACKAGE_ID);
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this widget
	 *
	 * Displays this widget displays as well as recursively displays any child
	 * widgets of this widget.
	 */
	public function display()
	{
		$this->displayed = true;
	}

	// }}}
	// {{{ abstract public function printWidgetTree()

	/**
	 * @todo document me
	 */
	abstract public function printWidgetTree();

	// }}}
	// {{{ public function process()

	/**
	 * Processes this widget
	 *
	 * After a form submit, this widget processes itself and its dependencies
	 * and then recursively processes  any of its child widgets.
	 */
	public function process()
	{
		$this->processed = true;
	}

	// }}}
	// {{{ public function init()

	/**
	 * Initializes this widget
	 *
	 * Initialization is done post-construction. It is called by SwatUI and
	 * may be called manually.
	 *
	 * Init allows properties to be manually set on widgets between the
	 * constructor and other initialization routines.
	 */
	public function init()
	{
		if ($this->requires_id && $this->id === null)
			$this->id = $this->getUniqueId();

		if ($this->stylesheet !== null)
			$this->addStyleSheet($this->stylesheet);
	}

	// }}}
	// {{{ public function displayHtmlHeadEntries()

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

	// }}}
	// {{{ public function getHtmlHeadEntrySet()

	/**
	 * Gets the SwatHtmlHeadEntry objects needed by this widget 
	 *
	 * If this widget has not been displayed, an empty set is returned to
	 * reduce the number of required HTTP requests.
	 *
	 * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects needed by
	 *                               this widget.
	 */
	public function getHtmlHeadEntrySet()
	{
		if ($this->isDisplayed())
			$set = new SwatHtmlHeadEntrySet($this->html_head_entry_set);
		else
			$set = new SwatHtmlHeadEntrySet();

		return $set;
	}

	// }}}
	// {{{ abstract public function addMessage()

	/**
	 * Adds a message
	 *
	 * Adds a new message to this widget. The message may be shown by the
	 * display() method and will as cause {@link SwatWidget::hasMessage()} to
	 * return as true.
	 *
	 * @param SwatMessage the message object to add.
	 *
	 * @see SwatMessage
	 */
	abstract public function addMessage(SwatMessage $message);

	// }}}
	// {{{ abstract public function getMessages()

	/**
	 * Gets all messages
	 *
	 * Gathers all messages from children of this widget and this widget 
	 * itself.
	 *
	 * @return array an array of {@link SwatMessage} objects.
	 *
	 * @see SwatMessage
	 */
	abstract public function getMessages();

	// }}}
	// {{{ abstract public function hasMessage()

	/**
	 * Checks for the presence of messages
	 *
	 * @return boolean true if there is an message in the widget subtree
	 *                  starting at this widget and false if there is not.
	 */
	abstract public function hasMessage();

	// }}}
	// {{{ public function isSensitive()

	/**
	 * Determines the sensitivity of this widget.
	 *
	 * Looks at the sensitive property of the ancestors of this widget to 
	 * determine if this widget is sensitive.
	 *
	 * @return boolean whether this widget is sensitive.
	 *
	 * @see SwatWidget::$sensitve
	 */
	public function isSensitive()
	{
		if ($this->parent !== null && $this->parent instanceof SwatWidget)
			return ($this->parent->isSensitive() && $this->sensitive);
		else
			return $this->sensitive;
	}

	// }}}
	// {{{ public function isProcessed()

	/**
	 * Whether or not this widget is processed
	 *
	 * @return boolean whether or not this widget is processed.
	 */
	public function isProcessed()
	{
		return $this->processed;
	}

	// }}}
	// {{{ public function isDisplayed()

	/**
	 * Whether or not this widget is displayed
	 *
	 * @return boolean whether or not this widget is displayed.
	 */
	public function isDisplayed()
	{
		return $this->displayed;
	}

	// }}}
	// {{{ public function getFocusableHtmlId()

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

	// }}}
	// {{{ public function replaceWithContainer()

	/**
	 * Replace this widget with a new container
	 * 
	 * Replaces this widget in the widget tree with a new SwatContainer, then
	 * add this widget to the new container.
	 *
	 * @throws SwatException
	 *
	 * @return SwatContainer a reference to the new container.
	 */
	public function replaceWithContainer()
	{
		if ($this->parent === null)
			throw new SwatException('Widget does not have a parent, unable '.
				'to replace this widget with a container.');

		$container = new SwatContainer();
		$parent = $this->parent;
		$parent->replace($this, $container);
		$container->add($this);

		return $container;
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS  classes that are applied  to this widget
	 *
	 * @return array the array of CSS  classes that are applied to this widget.
	 */
	protected function getCSSClassNames()
	{
		$classes = array();

		if (!$this->isSensitive())
			$classes[] = 'swat-insensitive';

		$classes = array_merge($classes, parent::getCSSClassNames());

		return $classes;
	}

	// }}}
}

?>
