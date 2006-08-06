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

	/**
	 * Whether or not this row is visible
	 *
	 * @param boolean
	 */
	public $visible = true;

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
	// {{{ public abstract function display()

	/**
	 * Displays this row
	 */
	public abstract function display();

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
	// {{{ public function getVisibleByCount()

	/**
	 * Gets whether or not to show this row based on a count of rows
	 *
	 * By default if there are no entries in the table model, this row is not
	 * shown.
	 *
	 * @param integer $count the number of entries in this row's view's model.
	 *
	 * @return boolean true if this row should be shown and false if it should
	 *                  not.
	 */
	public function getVisibleByCount($count)
	{
		if ($count == 0)
			return false;

		return true;
	}

	// }}}
}

?>
