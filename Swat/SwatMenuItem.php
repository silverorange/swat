<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatUIParent.php';
require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatString.php';
require_once 'Swat/SwatAbstractMenu.php';
require_once 'Swat/exceptions/SwatUndefinedStockTypeException.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';
require_once 'Swat/exceptions/SwatException.php';

/**
 * An item in a menu
 *
 * SwatMenuItem objects may be added to {@link SwatMenu} or
 * {@link SwatMenuGroup} widgets.
 *
 * @package   Swat
 * @copyright 2007-2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see SwatMenu
 * @see SwatMenuGroup
 */
class SwatMenuItem extends SwatControl implements SwatUIParent
{
	// {{{ public properties

	/**
	 * The URI-reference (see RFC2396) linked by this menu item
	 *
	 * If no link is specified, this menu item does not link to anything.
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

	/**
	 * The stock id of this menu item
	 *
	 * Specifying a stock id initializes this menu item with a set of
	 * stock values.
	 *
	 * @var string
	 *
	 * @see SwatToolLink::setFromStock()
	 */
	public $stock_id = null;

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

	/**
	 * A CSS class set by the stock_id of this menu item
	 *
	 * @var string
	 */
	protected $stock_class = null;

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
	 * @see SwatUIParent
	 * @see SwatMenuItem::setSubMenu()
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
		parent::init();

		if ($this->stock_id !== null)
			$this->setFromStock($this->stock_id, false);

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
		if (!$this->visible)
			return;

		parent::display();

		if ($this->link === null) {
			$span_tag = new SwatHtmlTag('span');
			$span_tag->id = $this->id;
			$span_tag->class = $this->getCSSClassString();
			$span_tag->setContent($this->title);
			$span_tag->display();
		} else {
			$anchor_tag = new SwatHtmlTag('a');
			$anchor_tag->id = $this->id;
			$anchor_tag->class = $this->getCSSClassString();

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
	// {{{ public function setFromStock()

	/**
	 * Sets the values of this menu item to a stock type
	 *
	 * Valid stock type ids are:
	 *
	 * - create
	 * - add
	 * - edit
	 * - delete
	 * - preview
	 * - change-order
	 * - help
	 * - print
	 * - email
	 *
	 * @param string $stock_id the identifier of the stock type to use.
	 * @param boolean $overwrite_properties whether to overwrite properties if
	 *                                       they are already set.
	 *
	 * @throws SwatUndefinedStockTypeException
	 */
	public function setFromStock($stock_id, $overwrite_properties = true)
	{
		switch ($stock_id) {
		case 'create':
			$title = Swat::_('Create');
			$class = 'swat-menu-item-create';
			break;

		case 'add':
			$title = Swat::_('Add');
			$class = 'swat-menu-item-add';
			break;

		case 'edit':
			$title = Swat::_('Edit');
			$class = 'swat-menu-item-edit';
			break;

		case 'delete':
			$title = Swat::_('Delete');
			$class = 'swat-menu-item-delete';
			break;

		case 'preview':
			$title = Swat::_('Preview');
			$class = 'swat-menu-item-preview';
			break;

		case 'change-order':
			$title = Swat::_('Change Order');
			$class = 'swat-menu-item-change-order';
			break;

		case 'help':
			$title = Swat::_('Help');
			$class = 'swat-menu-item-help';
			break;

		case 'print':
			$title = Swat::_('Print');
			$class = 'swat-menu-item-print';
			break;

		case 'email':
			$title = Swat::_('Email');
			$class = 'swat-menu-item-email';
			break;

		default:
			throw new SwatUndefinedStockTypeException(
				"Stock type with id of '{$stock_id}' not found.",
				0, $stock_id);
		}

		if ($overwrite_properties || ($this->title === null))
			$this->title = $title;

		$this->stock_class = $class;
	}

	// }}}
	// {{{ public function getDescendants()

	/**
	 * Gets descendant UI-objects
	 *
	 * @param string $class_name optional class name. If set, only UI-objects
	 *                            that are instances of <i>$class_name</i> are
	 *                            returned.
	 *
	 * @return array the descendant UI-objects of this menu item. If
	 *                descendant objects have identifiers, the identifier is
	 *                used as the array key.
	 *
	 * @see SwatUIParent::getDescendants()
	 */
	public function getDescendants($class_name = null)
	{
		if (!($class_name === null ||
			class_exists($class_name) || interface_exists($class_name)))
			return array();

		$out = array();

		if ($this->sub_menu !== null) {
			if ($class_name === null ||
				$this->sub_menu instanceof $class_name) {
				if ($this->sub_menu->id === null)
					$out[] = $this->sub_menu;
				else
					$out[$this->sub_menu->id] = $this->sub_menu;
			}

			if ($this->sub_menu instanceof SwatUIParent)
				$out = array_merge($out,
					$this->sub_menu->getDescendants($class_name));
		}

		return $out;
	}

	// }}}
	// {{{ public function getFirstDescendant()

	/**
	 * Gets the first descendant UI-object of a specific class
	 *
	 * @param string $class_name class name to look for.
	 *
	 * @return SwatUIObject the first descendant widget or null if no matching
	 *                       descendant is found.
	 *
	 * @see SwatUIParent::getFirstDescendant()
	 */
	public function getFirstDescendant($class_name)
	{
		if (!class_exists($class_name) && !interface_exists($class_name))
			return null;

		$out = null;

		if ($this->sub_menu instanceof $class_name)
			$out = $this->sub_menu;

		if ($out === null && $this->sub_menu instanceof SwatUIParent)
			$out = $this->sub_menu->getFirstDescendant($class_name);

		return $out;
	}

	// }}}
	// {{{ public function getDescendantStates()

	/**
	 * Gets descendant states
	 *
	 * Retrieves an array of states of all stateful UI-objects in the widget
	 * subtree below this menu item.
	 *
	 * @return array an array of UI-object states with UI-object identifiers as
	 *                array keys.
	 */
	public function getDescendantStates()
	{
		$states = array();

		foreach ($this->getDescendants('SwatState') as $id => $object)
			$states[$id] = $object->getState();

		return $states;
	}

	// }}}
	// {{{ public function setDescendantStates()

	/**
	 * Sets descendant states
	 *
	 * Sets states on all stateful UI-objects in the widget subtree below this
	 * menu item.
	 *
	 * @param array $states an array of UI-object states with UI-object
	 *                       identifiers as array keys.
	 */
	public function setDescendantStates(array $states)
	{
		foreach ($this->getDescendants('SwatState') as $id => $object)
			if (isset($states[$id]))
				$object->setState($states[$id]);
	}

	// }}}
	// {{{ public function copy()

	/**
	 * Performs a deep copy of the UI tree starting with this UI object
	 *
	 * @param string $id_suffix optional. A suffix to append to copied UI
	 *                           objects in the UI tree.
	 *
	 * @return SwatUIObject a deep copy of the UI tree starting with this UI
	 *                       object.
	 *
	 * @see SwatUIObject::copy()
	 */
	public function copy($id_suffix = '')
	{
		$copy = parent::copy($id_suffix);

		if ($this->sub_menu !== null) {
			$copy_sub_menu = $this->sub_menu->copy($id_suffix);
			$copy_sub_menu->parent = $copy;
			$copy->sub_menu = $copy_sub_menu;
		}

		return $copy;
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
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this menu item
	 *
	 * @return array the array of CSS classes that are applied to this menu
	 *                item.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-menu-item');

		if ($this->stock_class !== null)
			$classes[] = $this->stock_class;

		$classes = array_merge($classes, parent::getCSSClassNames());

		return $classes;
	}

	// }}}
}

?>
