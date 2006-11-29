<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatObject.php';
require_once 'Swat/SwatTableModel.php';

/**
 * A data structure that can be used with the SwatTableView
 *
 * A new table store is empty by default. Use the
 * {@link SwatTableStore::addRow()} method to add rows to a table store.
 *
 * @package   Swat
 * @copyright 2004-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTableStore extends SwatObject implements SwatTableModel
{
	// {{{ private properties

	/**
	 * The indvidual rows for this data structure
	 *
	 * @var array
	 */
	private $rows = array();

	// }}}
	// {{{ public function getRowCount()

	/**
	 * Gets the number of rows in this data structure
	 *
	 * @see SwatTableModel::getRowCount()
	 */
	public function getRowCount()
	{
		return count($this->rows);
	}

	// }}}
	// {{{ public function &getRows()

	/**
	 * Gets the rows of this data structure as an array
	 *
	 * @return array the rows of this data structure
	 *
	 * @see SwatTableModel::getRows()
	 */
	public function &getRows()
	{
		return $this->rows;
	}

	// }}}
	// {{{ public function addRow()

	/**
	 * Adds a row to this data structure
	 *
	 * @param $data the data of the row to add.
	 * @param $id an optional uniqueid of the row to add.
	 *
	 * @see SwatTableModel::addRow()
	 */
	public function addRow($data, $id = null)
	{
		if ($id === null)
			$this->rows[] = $data;
		else
			$this->rows[$id] = $data;
	}

	// }}}
}

?>
