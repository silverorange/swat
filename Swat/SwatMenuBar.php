<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatMenu.php';

/**
 * A menu bar control
 *
 * A menu bar is like a {@link SwatMenu} but it displays menu items
 * horizontally in a bar.
 *
 * @package   Swat
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see SwatMenu, SwatMenuItem
 */
class SwatMenuBar extends SwatMenu
{
	// {{{ protected function getJavaScriptClass()

	/**
	 * Gets the name of the JavaScript class to instantiate for this menu
	 *
	 * @return string the name of the JavaScript class to instantiate for this
	 *                 menu. For the SwatMenuBar widget, this is
	 *                 'YAHOO.widget.MenuBar'.
	 */
	protected function getJavaScriptClass()
	{
		return 'YAHOO.widget.MenuBar';
	}

	// }}}
}

?>
