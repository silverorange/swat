<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatYUI.php';

/**
 * Abstract base class for menus in Swat
 *
 * Menu in Swat make use of the YUI menu widget and its progressive enhancement
 * features.
 *
 * @package   Swat
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see SwatMenu, SwatGroupedMenu
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
		$properties = sprintf('{ clicktohide: %s, autosubmenudisplay: %s }',
			$this->click_to_hide ? 'true' : 'false',
			$this->auto_sub_menu_display ? 'true' : 'false');

		$javascript = sprintf("var %s_obj = new %s('%s', %s);",
			$this->id,
			$this->getJavaScriptClass(),
			$this->id,
			$properties);

		$javascript.= sprintf("\n%s_obj.render('new-menu');\n%s_obj.show();",
			$this->id,
			$this->id);

		return $javascript;
	}

	// }}}
}

?>
