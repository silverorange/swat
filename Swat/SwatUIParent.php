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
	 * This method is used by {@link SwatUI} when building a widget tree and
	 * does not need to be called elsewhere. To add a field to a field view,
	 * use {@link SwatFieldView::appendField()}.
	 *
	 * @param SwatObject $child the child object to add to this object.
	 */
	public function addChild($child);
}

?>
