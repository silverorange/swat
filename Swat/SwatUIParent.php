<?php

/**
 * Interface for widgets that are parents for other widgets.
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
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
