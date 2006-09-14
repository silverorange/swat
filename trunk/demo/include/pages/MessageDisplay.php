<?php

require_once 'DemoPage.php';

/**
 * A demo using a message display
 *
 * This page sets up a a number of messages and addes them
 * to the message display.
 *
 * @package   SwatDemo
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class MessageDisplay extends DemoPage
{
	// {{{ public function initUI()

	public function initUI()
	{
		$short_message_display = $this->ui->getWidget('short_message_display');

		$short_message_display->add(
			new SwatMessage('This is a notification message.',
			SwatMessage::NOTIFICATION));
		
		$short_message_display->add(new SwatMessage(
			'This is a warning message.', SwatMessage::WARNING));
		
		$short_message_display->add(new SwatMessage(
			'This is an error message.', SwatMessage::ERROR));
		
		$short_message_display->add(
			new SwatMessage('This is a system error message.', 
			SwatMessage::SYSTEM_ERROR));

		$long_message_display = $this->ui->getWidget('long_message_display');

		$msg = new SwatMessage('This is a notification message.', 
			SwatMessage::NOTIFICATION);

		$msg->secondary_content = 'This message has secondary content.';
		$long_message_display->add($msg);

		$msg = new SwatMessage('This is a warning message.', 
			SwatMessage::WARNING);

		$msg->secondary_content = 'This message has secondary content.';
		$long_message_display->add($msg);
		
		$msg = new SwatMessage('This is an error message.', 
			SwatMessage::ERROR);

		$msg->secondary_content = 'This message has secondary content.';
		$long_message_display->add($msg);
		
		$msg = new SwatMessage('This is a system error message.', 
			SwatMessage::SYSTEM_ERROR);

		$msg->secondary_content = 'This message has secondary content.';
		$long_message_display->add($msg);
	}

	// }}}
}

?>
