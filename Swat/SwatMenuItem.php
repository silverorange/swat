<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatString.php';
require_once 'Swat/SwatMenu.php';

/**
 * An item in a menu
 *
 * SwatMenuItem objects may be added to {@link SwatMenu} or
 * {@link SwatMenuGroup} widgets.
 *
 * @package   Swat
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see SwatMenu, SwatMenuGroup
 */
class SwatMenuItem extends SwatControl
{
	// {{{ public properties

	/**
	 * The URI this menu items links to
	 *
	 * If no URI is specified, this menu item is not displayed as a link.
	 *
	 * @var string
	 */
	public $uri;

	/**
	 * The user-visible title of this menu item
	 *
	 * @var string
	 */
	public $title;

	// }}}
	// {{{ protected properties

	/**
	 * The sub menu of this menu item
	 *
	 * @var SwatAbstractMenu
	 *
	 * @see SwatMenuItem::setSubMenu()
	 */
	protected $sub_menu;

	// }}}
	// {{{ public function setSubMenu()

	/**
	 * Sets the sub-menu of this menu item
	 *
	 * @param SwatAbstractMenu $menu the sub-menu for this menu item.
	 */
	public function setSubMenu(SwatAbstractMenu $menu)
	{
		$this->sub_menu = $menu;
		$menu->parent = $this;
	}

	// }}}
	// {{{ pubic function init()

	/**
	 * Initializes this menu item
	 */
	public function init()
	{
		if ($this->sub_menu !== null)
			$this->sub_menu->init();
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this menu item
	 *
	 * If this item has a sub-menu, the sub-menu is also displayed.
	 */
	public function display()
	{
		if ($this->uri === null) {
			echo SwatString::minimizeEntities($this->title);
		} else {
			$anchor_tag = new SwatHtmlTag('a');
			$anchor_tag->href = $this->uri;
			$anchor_tag->setContent($this->title);
			$anchor_tag->display();
		}

		$this->displaySubMenu();
	}

	// }}}
	// {{{ protected function displaySubMenu()

	/**
	 * Displays this menu item's sub-menu
	 */
	protected function displaySubMenu()
	{
		if ($this->sub_menu !== null) {
			$this->sub_menu->display(false);
		}
	}

	// }}}
}

?>
