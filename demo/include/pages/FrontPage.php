<?php

require_once 'DemoPage.php';

/**
 * The front page of the demo application
 *
 * This page displays a quick introduction to the Swat Demo Application
 *
 * @package   SwatDemo
 * @copyright 2005-2006 silverorange
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

		$this->documentation_menu = $this->getDocumentationMenu();
	}

	public function initUI()
	{
		$content = $this->ui->getWidget('content');
		$content->content =
			"This Swat demo site includes examples of Swat widgets and classes. ".
			"Each demo includes the SwatML source and links to the related ".
			"documentation for the classes used.";
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
		return new SiteLayout('../layouts/no_source.php');
	}
}

?>
