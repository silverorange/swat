<?php

require_once 'ExamplePage.php';

class MessageBox extends ExamplePage
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
