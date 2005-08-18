<?php

require_once 'ExamplePage.php';

class MessageBox extends ExamplePage
{
	public function initUI()
	{
		$message_box = $this->ui->getWidget('message_box');

		$message_box->messages = array(
			new SwatMessage('This is an informational message.',
				SwatMessage::INFO),
			new SwatMessage('This is a warning message.',
				SwatMessage::WARNING),
			new SwatMessage('This is a user error message.',
				SwatMessage::USER_ERROR),
			new SwatMessage('This is an error message.', SwatMessage::ERROR)
		);
	}
}

?>
