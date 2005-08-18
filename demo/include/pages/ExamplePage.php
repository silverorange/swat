<?php

require_once 'Swat/SwatPage.php';
require_once 'Swat/SwatUI.php';
require_once '../include/ExampleMenu.php';

class ExamplePage extends SwatPage
{
	protected $ui = null;

	private $demo;
	
	public function init()
	{
		$this->demo = SwatApplication::initVar('demo', 'Main',
			SwatApplication::VAR_GET);

		// simple security
		$this->demo = basename($this->demo);
		
		$this->ui = new SwatUI();
		$this->ui->loadFromXML('../include/pages/'.strtolower($this->demo).'.xml');

		$this->initUI();

		$this->ui->init();
	}

	/**
	 * Allows subclasses to modify the SwatUI object before it is displayed
	 */
	public function initUI()
	{
	}

	public function process()
	{
		$this->ui->process();
	}

	public function build()
	{
		$this->layout->app_title = $this->app->title;

		$this->layout->title = $this->demo;
		
		$this->layout->source_code =
			str_replace("\t", '    ', htmlspecialchars(implode('',
				file('../include/pages/'.strtolower($this->demo).'.xml'))));

		ob_start();
		$this->ui->display();
		$this->layout->ui = ob_get_clean();

		ob_start();
		$this->menu = new ExampleMenu();
		$this->menu->display();
		$this->layout->menu = ob_get_clean();
	}
}

?>
