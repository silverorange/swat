<?php

require_once 'Swat/SwatPage.php';
require_once 'Swat/SwatUI.php';
require_once '../include/DemoMenu.php';

/**
 * A page in the Swat Demo Application
 *
 * Demo application pages can set widget properties that are not expressable
 * in SwatML.
 *
 * @package   SwatDemo
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class DemoPage extends SwatPage
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
		$this->menu = new DemoMenu();
		$this->menu->display();
		$this->layout->menu = ob_get_clean();

		$this->layout->base_href = 'index.php';
	}
}

?>
