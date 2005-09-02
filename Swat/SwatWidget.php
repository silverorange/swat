<?php

require_once 'Swat/SwatObject.php';
require_once 'Swat/SwatMessage.php';

/**
 * Base class for all widgets
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatWidget extends SwatObject
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

	// }}}
	// {{{ protected properties

	/**
	 * Messages affixed to this widget
	 *
	 * @var array
	 */
	protected $messages = array();

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
		$this->init();
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
	 * Every widget is initialized in the widget constructor.
	 */
	public function init()
	{
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
}

?>
