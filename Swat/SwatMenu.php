<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatAbstractMenu.php';
require_once 'Swat/SwatMenuItem.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * A basic menu control
 *
 * @package   Swat
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see SwatMenuItem
 */
class SwatMenu extends SwatAbstractMenu
{
	// {{{ protected properties

	/**
	 * The set of SwatMenuItem objects contained in this menu
	 *
	 * @var array
	 */
	protected $items = array();

	// }}}
	// {{{ public function addItem()

	/**
	 * Adds a menu item to this menu
	 *
	 * @param SwatMenuItem $item the item to add.
	 */
	public function addItem(SwatMenuItem $item)
	{
		$this->items[] = $item;
		$item->parent = $this;
	}

	// }}}
	// {{{ public function init()

	/**
	 * Initializes this menu
	 */
	public function init()
	{
		parent::init();
		foreach ($this->items as $item)
			$item->init();
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this menu
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

		$ul_tag = new SwatHtmlTag('ul');
		$ul_tag->class = 'first-of-type';
		$ul_tag->open();

		foreach ($this->items as $item) {
			echo '<li class="yuimenuitem">';
			$item->display();
			echo '</li>';
		}

		$ul_tag->close();

		echo '</div>';

		$div_tag->close();

		if ($top_level)
			$this->displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
}

?>
