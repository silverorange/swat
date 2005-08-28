<?php

require_once 'Swat/SwatObject.php';
require_once 'Swat/SwatStyle.php';
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
	 * Messages affixed to this widget
	 *
	 * @var array
	 */
	protected $messages = array();

	/**
	 * The style of this widget
	 *
	 * @var SwatStyle
	 */
	private $style = null;

	/**
	 * The default style of all widgets
	 *
	 * @var SwatStyle
	 */
	static private $default_style = null;

	/**
	 * Creates a new SwatWidget
	 *
	 * @param string $id a non-visible unique id for this widget.
	 */
	public function __construct($id = null)
	{
		$this->id = $id;

		$this->style = $this->getDefaultStyle();

		$this->init();
	}

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

	/**
	 * Sets the style of this widget
	 *
	 * @param SwatStyle $style the styoe of this widget.
	 */
	public function setStyle(SwatStyle $style)
	{
		$this->style = $style;
	}

	/**
	 * Gets the default style of all widgets
	 *
	 * The default style is a static property of SwatWidget
	 */
	public function getDefaultStyle()
	{
		if (self::$default_style === null) {
			self::$default_style = new SwatStyle();
		}
		return self::$default_style;
	}
	
	/**
	 * Displays this widget
	 *
	 * Displays this widget displays as well as recursively displays any child
	 * widgets of this widget.
	 */
	abstract public function display();

	/**
	 * Displays this widget with tidy HTML
	 *
	 * The display() method is called and the output is cleaned up.
	 */
	public function displayTidy()
	{
		$breaking_tags = '</?(div|p|table|td|tr|ul|li|ol|dl)[^<>]*>';
		ob_start();
		$this->display();
		$buffer = ob_get_clean();
		$tidy = ereg_replace($breaking_tags, "\n\\0\n", $buffer);
		$tidy = ereg_replace("\n\n", "\n", $tidy);
		echo $tidy;
	}

	/**
	 * Processes this widget
	 *
	 * After a form submit, this widget processes itself as well as recursively
	 * processing any of its child widgets.
	 */
	public function process()
	{
	}

	/**
	 * Initializes this widget
	 *
	 * Every widget is initialized in the widget constructor.
	 */
	public function init()
	{
	}

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
	abstract public function getAllMessages();

	/**
	 * Gets messages for this widget
	 *
	 * Does not get messages from child widgets.
	 *
	 * @return array an array of {@link SwatMessage} objects.
	 */
	public function getMessages()
	{
		return $this->messages;
	}

	/**
	 * Checks for the presence of messages
	 *
	 * @return boolean true if there is an message in the subtree.
	 */
	abstract public function hasMessage();
}

?>
