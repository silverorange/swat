<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatNavBar.php';
require_once 'Swat/SwatUI.php';
require_once 'Site/pages/SitePage.php';
require_once 'Site/layouts/SiteLayout.php';
require_once '../include/DemoApplication.php';
require_once '../include/DemoMenu.php';
require_once '../include/DemoDocumentationMenu.php';

/**
 * A page in the Swat Demo Application
 *
 * Demo application pages can set widget properties that are not expressable
 * in SwatML.
 *
 * @package   SwatDemo
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class DemoPage
{
	// {{{ protected properties

	protected $ui = null;
	protected $navbar = null;
	protected $documentation_menu = null;

	protected $start_time = 0;
	
	protected $demo;

	// }}}
	// {{{ public function __construct()
	
	public function __construct(DemoApplication $app, $demo)
	{
		$this->navbar = new SwatNavBar();
		$this->ui = new SwatUI();
		$this->ui->mapClassPrefixToPath('Demo', '../include/');
		$this->demo = $demo;
	}

	// }}}
	// {{{ public function init()

	public function init()
	{
		$this->start_time = microtime(true);

		if ($this->demo === null)
			$this->demo = get_class($this);
		
		$this->ui->loadFromXML('../include/pages/'.strtolower($this->demo).
			'.xml');

		$this->initUI();

		$this->ui->init();

		$this->navbar->createEntry($this->app->title, 'index.php');
		$this->navbar->createEntry($this->demo);

		$this->documentation_menu = $this->getDocumentationMenu();
	}

	// }}}
	// {{{ public function initUI()

	/**
	 * Allows subclasses to modify the SwatUI object before it is displayed
	 */
	public function initUI()
	{
	}

	// }}}
	// {{{ pulbic function process

	public function process()
	{
		$this->ui->process();
	}

	// }}}
	// {{{ public function build()

	public function build()
	{
		parent::build();

		$this->layout->data->title = $this->demo.' | '.$this->app->title;

		$this->layout->addHtmlHeadEntrySet(
			$this->ui->getRoot()->getHtmlHeadEntrySet());

		$filename = '../include/pages/'.strtolower($this->demo).'.xml';
		$code = file_get_contents($filename);
		$code = htmlspecialchars($code, ENT_COMPAT, 'UTF-8');
		$code = str_replace("\t", '    ', $code);
		$this->layout->data->source_code = $code;

		$this->layout->startCapture('ui');
		$this->ui->display();
		$this->layout->endCapture();

		$this->layout->startCapture('menu');
		$this->menu = new DemoMenu();
		$this->menu->display();
		$this->layout->endCapture();

		$this->layout->startCapture('documentation_menu');
		$this->documentation_menu->display();
		$this->layout->endCapture();

		$this->layout->data->execution_time =
			round(microtime(true) - $this->start_time, 4);

		$this->layout->startCapture('navbar');
		$this->navbar->display();
		$this->layout->endCapture();
	}

	// }}}
	// {{{ protected function createLayout()

	protected function createLayout()
	{
		return new SiteLayout($this->app, '../include/layouts/xhtml/default.php');
	}

	// }}}
}

?>
