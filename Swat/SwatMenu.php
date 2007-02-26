<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatUIParent.php';
require_once 'Swat/SwatAbstractMenu.php';
require_once 'Swat/SwatMenuItem.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';

/**
 * A basic menu control
 *
 * @package   Swat
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see SwatMenuItem
 */
class SwatMenu extends SwatAbstractMenu implements SwatUIParent
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
	// {{{ public function addChild()

	/**
	 * Adds a child object
	 * 
	 * This method fulfills the {@link SwatUIParent} interface. It is used 
	 * by {@link SwatUI} when building a widget tree and should not need to be
	 * called elsewhere. To add a menu item to a menu, use 
	 * {@link SwatMenu::addItem()}.
	 *
	 * @param SwatMenuItem $child the child object to add.
	 *
	 * @throws SwatInvalidClassException
	 *
	 * @see SwatUIParent, SwatUI, SwatMenu::addItem()
	 */
	public function addChild(SwatObject $child)
	{
		if ($child instanceof SwatMenuItem)
			$this->addItem($child);
		else
			throw new SwatInvalidClassException(
				'Only SwatMenuItem objects may be nested within a '.
				'SwatMenu object.', 0, $child);
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
	 */
	public function display()
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

		if ($this->parent === null || !($this->parent instanceof SwatMenuItem))
			$this->displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
}

?>
