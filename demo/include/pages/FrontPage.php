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
		$this->layout->data->title = $this->app->title;
	}

	// {{{ protected function createLayout()

	protected function createLayout()
	{
		return new SiteLayout($this->app, '../include/layouts/xhtml/no_source.php');
	}

	// }}}
}

?>
