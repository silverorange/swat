<?php

require_once 'pages/FrontPage.php';

/**
 * An demo application
 *
 * This is an application to demonstrate various Swat widgets.
 *
 * @package   SwatDemo
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class DemoApplication extends SiteWebApplication
{
	// {{{ protected function loadPage()

	/**
	 * Loads the page
	 */
	protected function loadPage()
	{
		if ($this->page === null) {
			$source = self::initVar('demo');
			$source = basename($source); // simple security
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
		} else {
			return new FrontPage($this);
		}
	}

	// }}}
}

?>
