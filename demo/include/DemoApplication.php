<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'pages/FrontPage.php';

/**
 * A demo application
 *
 * This is an application to demonstrate various Swat widgets.
 *
 * @package   SwatDemo
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class DemoApplication extends SiteWebApplication
{
	// {{{ public function __construct()

	public function __construct($id)
	{
		parent::__construct($id);
		$this->exception_page_source = null;
	}

	// }}}
	// {{{ public function run()

	public function run()
	{
		$this->initModules();
		$this->parseURI();

		$this->loadPage();
		$this->page->layout->init();
		$this->page->init();
		$this->page->layout->process();
		$this->page->process();
		$this->page->layout->build();
		$this->page->build();
		$this->page->layout->finalize();

		$this->page->layout->display();
	}

	// }}}
	// {{{ protected function loadPage()

	/**
	 * Loads the page
	 */
	protected function loadPage()
	{
		if ($this->page === null) {
			$source = self::initVar('demo');
			// simple security
			$source = ($source === null) ? $source : basename($source);
			$this->page = $this->resolvePage($source);
		}
	}

	// }}}
	// {{{ protected function resolvePage()

	/**
	 * Resolves a page for a particular source
	 *
	 * @return SitePage An instance of a SitePage is returned.
	 */
	protected function resolvePage($source)
	{
		if (file_exists('../include/pages/'.$source.'.php')) {
			require_once '../include/pages/'.$source.'.php';
			return new $source($this);
		} elseif ($source === null) {
			return new FrontPage($this);
		} else {
			return new DemoPage($this, null, $source);
		}
	}

	// }}}
}

?>
