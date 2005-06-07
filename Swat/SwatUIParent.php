<?php

/**
 * Interface for widgets that are parents for other widgets.
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
interface SwatUIParent
{
	/**
	 * Adds a child object to this object
	 *
	 * @param SwatObject $child the child object to add to this object.
	 */
	public function addChild($child);
}

?>
