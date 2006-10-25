<?php

/**
 * Interface for widgets that are parents for other widgets.
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
interface SwatUIParent
{
	// {{{ public function addChild()

	/**
	 * Adds a child object to this parent object
	 *
	 * This method is used by {@link SwatUI} when building a widget tree and
	 * does not need to be called elsewhere. To add a field to a field view,
	 * use {@link SwatFieldView::appendField()}.
	 *
	 * @param SwatObject $child the child object to add to this parent object.
	 */
	public function addChild(SwatObject $child);

	// }}}
}

?>
