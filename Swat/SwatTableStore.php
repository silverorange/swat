<?php

/**
 * A data structure that can be used with the SwatTableView
 *
 * A new table store is empty by default. Use the
 * {@link SwatTableStore::add()} method to add rows to a table store.
 *
 * @package   Swat
 * @copyright 2004-2016 silverorange
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

	/**
	 * The current index of the iterator interface
	 *
	 * @var integer
	 */
	private $current_index = 0;

	// }}}
	// {{{ public function count()

	/**
	 * Gets the number of rows
	 *
	 * This satisfies the Countable interface.
	 *
	 * @return integer the number of rows in this data structure.
	 */
	public function count()
	{
		return count($this->rows);
	}

	// }}}
	// {{{ public function current()

	/**
	 * Returns the current element
	 *
	 * @return mixed the current element.
	 */
	public function current()
	{
		return $this->rows[$this->current_index];
	}

	// }}}
	// {{{ public function key()

	/**
	 * Returns the key of the current element
	 *
	 * @return integer the key of the current element
	 */
	public function key()
	{
		return $this->current_index;
	}

	// }}}
	// {{{ public function next()

	/**
	 * Moves forward to the next element
	 */
	public function next()
	{
		$this->current_index++;
	}

	// }}}
	// {{{ public function prev()

	/**
	 * Moves forward to the previous element
	 */
	public function prev()
	{
		$this->current_index--;
	}

	// }}}
	// {{{ public function rewind()

	/**
	 * Rewinds this iterator to the first element
	 */
	public function rewind()
	{
		$this->current_index = 0;
	}

	// }}}
	// {{{ public function valid()

	/**
	 * Checks is there is a current element after calls to rewind() and next()
	 *
	 * @return boolean true if there is a current element and false if there
	 *                  is not.
	 */
	public function valid()
	{
		return array_key_exists($this->current_index, $this->rows);
	}

	// }}}
	// {{{ public function add()

	/**
	 * Adds a row to this data structure
	 *
	 * @param $data the data of the row to add.
	 *
	 */
	public function add($data)
	{
		$this->rows[] = $data;
	}

	// }}}
	// {{{ public function addToStart()

	/**
	 * Adds a row to the beginning of this data structure
	 *
	 * @param $data the data of the row to add.
	 *
	 */
	public function addToStart($data)
	{
		array_unshift($this->rows, $data);
		$this->current_index++;
	}

	// }}}
	// {{{ public function getRowCount()

	/**
	 * Gets the number of rows in this data structure
	 *
	 * @deprecated Use Countable::count()
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
	 * @deprecated Use as an Iterator
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
	 * @deprecated Use SwatTableStore::add()
	 */
	public function addRow($data, $id = null)
	{
		$this->add($data);
	}

	// }}}
}

?>
