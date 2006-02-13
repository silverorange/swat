<?php

require_once 'Swat/SwatObject.php';
require_once 'Swat/SwatApplication.php';

/**
 * Base class for an application module
 *
 * @package   Swat
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatApplicationModule extends SwatObject
{
	// {{{ private properties

	/**
	 * Reference to the application object that contains this module
	 *
	 * @var SwatApplication
	 */
	protected $app;

	// }}}
	// {{{ public function __construct()

	public function __construct(SwatApplication $app)
	{
		$this->app = $app;
	}

	// }}}
	// {{{ abstract public function init()

	abstract public function init();

	// }}}
}
?>
