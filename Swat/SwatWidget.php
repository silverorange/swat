<?php

require_once 'Swat/SwatObject.php';
require_once 'Swat/SwatMessage.php';

/**
 * Base class for all widgets
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
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

	protected $messages = array();

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

	/**
	 * Generates automatic unique id
	 *
	 * Sets the id property of this widget to an auto-generated identifier
	 * if the id has not already been explicitly set.
	 */
	protected function generateAutoId()
	{
		static $counter = 0;

		if ($this->id !== null) return;
		$this->id = get_class($this).$counter;

		$counter++;
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
	abstract public function addMessage($msg);

	/**
	 * Gathers error messages
	 *
	 * Gathers all messages from children of this widget and this widget 
	 * itself.
	 *
	 * @return array an array of {@link SwatMessage} objects.
	 *
	 * @see SwatMessage
	 */
	abstract public function gatherMessages();

	/**
	 * Checks for the presence of messages
	 *
	 * @return boolean true if there is an message in the subtree.
	 */
	abstract public function hasMessage();
}

?>
