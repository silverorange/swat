<?php

require_once 'DemoPage.php';

/**
 * A demo using password entry widgets
 *
 * This page associates the confirm password box with the password entry box.
 *
 * @package   SwatDemo
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class PasswordEntry extends DemoPage
{
	// {{{ public function initUI()

	public function initUI()
	{
		$password = $this->ui->getWidget('password');
		$confirm_password = $this->ui->getWidget('confirm_password');

		$confirm_password->password_widget = $password;
	}

	// }}}
}

?>
