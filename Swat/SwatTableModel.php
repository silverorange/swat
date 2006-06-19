<?php

require_once 'Swat/SwatObject.php';

/**
 * The data model for a SwatTableView
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
interface SwatTableModel
{
	// {{{ public function &getRows()

	/**
	 * Gets all rows of this tabel model as an array
	 *
	 * @return array the rows of this table model.
	 */
	public function &getRows();

	// }}}
	// {{{ public function getRowCount()

	/**
	 * Gets the number of rows in this table model
	 *
	 * @return integer the number of rows in this table model.
	 */
	public function getRowCount();

	// }}}
	// {{{ public function addRow()

	/**
	 * Adds a row to this table data model
	 *
	 * @param mixed $data the data for the row to add.
	 * @param string $id a unique id for this row.
	 */
	public function addRow($data, $id = null);

	// }}}
}

?>
