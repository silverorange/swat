<?php

require_once 'ExamplePage.php';

class FrontPage extends ExamplePage
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
		$this->menu = new ExampleMenu();
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
