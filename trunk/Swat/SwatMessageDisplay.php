<?php

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatMessage.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';

/**
 * A control to display page status messages  
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatMessageDisplay extends SwatControl
{
	// {{{ private properties

	/**
	 * The messages to display
	 *
	 * The messages are stored as an array of references to SwatMessage
	 * objects.
	 *
	 * @var array
	 *
	 * @see SwatMessage
	 */
	private $_messages = array();

	// }}}
	// {{{ public function __construct()
 
	/**
	 * Creates a new message display
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->requires_id = true;

		$this->addJavaScript('packages/swat/javascript/swat-message-display.js',
			Swat::PACKAGE_ID);

		$this->addStyleSheet('packages/swat/styles/swat-message.css',
			Swat::PACKAGE_ID);

		$this->addStyleSheet('packages/swat/styles/swat-message-display.css',
			Swat::PACKAGE_ID);
	}

	// }}}
	// {{{ public function add()

	/**
	 * Adds a message
	 *
	 * Adds a new message. The message will be shown by the display() method
	 *
	 * @param mixed $msg either a {@link SwatMessage} object or a string to
	 *                    add to this display.
	 *
	 * @throws SwatInvalidClassException
	 */
	public function add($msg)
	{
		if (is_string($msg)) {
			$this->_messages[] = new SwatMessage($msg);
		} elseif ($msg instanceof SwatMessage) {
			$this->_messages[] = $msg;
		} else {
			throw new SwatInvalidClassException(
				'Cannot add message. Message must be either a string or a '.
				'SwatMessage.', 0, $msg);
		}
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays the messages
	 *
	 * The CSS class of each message is determined by the type of message being
	 * displayed.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		if (count($this->_messages) == 0)
			return;

		$ul_tag = new SwatHtmlTag('ul');
		$li_tag = new SwatHtmlTag('li');

		$ul_tag->id = $this->id;
		$ul_tag->class = $this->getCSSClassString();
		$ul_tag->open();

		$has_dismiss_link = false;

		foreach ($this->_messages as $key => $message) {
			$li_tag->id = $this->id.'_'.$key;
			$li_tag->class = $message->getCssClass();

			if ($message->secondary_content !== null) 
				$li_tag->class .= ' swat-message-with-secondary';

			$li_tag->open();

			if ($message->type == SwatMessage::NOTIFICATION |
				$message->type == SwatMessage::WARNING) {
				$dismiss_link = new SwatHtmlTag('a');
				$dismiss_link->href =
					"javascript:{$this->id}_obj.hideMessage({$key})";

				$dismiss_link->class = 'swat-message-display-dismiss-link';
				$dismiss_link->title = _('Dismiss this Message');
				$dismiss_link->setContent(_('Dismiss this Message'));
				$dismiss_link->display();
				$has_dismiss_link = true;
			}

			$primary_content = new SwatHtmlTag('h3');
			$primary_content->class = 'swat-message-primary-content';
			$primary_content->setContent(
				$message->primary_content, $message->content_type);

			$primary_content->display();

			if ($message->secondary_content !== null) {
				$secondary_div = new SwatHtmlTag('div');
				$secondary_div->class = 'swat-message-secondary-content';
				$secondary_div->setContent(
					$message->secondary_content, $message->content_type);

				$secondary_div->display();
			}

			$li_tag->close();
		}

		$ul_tag->close();

		if ($has_dismiss_link)
			$this->displayJavaScript();
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this message display
	 *
	 * @return array the array of CSS classes that are applied to this message
	 *                display.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-message-display');
		$classes = array_merge($classes, $this->classes);
		return $classes;
	}

	// }}}
	// {{{ private function displayJavaScript()

	/**
	 * Displays the JavaScript for hiding messages
	 */
	private function displayJavaScript()
	{
		echo '<script type="text/javascript">'."\n";
		printf("var %s_obj = new SwatMessageDisplay('%s', %s);\n",
			$this->id, $this->id, count($this->_messages));

		echo '</script>';
	}

	// }}}
}

?>
