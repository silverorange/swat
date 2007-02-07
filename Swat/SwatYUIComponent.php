<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatObject.php';
require_once 'Swat/SwatJavaScriptHtmlHeadEntry.php';
require_once 'Swat/SwatStyleSheetHtmlHeadEntry.php';
require_once 'Swat/SwatHtmlHeadEntrySet.php';
require_once 'Swat/SwatHtmlHeadEntry.php';
require_once 'Swat/SwatYUI.php';

/**
 * A component in the Yahoo UI Library
 *
 * This class is used internally by the {@link SwatYUI} class and is not meant
 * to be used by itself.
 *
 * @package   Swat
 * @copyright 2006-2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       SwatYUI
 */
class SwatYUIComponent extends SwatObject
{
	// {{{ private properties

	private $id;
	private $dependencies = array();
	private $html_head_entries = array();

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new YUI component
	 *
	 * @param string $id the identifier of this YUI component. This corresponds
	 *                    to a directory name under the <i>build</i> directory
	 *                    in the YUI distribution.
	 */
	public function __construct($id)
	{
		$this->id = $id;

		$this->html_head_entry_set['normal'] =
			new SwatHtmlHeadEntrySet();

		$this->html_head_entry_set['debug'] =
			new SwatHtmlHeadEntrySet();

		$this->html_head_entry_set['min'] =
			new SwatHtmlHeadEntrySet();
	}

	// }}}
	// {{{ public function addDependency()

	/**
	 * Adds a YUI component dependency to this YUI component
	 *
	 * @param SwatYUIComponent the YUI component this component depends on.
	 */
	public function addDependency(SwatYUIComponent $component)
	{
		$this->dependencies[] = $component;
	}

	// }}}
	// {{{ public function addJavaScript()

	/**
	 * Adds a {@link SwatJavaScriptHtmlHeadEntry} to this YUI component
	 *
	 * YUI component JavaScript is distributed in three modes:
	 * - debug
	 * - min
	 * - normal
	 *
	 * Adding JavaScript using this method creates HTML head entries for each
	 * of these three modes.
	 *
	 * @param string $component_directory optional. The YUI component directory
	 *                                     the JavaScript exists in. If the
	 *                                     directory is not specified, this
	 *                                     component's id is used.
	 */
	public function addJavaScript($component_directory = '')
	{
		if (strlen($component_directory) == 0)
			$component_directory = $this->id;

		$modes = array(
			'min'    => '-min',
			'debug'  => '-debug',
			'normal' => '',
		);

		$filename_template =
			'packages/yui/'.$component_directory.'/'.$this->id.'%s.js';

		foreach ($modes as $mode => $suffix) {
			$filename = sprintf($filename_template, $suffix);
			$this->html_head_entry_set[$mode]->addEntry(
				new SwatJavaScriptHtmlHeadEntry($filename,
					SwatYUI::PACKAGE_ID));
		}
	}

	// }}}
	// {{{ public function addStyleSheet()

	/**
	 * Adds a {@link SwatStyleSheetHtmlHeadEntry} to this YUI component
	 *
	 * YUI component style sheets are distributed in three modes:
	 * - min
	 * - normal
	 *
	 * Adding style sheets using this method creates HTML head entries for
	 * these two modes.
	 *
	 * @param string $component_directory optional. The YUI component directory
	 *                                     the style sheet exists in. If the
	 *                                     directory is not specified, this
	 *                                     component's id is used.
	 */
	public function addStyleSheet($component_directory = '')
	{
		if (strlen($component_directory) == 0)
			$component_directory = $this->id;

		$modes = array(
			'min'    => '-min',
			'debug'  => '',
			'normal' => '',
		);

		$filename_template =
			'packages/yui/'.$component_directory.'/'.$this->id.'%s.css';

		foreach ($modes as $mode => $suffix) {
			$filename = sprintf($filename_template, $suffix);
			$this->html_head_entry_set[$mode]->addEntry(
				new SwatStyleSheetHtmlHeadEntry($filename,
					SwatYUI::PACKAGE_ID));
		}
	}

	// }}}
	// {{{ public function getHtmlHeadEntrySet()

	public function getHtmlHeadEntrySet($mode = 'min')
	{
		$set = new SwatHtmlHeadEntrySet();
		if (isset($this->html_head_entry_set[$mode])) {
			foreach ($this->dependencies as $component) {
				$set->addEntrySet($component->getHtmlHeadEntrySet($mode));
			}
			$set->addEntrySet($this->html_head_entry_set[$mode]);
		}

		return $set;
	}

	// }}}
}

?>
