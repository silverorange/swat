<?php

require_once 'Swat/SwatUIBase.php';
require_once 'Swat/SwatMessage.php';

/**
 * Base class for all widgets
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatWidget extends SwatUIBase
{
	// {{{ public properties

	/**
	 * The widget which contains this widget
	 *
	 * @var SwatContainer
	 */
	public $parent = null;

	/**
	 * A non-visible unique id for this widget, or null
	 *
	 * @var string
	 */
	public $id = null;

	/**
	 * Visible
	 *
	 * Whether the widget is displayed. All widgets should respect this.
	 *
	 * @var boolean
	 */
	public $visible = true;

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
	 * {@link SwatUIBase::addStyleSheet() method will be called to add this 
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

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new SwatWidget
	 *
	 * @param string $id a non-visible unique id for this widget.
	 */
	public function __construct($id = null)
	{
		$this->id = $id;
		$this->addStylesheet('swat/styles/swat.css');
	}

	// }}}
	// {{{ public function getUniqueId()

	/**
	 * Generates a unique id
	 *
	 * Gets the an id that may be used for the id property of this widget.
	 * Ids are auto-generated.
	 *
	 * @return string a unique identifier.
	 */
	protected function getUniqueId()
	{
		static $counter = 0;

		$counter++;

		return get_class($this).$counter;
	}

	// }}}
	// {{{ abstract public function display()

	/**
	 * Displays this widget
	 *
	 * Displays this widget displays as well as recursively displays any child
	 * widgets of this widget.
	 */
	abstract public function display();

	// }}}
	// {{{ public function process()

	/**
	 * Processes this widget
	 *
	 * After a form submit, this widget processes itself as well as recursively
	 * processing any of its child widgets.
	 */
	public function process()
	{
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
		$html_head_entries = $this->getHtmlHeadEntries();

		foreach ($html_head_entries as $head_entry) {
			$head_entry->display();
			echo "\n";
		}
	}

	// }}}
	// {{{ abstract public function addMessage()

	/**
	 * Adds a message
	 *
	 * Adds a new message to this widget. The message will be shown by the
	 * display() method as well as cause hasMessage() to return true.
	 *
	 * @param SwatMessage {@link SwatMessage} the message object to add.
	 *
	 * @see SwatMessage
	 */
	abstract public function addMessage($message);

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
	 * @return boolean true if there is an message in the subtree.
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
		if ($this->parent === null)
			return $this->sensitive;
		else
			return ($this->parent->isSensitive() && $this->sensitive);
	}

	// }}}
	// {{{ public function isVisible()

	/**
	 * Determines the visiblity of this widget.
	 *
	 * Looks at the visible property of the ancestors of this widget to 
	 * determine if this widget is visible.
	 *
	 * @return boolean whether this widget is visible.
	 *
	 * @see SwatWidget::$visible
	 */
	public function isVisible()
	{
		if ($this->parent === null)
			return $this->visible;
		else
			return ($this->parent->isVisible() && $this->visible);
	}

	// }}}
	// {{{ public function getFirstAncestor()

	/**
	 * Gets the first ancestor widget of a specific class
	 *
	 * Retrieves the first ancestor widget in the parent path that is a 
	 * descendant of the specified class name.
	 *
	 * @param string $class_name class name to look for.
	 *
	 * @return mixed the first ancestor widget or null if no matching ancestor
	 *                is found.
	 *
	 * @see SwatContainer::getFirstDescendant()
	 */
	public function getFirstAncestor($class_name)
	{
		if (!class_exists($class_name))
			return null;

		if ($this->parent === null) {
			$out = null;
		} elseif ($this->parent instanceof $class_name) {
			$out = $this->parent;
		} else {
			$out = $this->parent->getFirstAncestor($class_name);
		}

		return $out;
	}

	// }}}
}

?>
