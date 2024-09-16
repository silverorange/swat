<?php

require_once 'Demo.php';

/**
 * A demo using password entry widgets
 *
 * @package   SwatDemo
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class PasswordEntryDemo extends Demo
{


	public function buildDemoUI(SwatUI $ui)
	{
		$password = $ui->getWidget('password');
		$confirm_password = $ui->getWidget('confirm_password');
		$confirm_password->password_widget = $password;
	}

}

?>
