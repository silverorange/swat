<?php

/**
 * MDB2 Recordset Wrapper
 *
 * Used to wrap an MDB2 recordset into a traversable collection of objects.
 *
 * @package   SwatDB
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatDBRecordsetWrapper implements Iterator
{
	// {{{ protected properties

	/**
	 * The name of the row wrapper class to use for this recordset wrapper
	 *
	 * @var string
	 */
	protected $row_wrapper_class;

	/**
	 * The name of a field to use as an index 
	 *
	 * This field is used to lookup objects using getIndex().
	 *
	 * @var string
	 */
	protected $index_field = null;

	// }}}
	// {{{ private properties

	/**
	 * An array of the objects created by this wrapper
	 *
	 * @var array
	 */
	private $objects = array();

	/**
	 * The current index of the iterator interface
	 *
	 * @var integer
	 */
	private $current_index = 0;
	
	// }}}
	// {{{ public function __construct

	/**
	 * Creates a new wrapper object
	 *
	 * @param resource a MDB2 recordset.
	 */
	public function __construct($rs)
	{
		if (MDB2::isError($rs))
			throw new Exception($rs->getMessage());

		if ($rs->numRows()) {
			while ($row = $rs->fetchRow(MDB2_FETCHMODE_OBJECT)) {
				if ($this->row_wrapper_class === null) {
					$object = $row;
				} else {
					$object = new $this->row_wrapper_class($row);

					if ($object instanceof SwatDBDataObject)
						$object->setDatabase($rs->db);
				}

				if ($this->index_field === null) {
					$this->objects[] = $object;
				} else {
					$index_field = $this->index_field;
					$index = $row->$index_field;
					$this->objects[$index] = $object;
				}
			}
		}
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
		return $this->objects[$this->current_index];
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
		return isset($this->objects[$this->current_index]);
	}

	// }}}
	// {{{ public function getFirst()

	/**
	 * Retrieves the first object
	 *
	 * @return mixed the first object or null if there are none.
	 */
	public function getFirst()
	{
		$first = null;

		if (count($this->objects) > 0)
			$first = $this->objects[0];

		return $first;
	}

	// }}}
	// {{{ public function getByIndex()

	/**
	 * Retrieves an object by index
	 *
	 * By default indexes are ordinal numbers unless the class property
	 * $index_field is set.
	 *
	 * @return mixed the object or null if not found.
	 */
	public function getByIndex($index)
	{
		if (isset($this->objects[$index]))
			return $this->objects[$index];

		return null;
	}

	// }}}
	// {{{ public function getCount()

	/**
	 * Gets the number of objects
	 *
	 * @return mixed the number of objects in the recordset.
	 */
	public function getCount()
	{
		return count($this->objects);
	}

	// }}}
}

?>
