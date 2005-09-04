<?php

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatMessage.php';

/**
 * A control to display page status messages  
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatMessageDisplay extends SwatControl
{
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
 
	/**
	 * Adds a message
	 *
	 * Adds a new message. The message will be shown by the display() method
	 *
	 * @param SwatMessage {@link SwatMessage} the message object to add.
	 *
	 * @see SwatMessage
	 */
	public function add($msg)
	{
		$this->_messages[] = $msg;
	}

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

		$div = new SwatHtmlTag('div');

		foreach ($this->_messages as $message) {
			switch ($message->type) {
				case SwatMessage::NOTIFICATION :
					$div->class = 'swat-message-display-notification';
					break;
				case SwatMessage::WARNING :
					$div->class = 'swat-message-display-warning';
					break;
				case SwatMessage::ERROR :
					$div->class = 'swat-message-display-error';
					break;
				case SwatMessage::SYSTEM_ERROR :
					$div->class = 'swat-message-display-system-error';
					break;
			}

			$div->open();

			$primary_content = new SwatHtmlTag('h3');
			$primary_content->class = 'swat-message-display-primary-content';
			$primary_content->content = $message->primary_content;
			$primary_content->display();

			if ($message->secondary_content !== null) {
				$secondary_div = new SwatHtmlTag('div');
				$secondary_div->class = 'swat-message-display-secondary-content';
				$secondary_div->content = $message->secondary_content;
				$secondary_div->display();
			}

			$div->close();
		}
	}
}

?>
