<?php

require_once 'Swat/SwatObject.php';
require_once 'SwatDB/exceptions/SwatDBException.php';

/**
 * MDB2 Recordset Wrapper
 *
 * Used to wrap an MDB2 recordset into a traversable collection of objects.
 *
 * @package   SwatDB
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatDBRecordsetWrapper extends SwatObject implements Iterator, Serializable
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

	/**
	 * @var MDB2
	 */
	protected $db = null;

	// }}}
	// {{{ private properties

	/**
	 * An array of the objects created by this wrapper
	 *
	 * @var array
	 */
	private $objects = array();
	private $objects_by_index = array();
	private $remove_objects = array();

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
		$this->init();

		if (MDB2::isError($rs))
			throw new SwatDBException($rs->getMessage());

		if ($rs->numRows()) {
			while ($row = $rs->fetchRow(MDB2_FETCHMODE_OBJECT)) {
				if ($this->row_wrapper_class === null) {
					$object = $row;
				} else {
					$object = new $this->row_wrapper_class($row);

					if ($object instanceof SwatDBDataObject)
						$object->setDatabase($rs->db);
				}

				$this->objects[] = $object;

				if ($this->index_field !== null) {
					$index_field = $this->index_field;
					$index = $row->$index_field;
					$this->objects_by_index[$index] = $object;
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
		if (isset($this->objects_by_index[$index]))
			return $this->objects_by_index[$index];
		elseif (isset($this->objects[$index]))
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
	// {{{ public function getArray()

	/**
	 * Gets this recordset as an array of objects
	 *
	 * @return array this record set as an array.
	 */
	public function &getArray()
	{
		return $this->objects;
	}

	// }}}
	// {{{ protected function init()

	/**
	 * Initializes this recordset wrapper
	 *
	 * By default, the row wrapper class is set to null. Subclasses may change
	 * this behaviour and optionally call additional initialization methods.
	 */
	protected function init()
	{
		$this->row_wrapper_class = null;
	}

	// }}}

	// serializing
	// {{{ public function serialize()

	public function serialize()
	{
		$data = array();

		$private_properties = array('row_wrapper_class',
			'index_field', 'objects', 'objects_by_index');

		foreach ($private_properties as $property)
			$data[$property] = &$this->$property;

		return serialize($data);
	}

	// }}}
	// {{{ public function unserialize()
	
	public function unserialize($data)
	{
		$data = unserialize($data);

		foreach ($data as $property => $value)
			$this->$property = $value;
	}

	// }}}

	// manipulating of sub data objects
	// {{{ public function getInternalValues()

	/**
	 * Get values from an internal property for each dataobject in the set
	 *
	 * @param string $name name of the property to load.
	 *
	 * @return array an array of values.
	 */
	public function getInternalValues($name)
	{
		if ($this->getCount() == 0)
			return;

		if (!$this->getFirst()->hasInternalValue($name))
			throw new SwatDBException(
				"Dataobjects do not contain an internal field named '$name'.");

		$values = array();

		foreach ($this->objects as $object)
			$values[] = $object->getInternalValue($name);

		return $values;
	}

	// }}}
	// {{{ public function loadAllSubDataObjects()

	/**
	 * Load all sub-dataobjects for an internal property of the dataobjects in this recordset
	 *
	 * @param string $name name of the property to load.
	 * @param MDB2 $db database object.
	 * @param string $sql SQL to execute with placeholder for set of internal values.
	 * @param string $wrapper name of a recordset wrapper to use for sub-dataobjects.
	 *
	 * @return SwatDBRecordsetWrapper an instance of the wrapper, or null.
	 */
	public function loadAllSubDataObjects($name, $db, $sql, $wrapper, $type = 'integer')
	{
		$values = $this->getInternalValues($name);

		if (empty($values))
			return null;

		$quoted_values = array();
		foreach ($values as $value)
			$quoted_values[] = $db->quote($value, $type);

		$sql = sprintf($sql, implode(',', $quoted_values));
		$sub_data_objects = SwatDB::query($db, $sql, $wrapper);
		$this->attachSubDataObjects($name, $sub_data_objects);
		return $sub_data_objects;
	}

	// }}}
	// {{{ public function attachSubDataObjects()

	/**
	 * Attach existing sub-dataobjects for an internal property of the dataobjects in this recordset
	 *
	 * @param string $name name of the property to attach to.
	 * @param SwatDBRecordset $sub_data_objects
	 */
	public function attachSubDataObjects($name, $sub_data_objects)
	{
		foreach ($this->objects as $object) {
			$value = $object->getInternalValue($name);
			$sub_dataobject = $sub_data_objects->getByIndex($value);
			$object->$name = $sub_dataobject;
		}
	}

	// }}}

	// manipulating of objects
	// {{{ public function add()

	/**
	 * Adds an object to this recordset
	 *
	 * @param SwatDBDataObject $object the object to add. The object must be
	 *                                  an instance of the {@link
	 *                                  $row_wrapper_class}.
	 */
	public function add($object)
	{
		if ($this->row_wrapper_class !== null &&
			!($object instanceof $this->row_wrapper_class))
			throw new SwatDBException(sprintf('You can only add instances of '.
				"'%s' to %s recordset wrappers.", $this->row_wrapper_class,
				get_class($this)));

		$this->objects[] = $object;
		$object->setDatabase($this->db);
	}

	// }}}
	// {{{ public function remove()

	/**
	 * Remove an object from this recordset
	 *
	 * @param SwatDBDataObject $object
	 */
	public function remove($remove_object)
	{
		foreach ($this->objects as $key => $object) {
			if ($object === $remove_object) {
				$this->remove_objects[] = $object;
				unset($this->objects[$key]);
				$this->objects = array_values($this->objects);

				if ($this->index_field !== null) {
					$index_field = $this->index_field;
					$index = $object->$index_field;
					unset($this->objects_by_index[$index]);
				}
			}
		}
	}

	// }}}
	// {{{ public function removeByIndex()

	/**
	 * Remove an object from this recordset using its index
	 *
	 * @param integer $index
	 */
	public function removeByIndex($index)
	{
		$object = $this->getByIndex($index);

		if ($object !== null)
			$this->remove($object);
	}

	// }}}

	// database loading and saving
	// {{{ public function setDatabase()

	/**
	 * @param MDB2 $db
	 */
	public function setDatabase($db)
	{
		$this->db = $db;

		foreach ($this->objects as $object)
			if ($object instanceof SwatDBDataObject ||
				$object instanceof SwatDBRecordsetWrapper)
					$object->setDatabase($db);
	}

	// }}}
	// {{{ public function save()

	/**
	 * Saves the set to the database.
	 *
	 * Objects that were added are inserted into the database.
	 * Objects that were modified are updated in the database.
	 * Objects that were removed are deleted from the database.
	 */
	public function save() {
		foreach ($this->objects as $object) {
			$object->setDatabase($this->db);
			$object->save();
		}

		foreach ($this->remove_objects as $object) {
			$object->setDatabase($this->db);
			$object->delete();
		}
	}

	// }}}
}

?>
