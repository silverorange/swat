<?php

require_once 'DemoPage.php';

/**
 * A demo using amessagebox
 *
 * This page sets up a a number of messages and addes them to the message box.
 *
 * @package   SwatDemo
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class MessageBox extends DemoPage
{
	public function initUI()
	{
		$message_box = $this->ui->getWidget('message_box');

		$message_box->messages = array(
			new SwatMessage('This is a notification message.',
				SwatMessage::NOTIFICATION),
			new SwatMessage('This is a warning message.',
				SwatMessage::WARNING),
			new SwatMessage('This is an error message.',
				SwatMessage::ERROR),
			new SwatMessage('This is a system error message.', SwatMessage::SYSTEM_ERROR)
		);
	}
}

?>
