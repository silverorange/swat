<?php

require_once 'Swat/SwatObject.php';

/**
 * Entry for the navbar navigation tool
 *
 * @package   Swat
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see SwatNavBar
 */
class SwatNavBarEntry extends SwatObject
{
	/**
	 * The visible title of this entry
	 *
	 * @var string
	 */
	public $title;

	/**
	 * The URI that this entry points to
	 *
	 * This property is optional. If it is not present this entry will not
	 * display as a hyperlink.
	 *
	 * @var string
	 */
	public $uri;

	/**
	 * Creates a new navbar entry
	 *
	 * @param string $title the title of this entry.
	 * @param string $uri the URI this entry points to.
	 */
	public function __construct($title, $uri = null)
	{
		$this->title = $title;
		$this->uri = $uri;
	}
}

?>
