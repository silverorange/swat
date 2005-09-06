<?php

require_once 'Swat/SwatPage.php';
require_once 'Swat/SwatNavBar.php';
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
	protected $navbar = null;

	protected $start_time = 0;
	
	protected $demo;
	
	public function __construct($app)
	{
		parent::__construct($app);

		$this->navbar = new SwatNavBar();
	}

	public function init()
	{
		$this->start_time = microtime(true);

		$this->demo = SwatApplication::initVar('demo', 'Main',
			SwatApplication::VAR_GET);

		// simple security
		$this->demo = basename($this->demo);
		
		$this->ui = new SwatUI();
		$this->ui->loadFromXML('../include/pages/'.strtolower($this->demo).'.xml');

		$this->initUI();

		$this->ui->init();

		$this->navbar->createEntry($this->app->title, 'index.php');
		$this->navbar->createEntry($this->demo);
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
		$this->layout->title = $this->demo.' | '.$this->app->title;

		ob_start();
		$this->ui->getRoot()->displayHtmlHeadEntries();
		$this->layout->html_head_entries = ob_get_clean();
		
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

		$this->layout->execution_time = round(microtime(true) - $this->start_time, 4);

		ob_start();
		$this->navbar->display();
		$this->layout->navbar = ob_get_clean();
	}
}

?>
