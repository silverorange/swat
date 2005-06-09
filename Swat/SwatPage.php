<?php

require_once 'Swat/SwatObject.php';

/**
 * Base class for a page
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatPage extends SwatObject
{
	/**
	 * Title of the page
	 *
	 * @var string
	 */
	public $title = '';

	/**
	 * Layout to use to display this page
	 *
	 * @var string 
	 */
	public $layout = 'default';

	/**
	 * Application object
	 * 
	 * A reference to the {@link SwatApplication} object that created
	 * this page.
	 *
	 * @var SwatApplication
	 */
	public $app = null;

	public function __construct()
	{
	}
}

?>
