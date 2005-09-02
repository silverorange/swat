<?php

require_once 'Swat/SwatObject.php';

/**
 * Entry for the navbar navigation tool
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see SwatNavBar
 */
class SwatNavBarEntry extends SwatObject
{
	public $title;
	public $uri;

	public function __construct($title, $uri = null)
	{
		$this->title = $title;
		$this->uri = $uri;
	}
}

?>
