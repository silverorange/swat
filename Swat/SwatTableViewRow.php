<?php

require_once 'Swat/SwatUIBase.php';

/**
 * Base class for a extra row displayed at the bottom of a table view
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatTableViewRow extends SwatUIBase
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
	 * @see SwatUIBase::getHtmlHeadEntries()
	 */
	public function getHtmlHeadEntries()
	{
		return $this->html_head_entries;
	}

	// }}}
}

?>
