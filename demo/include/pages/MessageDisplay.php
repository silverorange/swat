<?php

require_once 'DemoPage.php';

/**
 * A demo using a message display
 *
 * This page sets up a a number of messages and addes them to the message display.
 *
 * @package   SwatDemo
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class MessageDisplay extends DemoPage
{
	public function initUI()
	{
		$message_display = $this->ui->getWidget('message_display');

		$message_display->messages = array(
			new SwatMessage('This is a notification message.',
				SwatMessage::NOTIFICATION),
			new SwatMessage('This is a warning message.',
				SwatMessage::WARNING),
			new SwatMessage('This is an error message.',
				SwatMessage::ERROR),
			new SwatMessage('This is a system error message.', SwatMessage::SYSTEM_ERROR)
		);

		$msg = new SwatMessage('This is a notification message.', 
			SwatMessage::NOTIFICATION);

		$msg->secondary_content = 'This message has secondary content.';
		$message_display->messages[] = $msg;

		$msg = new SwatMessage('This is a warning message.', 
			SwatMessage::WARNING);

		$msg->secondary_content = 'This message has secondary content.';
		$message_display->messages[] = $msg;

		$msg = new SwatMessage('This is an error message.', 
			SwatMessage::ERROR);

		$msg->secondary_content = 'This message has secondary content.';
		$message_display->messages[] = $msg;

		$msg = new SwatMessage('This is a system error message.', 
			SwatMessage::SYSTEM_ERROR);

		$msg->secondary_content = 'This message has secondary content.';
		$message_display->messages[] = $msg;
	}
}

?>
