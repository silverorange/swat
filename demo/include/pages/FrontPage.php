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
	public function init()
	{
		$this->start_time = microtime(true);

		$this->demo = 'FrontPage';
		
		$this->ui = new SwatUI();
		$this->ui->loadFromXML('../include/pages/'.strtolower($this->demo).'.xml');

		$this->initUI();

		$this->ui->init();

		$this->navbar->createEntry($this->app->title);
	}

	public function initUI()
	{
		$content = $this->ui->getWidget('content');
		$content->content = "Welcome to the Swat Widget Gallery. ".
			"Here you will find a number of examples of the different widgets ".
			"Swat provides.";
	}

	public function process()
	{
	}

	public function build()
	{
		parent::build();
		
		$this->layout->title = $this->app->title;
	}

	protected function createLayout()
	{
		return new SwatLayout('../layouts/no_source.php');
	}
}

?>
