<?php

/**
 * A menu bar control
 *
 * A menu bar is like a {@link SwatMenu} but it displays menu items
 * horizontally in a bar.
 *
 * @package   Swat
 * @copyright 2007-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see SwatMenu
 * @see SwatMenuItem
 */
class SwatMenuBar extends SwatMenu
{

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

	/**
	 * Gets the CSS class name to use for menu items in this menu
	 *
	 * @return string the CSS class name to use for menu items in this menu.
	 */
	protected function getMenuItemCSSClassName()
	{
		return 'yuimenubaritem';
	}

	/**
	 * Gets the array of CSS classes that are applied to this menu bar
	 *
	 * @return array the array of CSS classes that are applied to this menu bar.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('yuimenubar');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

}

?>
