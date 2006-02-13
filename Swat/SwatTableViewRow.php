<?php

require_once 'Swat/SwatUIObject.php';

/**
 * Base class for a extra row displayed at the bottom of a table view
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatTableViewRow extends SwatUIObject
{
	// {{{ public properties

	/**
	 * The {@link SwatTableView} associated with this row
	 *
	 * @var SwatTableView
	 */
	public $view = null;

	/**
	 * Unique identifier of this row 
	 *
	 * @param string
	 */
	public $id = null;

	// }}}
	// {{{ public function init()

	/**
	 * Initializes this row
	 *
	 * This method does nothing and is implemented here so subclasses do not
	 * need to implement it.
	 *
	 * Row initialization happens during table-view initialization. Rows are
	 * initialized after columns.
	 */
	public function init()
	{
	}

	// }}}
	// {{{ public function process()

	/**
	 * Processes this row
	 *
	 * This method does nothing and is implemented here so subclasses do not
	 * need to implement it.
	 *
	 * Row processing happens during table-view processing. Rows are processed
	 * after columns.
	 */
	public function process()
	{
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this row
	 *
	 * @param array $columns an array of columns to render in this row.
	 */
	public abstract function display(&$columns);

	// }}}
	// {{{ public function getHtmlHeadEntries()

	/**
	 * Gathers the SwatHtmlHeadEntry objects needed by this row
	 *
	 * @return array the SwatHtmlHeadEntry objects needed by this row.
	 *
	 * @see SwatUIObject::getHtmlHeadEntries()
	 */
	public function getHtmlHeadEntries()
	{
		return $this->html_head_entries;
	}

	// }}}
	// {{{ public function getInlineJavaScript()

	/**
	 * Gets the inline JavaScript required by this row 
	 * 
	 * All inline JavaScript is displayed after the table-view has been
	 * displayed.
	 *
	 * @return string the inline JavaScript required by this row.
	 */
	public function getInlineJavaScript()
	{
		return '';
	}

	// }}}
}

?>
