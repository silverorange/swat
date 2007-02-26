<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatUIParent.php';
require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatString.php';
require_once 'Swat/SwatAbstractMenu.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';
require_once 'Swat/exceptions/SwatException.php';

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
class SwatMenuItem extends SwatControl implements SwatUIParent
{
	// {{{ public properties

	/**
	 * The URI-reference (see RFC2396) linked by this menu item
	 *
	 * If no link is specified, this menu does not link to anything.
	 *
	 * Optionally uses vsprintf() syntax, for example:
	 * <code>
	 * $item->link = 'MySection/MyPage/%s?id=%s';
	 * </code>
	 *
	 * @var string
	 *
	 * @see SwatMenuItem::$value
	 */
	public $link;

	/**
	 * A value or array of values to substitute into the <i>link</i> property
	 * of this menu item
	 *
	 * The value property may be specified either as an array of values or as
	 * a single value. If an array is passed, a call to vsprintf() is done
	 * on the {@link SwatMenuItem::$link} property. If the value is a string
	 * a single sprintf() call is made.
	 *
	 * @var array|string 
	 *
	 * @see SwatMenuItem::$link
	 */
	public $value;

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
	// {{{ public function addChild()

	/**
	 * Adds a child object
	 * 
	 * This method fulfills the {@link SwatUIParent} interface. It is used 
	 * by {@link SwatUI} when building a widget tree and should not need to be
	 * called elsewhere. To set the sub-menu for a menu item, use 
	 * {@link SwatMenuItem::setSubMenu()}.
	 *
	 * @param SwatAbstractMenu $child the child object to add.
	 *
	 * @throws SwatInvalidClassException
	 * @throws SwatException if this menu item already has a sub-menu.
	 *
	 * @see SwatUIParent, SwatUI, SwatMenuItem::setSubMenu()
	 */
	public function addChild(SwatObject $child)
	{
		if ($this->sub_menu === null) {
			if ($child instanceof SwatAbstractMenu)
				$this->setSubMenu($child);
			else
				throw new SwatInvalidClassException(
					'Only a SwatAbstractMenu object may be nested within a '.
					'SwatMenuItem object.', 0, $child);
		} else {
			throw new SwatException(
				'Can only add one sub-menu to a menu item.');
		}
	}

	// }}}
	// {{{ public function init()

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
		if ($this->link === null) {
			echo SwatString::minimizeEntities($this->title);
		} else {
			$anchor_tag = new SwatHtmlTag('a');

			if ($this->value === null)
				$anchor_tag->href = $this->link;
			elseif (is_array($this->value))
				$anchor_tag->href = vsprintf($this->link, $this->value);
			else
				$anchor_tag->href = sprintf($this->link, $this->value);

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
		if ($this->sub_menu !== null)
			$this->sub_menu->display();
	}

	// }}}
}

?>
