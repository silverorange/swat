<?php

require_once 'Swat/SwatObject.php';
require_once 'Swat/SwatLayout.php';

/**
 * Base class for a page
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatPage extends SwatObject
{
	// {{{ public properties

	/**
	 * Layout object to use to display this page
	 *
	 * @var SwatLayout
	 */
	public $layout = null;

	/**
	 * Application object
	 * 
	 * A reference to the {@link SwatApplication} object that created
	 * this page.
	 *
	 * @var SwatApplication
	 */
	public $app = null;

	// }}}
	// {{{ public function __construct()

	public function __construct(SwatApplication $app)
	{
		$this->app = $app;
		$this->layout = $this->createLayout();
	}

	// }}}
	// {{{ public function init()

	public function init()
	{

	}

	// }}}
	// {{{ protected function createLayout()

	protected function createLayout()
	{
		return new SwatLayout('../layouts/default.php');
	}

	// }}}
}
?>
