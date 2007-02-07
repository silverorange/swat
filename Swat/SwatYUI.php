<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatJavaScriptHtmlHeadEntry.php';
require_once 'Swat/SwatStyleSheetHtmlHeadEntry.php';
require_once 'Swat/SwatHtmlHeadEntrySet.php';
require_once 'Swat/SwatHtmlHeadEntry.php';

/**
 * A component in the Yahoo UI Library
 *
 * This class is used internally by the {@link SwatYUI} class and is not meant
 * to be used by itself.
 *
 * @package   Swat
 * @copyright 2006-2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class YUIComponent
{
	// {{{ private properties

	private $id;
	private $dependencies = array();
	private $html_head_entries = array();

	// }}}
	// {{{ public function __construct()

	public function __construct($id)
	{
		$this->id = $id;

		$this->html_head_entry_set[YUI::MODE_NORMAL] =
			new SwatHtmlHeadEntrySet();

		$this->html_head_entry_set[YUI::MODE_DEBUG] =
			new SwatHtmlHeadEntrySet();

		$this->html_head_entry_set[YUI::MODE_MIN] =
			new SwatHtmlHeadEntrySet();
	}

	// }}}
	// {{{ public function addDependency()

	public function addDependency(YUIComponent $component)
	{
		$this->dependencies[] = $component;
	}

	// }}}
	// {{{ public function addJavaScript()

	public function addJavaScript($mode, $filename = '')
	{
		if (strlen($filename) == 0) {
			$filename = 'packages/yui/'.$this->id.'/'.$this->id;
			if (strlen($mode) > 0)
				$filename.= '-'.$mode;

			$filename.='.js';
		}

		if ($this->validateMode($mode))
			$this->html_head_entry_set[$mode]->addEntry(
				new SwatJavaScriptHtmlHeadEntry($filename, YUI::PACKAGE_ID));
	}

	// }}}
	// {{{ public function addStyleSheet()

	public function addStyleSheet($mode, $filename = '')
	{
		if (strlen($filename) == 0) {
			$filename = 'packages/yui/'.$this->id.'/'.$this->id;
			if (strlen($mode) > 0)
				$filename.= '-'.$mode;

			$filename.='.css';
		}

		if ($this->validateMode($mode))
			$this->html_head_entry_set[$mode]->addEntry(
				new SwatStyleSheetHtmlHeadEntry($filename, YUI::PACKAGE_ID));
	}

	// }}}
	// {{{ public function getHtmlHeadEntrySet()

	public function getHtmlHeadEntrySet($mode = YUI::MODE_MIN)
	{
		$set = new SwatHtmlHeadEntrySet();
		if ($this->validateMode($mode)) {
			foreach ($this->dependencies as $component) {
				$set->addEntrySet($component->getHtmlHeadEntrySet());
			}
			$set->addEntrySet($this->html_head_entry_set[$mode]);
		}

		return $set;
	}

	// }}}
	// {{{ private function validateMode()

	private function validateMode($mode)
	{
		static $valid_modes =
			array(YUI::MODE_NORMAL, YUI::MODE_DEBUG, YUI::MODE_MIN);

		return (in_array($mode, $valid_modes));
	}

	// }}}
}

?>
<?php

require_once 'Swat/SwatHtmlHeadEntrySet.php';

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

/**
 * Object for building Swat HTML head entry dependencies for Yahoo UI Library
 * components
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
class SwatYUI
{
	// {{{ class constants

	const PACKAGE_ID = 'YUI';

	const MODE_MIN = 'min';
	const MODE_DEBUG = 'debug';
	const MODE_NORMAL = '';

	// }}}
	// {{{ private static properties

	private static $components = array();

	// }}}
	// {{{ private properties

	private $html_head_entry_set;

	// }}}
	// {{{ public function __construct()

	public function __construct($component_ids = array(),
		$mode = YUI::MODE_MIN)
	{
		self::buildComponents();

		if (!is_array($component_ids))
			$component_ids = array($component_ids);

		if (count($component_ids) == 0) {
			$component_ids = array(
				'animation',
				'autocomplete',
				'calendar',
				'connection',
				'container',
				'dom',
				'dragdrop',
				'event',
				'fonts',
				'grids',
				'logger',
				'menu',
				'reset',
				'slider',
				'treeview',
				'yahoo',
			);
		}

		$this->html_head_entry_set =
			$this->buildHtmlHeadEntrySet($component_ids, $mode);
	}

	// }}}
	// {{{ public function getHtmlHeadEntrySet()

	public function getHtmlHeadEntrySet()
	{
		return $this->html_head_entry_set;
	}

	// }}}
	// {{{ private function buildHtmlHeadEntrySet()

	private function buildHtmlHeadEntrySet($component_ids, $mode)
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
	
	private static function buildComponents()
	{
		static $components_built = false;
		static $components = array();

		if ($components_built)
			return;

		$components['animation'] = new YUIComponent('animation');
		$components['animation']->addJavaScript(YUI::MODE_DEBUG);
		$components['animation']->addJavaScript(YUI::MODE_MIN);
		$components['animation']->addJavaScript(YUI::MODE_NORMAL);

		$components['autocomplete'] = new YUIComponent('autocomplete');
		$components['autocomplete']->addJavaScript(YUI::MODE_DEBUG);
		$components['autocomplete']->addJavaScript(YUI::MODE_MIN);
		$components['autocomplete']->addJavaScript(YUI::MODE_NORMAL);

		$components['calendar'] = new YUIComponent('calendar');
		$components['calendar']->addJavaScript(YUI::MODE_DEBUG);
		$components['calendar']->addJavaScript(YUI::MODE_MIN);
		$components['calendar']->addJavaScript(YUI::MODE_NORMAL);

		$components['connection'] = new YUIComponent('connection');
		$components['connection']->addJavaScript(YUI::MODE_DEBUG);
		$components['connection']->addJavaScript(YUI::MODE_MIN);
		$components['connection']->addJavaScript(YUI::MODE_NORMAL);

		$components['container'] = new YUIComponent('container');
		$components['container']->addJavaScript(YUI::MODE_DEBUG);
		$components['container']->addJavaScript(YUI::MODE_MIN);
		$components['container']->addJavaScript(YUI::MODE_NORMAL);

		$components['container_core'] = new YUIComponent('container');
		$components['container_core']->addJavaScript(YUI::MODE_DEBUG,
			'packages/yui/container/container_core-debug.js');

		$components['container_core']->addJavaScript(YUI::MODE_MIN,
			'packages/yui/container/container_core-min.js');

		$components['container_core']->addJavaScript(YUI::MODE_NORMAL,
			'packages/yui/container/container_core.js');

		$components['dom'] = new YUIComponent('dom');
		$components['dom']->addJavaScript(YUI::MODE_DEBUG);
		$components['dom']->addJavaScript(YUI::MODE_MIN);
		$components['dom']->addJavaScript(YUI::MODE_NORMAL);

		$components['dragdrop'] = new YUIComponent('dragdrop');
		$components['dragdrop']->addJavaScript(YUI::MODE_DEBUG);
		$components['dragdrop']->addJavaScript(YUI::MODE_MIN);
		$components['dragdrop']->addJavaScript(YUI::MODE_NORMAL);

		$components['event'] = new YUIComponent('event');
		$components['event']->addJavaScript(YUI::MODE_DEBUG);
		$components['event']->addJavaScript(YUI::MODE_MIN);
		$components['event']->addJavaScript(YUI::MODE_NORMAL);

		$components['fonts'] = new YUIComponent('fonts');
		$components['fonts']->addStyleSheet(YUI::MODE_MIN);
		$components['fonts']->addStyleSheet(YUI::MODE_NORMAL);

		$components['grids'] = new YUIComponent('grids');
		$components['grids']->addStyleSheet(YUI::MODE_MIN);
		$components['grids']->addStyleSheet(YUI::MODE_NORMAL);

		$components['logger'] = new YUIComponent('logger');
		$components['logger']->addJavaScript(YUI::MODE_DEBUG);
		$components['logger']->addJavaScript(YUI::MODE_MIN);
		$components['logger']->addJavaScript(YUI::MODE_NORMAL);

		$components['menu'] = new YUIComponent('menu');
		$components['menu']->addJavaScript(YUI::MODE_DEBUG);
		$components['menu']->addJavaScript(YUI::MODE_MIN);
		$components['menu']->addJavaScript(YUI::MODE_NORMAL);
		$components['menu']->addStyleSheet(YUI::MODE_NORMAL,
			'packages/yui/menu/assets/menu.css');

		$components['menu']->addStyleSheet(YUI::MODE_MIN,
			'packages/yui/menu/assets/menu.css');

		$components['reset'] = new YUIComponent('reset');
		$components['reset']->addStyleSheet(YUI::MODE_MIN);
		$components['reset']->addStyleSheet(YUI::MODE_NORMAL);

		$components['slider'] = new YUIComponent('slider');
		$components['slider']->addJavaScript(YUI::MODE_DEBUG);
		$components['slider']->addJavaScript(YUI::MODE_MIN);
		$components['slider']->addJavaScript(YUI::MODE_NORMAL);

		$components['treeview'] = new YUIComponent('treeview');
		$components['treeview']->addJavaScript(YUI::MODE_DEBUG);
		$components['treeview']->addJavaScript(YUI::MODE_MIN);
		$components['treeview']->addJavaScript(YUI::MODE_NORMAL);

		$components['yahoo'] = new YUIComponent('yahoo');
		$components['yahoo']->addJavaScript(YUI::MODE_DEBUG);
		$components['yahoo']->addJavaScript(YUI::MODE_MIN);
		$components['yahoo']->addJavaScript(YUI::MODE_NORMAL);

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
}

?>
