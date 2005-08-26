<?php

require_once 'DemoPage.php';

/**
 * The front page of the demo application
 *
 * This page displays a quick introduction to the Swat Demo Application
 *
 * @package   SwatDemo
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class FrontPage extends DemoPage
{
	protected $ui = null;

	private $demo;
	
	public function init()
	{
	}

	public function process()
	{
	}

	public function build()
	{
		$this->layout->app_title = $this->app->title;

		$this->layout->title = 'Swat Widget Gallery';

		ob_start();
		$this->menu = new DemoMenu();
		$this->menu->display();
		$this->layout->menu = ob_get_clean();

		$this->layout->content = "Welcome to the Swat Widget Gallery. ".
			"Here you will find a number of examples of the different widgets ".
			"Swat provides.";
	}

	protected function createLayout()
	{
		return new SwatLayout('../layouts/front.php');
	}
}

?>
