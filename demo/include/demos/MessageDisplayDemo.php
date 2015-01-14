<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Demo.php';

/**
 * A demo using a message display
 *
 * @package   SwatDemo
 * @copyright 2005-2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class MessageDisplayDemo extends Demo
{
	// {{{ public function buildDemoUI()

	public function buildDemoUI(SwatUI $ui)
	{
		$short_message_display = $ui->getWidget('short_message_display');

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

		$long_message_display = $ui->getWidget('long_message_display');

		$message = new SwatMessage('This is a notification message.',
			SwatMessage::NOTIFICATION);

		$message->secondary_content = 'This message has secondary content.';
		$long_message_display->add($message);

		$message = new SwatMessage('This is a warning message.',
			SwatMessage::WARNING);

		$message->secondary_content = 'This message has secondary content.';
		$long_message_display->add($message);

		$message = new SwatMessage('This is an error message.',
			SwatMessage::ERROR);

		$message->secondary_content = 'This message has secondary content.';
		$long_message_display->add($message);

		$message = new SwatMessage('This is a system error message.',
			SwatMessage::SYSTEM_ERROR);

		$message->secondary_content = 'This message has secondary content.';
		$long_message_display->add($message);
	}

	// }}}
}

?>
