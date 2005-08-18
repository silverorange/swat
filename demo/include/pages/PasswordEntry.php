<?php

require_once 'ExamplePage.php';

class PasswordEntry extends ExamplePage
{
	public function initUI()
	{
		$password = $this->ui->getWidget('password');
		$confirm_password = $this->ui->getWidget('confirm_password');

		$confirm_password->password_widget = $password;
	}
}

?>
