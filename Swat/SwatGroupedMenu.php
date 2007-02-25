<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatAbstractMenu.php';
require_once 'Swat/SwatMenuGroup.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * A menu control where menu items are grouped together
 *
 * @package   Swat
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see SwatMenuGroup
 */
class SwatGroupedMenu extends SwatAbstractMenu
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
	 *
	 * @param boolean $top_level optional. Whether or not this menu is a
	 *                            top-level menu. Defaults to true.
	 */
	public function display($top_level = true)
	{
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

		if ($top_level)
			$this->displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
}

?>
