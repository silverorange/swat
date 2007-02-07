<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatObject.php';
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
