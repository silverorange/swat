<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatUIParent.php';
require_once 'Swat/SwatAbstractMenu.php';
require_once 'Swat/SwatMenuGroup.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';

/**
 * A menu control where menu items are grouped together
 *
 * @package   Swat
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see SwatMenuGroup
 */
class SwatGroupedMenu extends SwatAbstractMenu implements SwatUIParent
{
	// {{{ protected properties

	/**
	 * The set of SwatMenuGroup objects contained in this grouped menu
	 *
	 * @var array
	 */
	protected $groups = array();

	// }}}
	// {{{ public function addGroup()

	/**
	 * Adds a group to this grouped menu
	 *
	 * @param SwatMenuGroup $group the group to add.
	 */
	public function addGroup(SwatMenuGroup $group)
	{
		$this->groups[] = $group;
		$group->parent = $this;
	}

	// }}}
	// {{{ public function addChild()

	/**
	 * Adds a child object
	 * 
	 * This method fulfills the {@link SwatUIParent} interface. It is used 
	 * by {@link SwatUI} when building a widget tree and should not need to be
	 * called elsewhere. To add a menu group to a grouped menu, use 
	 * {@link SwatGroupedMenu::addGroup()}.
	 *
	 * @param SwatMenuGroup $child the child object to add.
	 *
	 * @throws SwatInvalidClassException
	 *
	 * @see SwatUIParent
	 * @see SwatGroupedMenu::addGroup()
	 */
	public function addChild(SwatObject $child)
	{
		if ($child instanceof SwatMenuGroup)
			$this->addGroup($child);
		else
			throw new SwatInvalidClassException(
				'Only SwatMenuGroup objects may be nested within a '.
				'SwatGroupedMenu object.', 0, $child);
	}

	// }}}
	// {{{ public function init()

	/**
	 * Initializes this grouped menu
	 */
	public function init()
	{
		parent::init();
		foreach ($this->groups as $group)
			$group->init();
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this grouped menu
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$displayed_classes = array();

		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id;
		$div_tag->class = 'yuimenu';
		$div_tag->open();

		echo '<div class="bd">';

		$first = true;
		foreach ($this->groups as $group) {
			if ($first) {
				$group->display(true);
				$first = false;
			} else {
				$group->display();
			}
		}

		echo '</div>';

		$div_tag->close();

		if ($this->parent === null || !($this->parent instanceof SwatMenuItem))
			$this->displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ public function getDescendants()

	/**
	 * Gets descendant widgets
	 *
	 * Retrieves an ordered array of all widgets in the widget subtree below 
	 * this grouped menu. Widgets are ordered in the array as they are found in 
	 * a breadth-first traversal of the widget subtree.
	 *
	 * This method mirrors the behaviour of
	 * {@link SwatContainer::getDescendants()}.
	 *
	 * @param string $class_name optional class name. If set, only widgets that
	 *                            are instances of <i>$class_name</i> are
	 *                            returned.
	 *
	 * @return array the descendant widgets of this grouped menu.
	 */
	public function getDescendants($class_name = null)
	{
		$out = array();

		foreach ($this->groups as $group) {
			if ($class_name === null || $group instanceof $class_name) {
				if ($group->id === null)
					$out[] = $group;
				else
					$out[$group->id] = $group;
			}
			$out = array_merge($out, $group->getDescendants($class_name));
		}

		return $out;
	}

	// }}}
}

?>
