<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatUIObject.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';

/**
 * Notebook widget for containing Notebook pages
 *
 * @package   Swat
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatNoteBook extends SwatWidget implements SwatUIParent
{
	
	// {{{ public properties
	/**
	 * Visible
	 *
	 * Whether the widget is display. All widgets should respect this.
	 *
	 * @var boolean
	 */
	public $visible = true;

	/**
	 * A non-visible unique id for this widget, or null
	 *
	 * @var string
	 */
	public $id = null;

	// }}}
	// {{{ private properties
	
	/**
	 * Pages affixed to this widget
	 *
	 * @var array
 	 */
	protected $pages = array();

	/**
	 * Messages affixed to this widget
	 *
	 * @var array
	 */
	protected $messages = array();

	// }}}
	// {{{ public function __construct

	/**
	 * Creates a new SwatWidget
	 *
	 * @param string $id a non-visable unique id for this widget.
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->requires_id = true;

		$yui = new SwatYUI(array('tabview'));
		$this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());

		$this->addStyleSheet('packages/swat/styles/swat-note-book.css',
			Swat::PACKAGE_ID);
	}
	
	// }}}	
	// {{{ public function addChild
	
	/**
	 * Adds a SwatNoteBookPage to this widget
	 *
	 * @param object $object an instance of SwatNoteBookPage
	 */
	public function addChild(SwatObject $object)
	{
		if ($object instanceof SwatNoteBookPage)
			$this->addPage($object);
		 else
			throw new SwatInvalidClassException('You can only add SwatNoteBookPage.', 0, $object);
	}
	// }}}
	// {{{ public function addPage
	
	/**
	 * Adds a SwatNoteBookPage to this widget.
	 *
	 * @param object $page and instance of SwatNoteBookPage
	 */
	public function addPage(SwatNoteBookPage $page)
	{
		$this->pages[] = $page;
		$page->parent = $this;
	}

	// }}}
	// {{{ public function init
	
	/**
	 * Initializaes this widget
	 *
	 * Initialization is done post-constuction. It is called by SwatUI and
	 * may be called manually.
	 *
	 * Init allows properties to manually set on widgets between the
	 * constructor and other initialization routines.
	 */
	public function init()
	{	
		parent::init();
		foreach($this->pages as $page)
			$page->init();
		
	}
	
	// }}}
	// {{{ public function display
	
	/**
	 * Displays this widget
	 *
	 * Displays this widget displays as well as recursively displays any child
	 * widgets of this widget.
	 */
	public function display()
	{
		if (!$this->visible)
			return;
		
		$li_counter = 0;
		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id;
		$div_tag->class = 'yui-navset';
		$div_tag->open();

		echo'<ul class="yui-nav">';
		foreach ($this->pages as $page)
		{
			$li_counter += 1;
			if ($li_counter === 1){
				$li_tag = new SwatHtmlTag('li');
				$li_tag->class = 'selected';
			} else {
				$li_tag = new SwatHtmlTag('li');
			}

			$anchor_tag = new SwatHtmlTag('a');
			$anchor_tag->href = '#'.$page->id;

			$em_tag = new SwatHtmlTag('em');
			$em_tag->setContent($page->title);
			
			$li_tag->open();
			$anchor_tag->open();
			$em_tag->display();
			$anchor_tag->close();
			$li_tag->close();
		}
		echo'</ul>';

		echo '<div class="yui-content">';
		foreach ($this->pages as $page)
			$page->display();

		echo '</div>';
		$div_tag->close();
		$this->displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ public function printWidgetTree
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
	// {{{ public function addMessage
	
	/**
	 * Adds a message
	 *
	 * Adds a new mesage to this widget. The message will be shown by the
	 * display() method as well as cause hasMessage() to return true.
	 *
	 * @param SwatMessage {@link Swat Message} the message object to add.
	 *
	 * @see SwatMessage
	 */
	public function addMessage(SwatMessage $message)
	{
		$this->messages[] = $message;
	}

	// }}}
	// {{{ public function getMessages
	
	/**
	 * Gets all messaages
	 *
	 * Gathers all messages from children of this widget and this widget
	 * itself.
	 *
	 * @return array and array of {@link SwatMessage} objects.
	 *
	 * @see SwatMessage
	 */
	public function getMessages()
	{
		$messages = $this->messages;

		foreach ($this->pages as $page)
			$messages = array_merge($messages, $page->getMessages());

		return $messages;
	}

	// }}}
	// {{{ public function hasMessage
	
	/**
	 * Checks for the presence of messages
	 *
	 * @return boolean true if there is a message in the subtree.
	 */
	public function hasMessage()
	{
		$has_message = false;
		
		foreach ($this->pages as $page) {
			if ($page->hasMessage()) {
				$has_message = true;
				break;
			}
		}
		
		return $has_message;
	}

	// }}}
	// {{{ public function getInlineJavaScript

	/**
	 * Gets inline JavaScript used by this user-interface object
	 *
	 * @return string inline JavaScript used by this use-interface object.
	 */
	protected function getInlineJavaScript()
	{
		return sprintf("var %s_obj = new YAHOO.widget.TabView('%s');",
			$this->id, $this->id);
	}

	// }}}
}
?>
