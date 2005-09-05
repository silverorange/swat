<?php

require_once 'Swat/SwatObject.php';
require_once 'Swat/SwatHtmlHeadEntry.php';
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

	/**
	 * An array of HTML head entries needed by this widget
	 *
	 * Entries are stored in a data object called SwatHtmlHeadEntry. This
	 * property contains an array of such objects.
	 *
	 * @var array
	 */
	protected $html_head_entries = array();

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
		$this->addStylesheet('swat/swat.css');
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
	}

	// }}}
	// {{{ public function addStyleSheet()

	/**
	 * Adds a stylesheet to the list of stylesheets needed by this widget
	 *
	 * @param string $stylesheet the uri of the stylesheet.
	 */
	public function addStyleSheet($stylesheet)
	{
		$this->html_head_entries[$stylesheet] =
			new SwatHtmlHeadEntry($stylesheet, SwatHtmlHeadEntry::TYPE_STYLE);
	}

	// }}}
	// {{{ public function addJavaScript()

	/**
	 * Adds a JavaScript include to the list of JavaScript includes needed
	 * by this widget
	 *
	 * @param string $javascript the uri of the JavaScript include.
	 */
	public function addJavaScript($javascript)
	{
		$this->html_head_entries[$javascript] =
			new SwatHtmlHeadEntry($javascript,
			SwatHtmlHeadEntry::TYPE_JAVASCRIPT);
	}

	// }}}
	// {{{ public function displayHtmlHeadEntries()

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
	// {{{ abstract public function getHtmlHeadEntries()

	/**
	 * Gathers the SwatHtmlHeadEntry objects needed by this widget
	 *
	 * Head entries are things like stylesheets and javascript includes that
	 * should go in the head section of html.
	 *
	 * @return array the SwatHtmlHeadEntry objects needed by this widget.
	 */
	abstract public function getHtmlHeadEntries();

	// }}}
}

?>
