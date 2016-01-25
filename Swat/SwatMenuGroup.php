<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatUIParent.php';
require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatMenuItem.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';

/**
 * A group of menu items
 *
 * SwatMenuGroup objects are added to {@link SwatGroupedMenu} objects and are
 * used to group together a set of {@link SwatMenuItem} objects.
 *
 * @package   Swat
 * @copyright 2007-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see SwatGroupedMenu
 * @see SwatMenuItem
 */
class SwatMenuGroup extends SwatControl implements SwatUIParent
{
	// {{{ public properties

	/**
	 * The user-visible title of this group
	 *
	 * @var string
	 */
	public $title;

	// }}}
	// {{{ protected properties

	/**
	 * The set of SwatMenuItem objects contained in this group
	 *
	 * @var array
	 */
	protected $items = array();

	// }}}
	// {{{ public function addItem()

	/**
	 * Adds a menu item to this group
	 *
	 * @param SwatMenuItem $item the item to add.
	 */
	public function addItem(SwatMenuItem $item)
	{
		$this->items[] = $item;
		$item->parent = $this;
	}

	// }}}
	// {{{ public function addChild()

	/**
	 * Adds a child object
	 *
	 * This method fulfills the {@link SwatUIParent} interface. It is used
	 * by {@link SwatUI} when building a widget tree and should not need to be
	 * called elsewhere. To add a menu item to a menu group, use
	 * {@link SwatMenuGroup::addItem()}.
	 *
	 * @param SwatMenuItem $child the child object to add.
	 *
	 * @throws SwatInvalidClassException
	 *
	 * @see SwatUIParent
	 * @see SwatMenuGroup::addItem()
	 */
	public function addChild(SwatObject $child)
	{
		if ($child instanceof SwatMenuItem)
			$this->addItem($child);
		else
			throw new SwatInvalidClassException(
				'Only SwatMenuItem objects may be nested within a '.
				'SwatMenuGroup object.', 0, $child);
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this menu group
	 *
	 * @param boolean $first optional. Whether or not this group is the first
	 *                        group in a {@link SwatGroupedMenu}. Defaults to
	 *                        false.
	 */
	public function display($first = false)
	{
		if (!$this->visible)
			return;

		parent::display();

		if ($this->title !== null) {
			$header_tag = new SwatHtmlTag('h6');
			if ($first)
				$header_tag->class = 'first-of-type';

			$header_tag->setContent($this->title);
			$header_tag->display();
		}

		$ul_tag = new SwatHtmlTag('ul');
		$ul_tag->class = ($this->title === null) ? '' : 'hastitle ';
		if ($first)
			$ul_tag->class.= 'first-of-type';

		$ul_tag->open();

		$li_tag = new SwatHtmlTag('li');
		$li_tag->class = 'first-of-type';
		$first = true;
		foreach ($this->items as $item) {
			ob_start();
			$item->display();
			$content = ob_get_clean();
			if ($content != '') {
				$li_tag->setContent($content, 'text/xml');
				$li_tag->display();

				if ($first) {
					$li_tag->class = null;
					$first = false;
				}
			}
		}

		$ul_tag->close();
	}

	// }}}
	// {{{ public function setMenuItemValues()

	/**
	 * Sets the value of all {@link SwatMenuItem} objects within this menu
	 * group
	 *
	 * This is usually easier than setting all the values manually if the
	 * values are dynamic.
	 *
	 * @param string $value
	 */
	public function setMenuItemValues($value)
	{
		$items = $this->getDescendants('SwatMenuItem');
		foreach ($items as $item)
			$item->value = $value;
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
	 * @return array the descendant UI-objects of this menu group. If
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

		foreach ($this->items as $item) {
			if ($class_name === null || $item instanceof $class_name) {
				if ($item->id === null)
					$out[] = $item;
				else
					$out[$item->id] = $item;
			}

			if ($item instanceof SwatUIParent)
				$out = array_merge($out, $item->getDescendants($class_name));
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
	 * @return SwatUIObject the first descendant UI-object or null if no
	 *                       matching descendant is found.
	 *
	 * @see SwatUIParent::getFirstDescendant()
	 */
	public function getFirstDescendant($class_name)
	{
		if (!class_exists($class_name) && !interface_exists($class_name))
			return null;

		$out = null;

		foreach ($this->items as $item) {
			if ($item instanceof $class_name) {
				$out = $item;
				break;
			}

			if ($item instanceof SwatUIParent) {
				$out = $item->getFirstDescendant($class_name);
				if ($out !== null)
					break;
			}
		}

		return $out;
	}

	// }}}
	// {{{ public function getDescendantStates()

	/**
	 * Gets descendant states
	 *
	 * Retrieves an array of states of all stateful UI-objects in the widget
	 * subtree below this menu group.
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
	 * menu group.
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

		foreach ($this->items as $key => $item) {
			$copy_item = $item->copy($id_suffix);
			$copy_item->parent = $copy;
			$copy->items[$key] = $copy_item;
		}

		return $copy;
	}

	// }}}
}

?>
