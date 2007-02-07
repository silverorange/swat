<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatObject.php';
require_once 'Swat/SwatHtmlHeadEntrySet.php';
require_once 'Swat/SwatYUIComponent.php';
require_once 'Swat/exceptions/SwatException.php'

/**
 * Object for building Swat HTML head entry dependencies for Yahoo UI Library
 * components
 *
 * Most of Swat's UI objects using JavaScript make use of the Yahoo User
 * Interface Library (YUI) to abstract cross-browser event-handling, DOM
 * manipulation and CSS positioning. YUI's JavaScript is separated into
 * separate components. This class takes a list of YUI components and generates
 * a set of {@link SwatHtmlHeadEntry} objects required for the YUI component.
 * This greatly simplifies using YUI in Swat UI objects.
 *
 * YUI components are distributed in three modes:
 * - min
 * - normal
 * - debug
 *
 * The 'normal' mode is regular JavaScript and style-sheet code with full
 * documentation and whitespace formatting. The 'min' mode is the same as
 * 'normal' except the whitespace has been compressed and the comments have
 * been stripped. The 'debug' mode is the same as normal except special
 * debugging code has been added to the JavaScript.
 *
 * When using SwatYUI to generate a set of HTML head entries, you can specify
 * one of the three modes to suit your needs.
 *
 * Example usage:
 * <code>
 * $yui = new SwatYUI('dom');
 * $html_head_entries = $yui->getHtmlHeadEntrySet();
 * </code>
 *
 * @package   Swat
 * @copyright 2006-2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatYUI extends SwatObject
{
	// {{{ class constants

	/**
	 * @todo document me or get rid of me
	 */
	const PACKAGE_ID = 'YUI';

	// }}}
	// {{{ private static properties

	/**
	 * Static component definitions
	 *
	 * This array is used for each instance of SwatYUI and contains component
	 * definitions and dependency information.
	 *
	 * @var array
	 * @see SwatYUI::buildComponents()
	 */
	private static $components = array();

	// }}}
	// {{{ private properties

	/**
	 * The {@link SwatHtmlHeadEntrySet} required for this SwaYUI object
	 *
	 * @var SwatHtmlHeadEntrySet
	 */
	private $html_head_entry_set;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new SwatYUI HTML head entry set building object
	 *
	 * @param array $component_ids an array of YUI component ids to build a
	 *                              HTML head entry set for.
	 * @param string $mode the YUI component mode to use. Should be one of the
	 *                      'min', 'normal' or 'debug'. The default mode is
	 *                      'min'.
	 */
	public function __construct(array $component_ids, $mode = 'min')
	{
		$this->checkInstall();

		self::buildComponents();

		if (!is_array($component_ids))
			$component_ids = array($component_ids);

		$this->html_head_entry_set =
			$this->buildHtmlHeadEntrySet($component_ids, $mode);
	}

	// }}}
	// {{{ public function getHtmlHeadEntrySet()

	/**
	 * Gets the HTML head entry set required for the YUI components of this
	 * object
	 *
	 * @return SwatHtmlHeadEntrySet
	 */
	public function getHtmlHeadEntrySet()
	{
		return $this->html_head_entry_set;
	}

	// }}}
	// {{{ private function buildHtmlHeadEntrySet()

	/**
	 * Builds the HTML head entry set required for the YUI components of this
	 * object
	 *
	 * @param array $component_ids an array of YUI component ids to build
	 *                              HTML head entries for.
	 * @param string $mode the YUI component mode to use.
	 *
	 * @return SwatHtmlHeadEntrySet the full constructed set of HTML head
	 *                               entries.
	 */
	private function buildHtmlHeadEntrySet(array $component_ids, $mode)
	{
		$set = new SwatHtmlHeadEntrySet();
		foreach ($component_ids as $component_id) {
			$set->addEntrySet(
				self::$components[$component_id]->getHtmlHeadEntrySet($mode));
		}
		return $set;
	}

	// }}}
	// {{{ private static function buildComponents()

	/**
	 * Builds the YUI component definitions and dependency information
	 *
	 * Since this is a large data structure, the actual building is only done
	 * once and the result is stored in a static class variable.
	 */
	private static function buildComponents()
	{
		static $components_built = false;
		static $components = array();

		if ($components_built)
			return;

		$components['animation'] = new SwatYUIComponent('animation');
		$components['animation']->addJavaScript();

		$components['autocomplete'] = new SwatYUIComponent('autocomplete');
		$components['autocomplete']->addJavaScript();

		$components['calendar'] = new SwatYUIComponent('calendar');
		$components['calendar']->addJavaScript();

		$components['connection'] = new SwatYUIComponent('connection');
		$components['connection']->addJavaScript();

		$components['container'] = new SwatYUIComponent('container');
		$components['container']->addJavaScript();

		$components['container_core'] = new SwatYUIComponent('container_core');
		$components['container_core']->addJavaScript('container');

		$components['dom'] = new SwatYUIComponent('dom');
		$components['dom']->addJavaScript();

		$components['dragdrop'] = new SwatYUIComponent('dragdrop');
		$components['dragdrop']->addJavaScript();

		$components['event'] = new SwatYUIComponent('event');
		$components['event']->addJavaScript();

		$components['fonts'] = new SwatYUIComponent('fonts');
		$components['fonts']->addStyleSheet();

		$components['grids'] = new SwatYUIComponent('grids');
		$components['grids']->addStyleSheet();

		$components['logger'] = new SwatYUIComponent('logger');
		$components['logger']->addJavaScript();

		$components['menu'] = new SwatYUIComponent('menu');
		$components['menu']->addJavaScript();
		$components['menu']->addStyleSheet('menu/assets');

		$components['reset'] = new SwatYUIComponent('reset');
		$components['reset']->addStyleSheet();

		$components['slider'] = new SwatYUIComponent('slider');
		$components['slider']->addJavaScript();

		$components['treeview'] = new SwatYUIComponent('treeview');
		$components['treeview']->addJavaScript();

		$components['yahoo'] = new SwatYUIComponent('yahoo');
		$components['yahoo']->addJavaScript();

		// dependencies
		$components['animation']->addDependency($components['yahoo']);
		$components['animation']->addDependency($components['dom']);
		$components['animation']->addDependency($components['event']);

		$components['autocomplete']->addDependency($components['yahoo']);
		$components['autocomplete']->addDependency($components['dom']);
		$components['autocomplete']->addDependency($components['event']);
		$components['autocomplete']->addDependency($components['connection']);
		$components['autocomplete']->addDependency($components['animation']);

		$components['calendar']->addDependency($components['yahoo']);
		$components['calendar']->addDependency($components['dom']);
		$components['calendar']->addDependency($components['event']);

		$components['connection']->addDependency($components['yahoo']);
		$components['connection']->addDependency($components['event']);

		$components['container']->addDependency($components['yahoo']);
		$components['container']->addDependency($components['dom']);
		$components['container']->addDependency($components['event']);
		$components['container']->addDependency($components['connection']);
		$components['container']->addDependency($components['animation']);

		$components['container_core']->addDependency($components['yahoo']);
		$components['container_core']->addDependency($components['dom']);
		$components['container_core']->addDependency($components['event']);
		$components['container_core']->addDependency($components['connection']);
		$components['container_core']->addDependency($components['animation']);

		$components['dom']->addDependency($components['yahoo']);

		$components['dragdrop']->addDependency($components['yahoo']);
		$components['dragdrop']->addDependency($components['dom']);
		$components['dragdrop']->addDependency($components['event']);

		$components['event']->addDependency($components['yahoo']);

		$components['grids']->addDependency($components['fonts']);

		$components['logger']->addDependency($components['yahoo']);
		$components['logger']->addDependency($components['dom']);
		$components['logger']->addDependency($components['event']);
		$components['logger']->addDependency($components['dragdrop']);
		
		$components['menu']->addDependency($components['yahoo']);
		$components['menu']->addDependency($components['dom']);
		$components['menu']->addDependency($components['event']);
		$components['menu']->addDependency($components['fonts']);
		$components['menu']->addDependency($components['container_core']);

		$components['slider']->addDependency($components['yahoo']);
		$components['slider']->addDependency($components['dom']);
		$components['slider']->addDependency($components['event']);
		$components['slider']->addDependency($components['dragdrop']);

		$components['treeview']->addDependency($components['yahoo']);

		self::$components = $components;

		$components_built = true;
	}

	// }}}
	// {{{ private function checkInstall()

	/**
	 * Verifies the YUI library is installed and displays a helpful message if
	 * it is not*
	 *
	 * @throws SwatException
	 */
	private function checkInstall()
	{
		$yui_installed = file_exists('packages/yui/yahoo/yahoo.js');
		if (!$yui_installed)
			throw new SwatException();
	}

	// }}}
}

?>
