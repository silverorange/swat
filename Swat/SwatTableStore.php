<?php

require_once 'Swat/SwatObject.php';
require_once 'Swat/SwatTableModel.php';

/**
 * A data structure that can be used with the SwatTableView
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTableStore extends SwatObject implements SwatTableModel
{
	/**
	 * The indvidual rows for this data structure
	 *
	 * @var array
	 */
	private $rows = array();

	public function __construct()
	{
	}

	/**
	 * Gets the number of rows in this data structure
	 *
	 * @see SwatTableModel::getRowCount()
	 */
	public function getRowCount()
	{
		return count($this->rows);
	}

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
}

?>
