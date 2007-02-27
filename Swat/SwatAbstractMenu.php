<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatYUI.php';

/**
 * Abstract base class for menus in Swat
 *
 * Menu in Swat make use of the YUI menu widget and its progressive enhancement
 * features. Swat menus are always positioned statically. See
 * {@link http://developer.yahoo.com/yui/docs/YAHOO.widget.Menu.html#position
 * The YUI Menu documentation} for what this means.
 *
 * @package   Swat
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see SwatMenu
 * @see SwatGroupedMenu
 */
abstract class SwatAbstractMenu extends SwatControl
{
	// {{{ public properties

	/**
	 * Whether or not a mouse click outside this menu will hide this menu
	 *
	 * Defaults to true.
	 *
	 * @var boolean
	 */
	public $click_to_hide = true;

	/**
	 * Whether or not sub-menus of this menu will automatically display on
	 * mouse-over
	 *
	 * Defaults to true. Set to false to require clicking on a menu item to
	 * display a sub-menu.
	 *
	 * @var boolean
	 */
	public $auto_sub_menu_display = true;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new menu object
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see swatwidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->requires_id = true;

		$yui = new SwatYUI(array('menu'));
		$this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());
		$this->addStyleSheet('packages/swat/styles/swat-menu.css',
			Swat::PACKAGE_ID);
	}

	// }}}
	// {{{ public function setMenuItemValues()

	/**
	 * Sets the value of all {@link SwatMenuItem} objects within this menu
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
	// {{{ abstract public function getDescendants()

	/**
	 * Gets descendant widgets
	 *
	 * Retrieves an ordered array of all widgets in the widget subtree below 
	 * this menu. Widgets are ordered in the array as they are found in 
	 * a breadth-first traversal of the widget subtree.
	 *
	 * This method mirrors the behaviour of
	 * {@link SwatContainer::getDescendants()}.
	 *
	 * @param string $class_name optional class name. If set, only widgets that
	 *                            are instances of <i>$class_name</i> are
	 *                            returned.
	 *
	 * @return array the descendant widgets of this menu.
	 */
	abstract public function getDescendants($class_name = null);

	// }}}
	// {{{ protected function getJavaScriptClass()

	/**
	 * Gets the name of the JavaScript class to instantiate for this menu
	 *
	 * Sub-classes of this class may want to return a sub-class of the default
	 * JavaScript menu class.
	 *
	 * @return string the name of the JavaScript class to instantiate for this
	 *                 menu. Defaults to 'YAHOO.widget.Menu'.
	 */
	protected function getJavaScriptClass()
	{
		return 'YAHOO.widget.Menu';
	}

	// }}}
	// {{{ protected function getInlineJavaScript()

	/**
	 * Gets the inline JavaScript used by this menu control
	 *
	 * @return string the inline JavaScript used by this menu control.
	 */
	protected function getInlineJavaScript()
	{
		$properties = sprintf(
			"{ clicktohide: %s, autosubmenudisplay: %s, position: 'static' }",
			$this->click_to_hide ? 'true' : 'false',
			$this->auto_sub_menu_display ? 'true' : 'false');

		$javascript = sprintf(
			"function swat_create_menu_%1\$s(event)".
			"\n{".
				"\n\tvar %1\$s_obj = new %2\$s('%1\$s', %3\$s);".
				"\n\t%1\$s_obj.render();".
				"\n\t%1\$s_obj.show();".
			"\n}".
			"\nYAHOO.util.Event.onContentReady('%1\$s', ".
				"swat_create_menu_%1\$s);",
			$this->id,
			$this->getJavaScriptClass(),
			$properties);

		return $javascript;
	}

	// }}}
}

?>
