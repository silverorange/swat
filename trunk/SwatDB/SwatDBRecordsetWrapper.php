<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatObject.php';
require_once 'SwatDB/SwatDBTransaction.php';
require_once 'SwatDB/SwatDBClassMap.php';
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
abstract class SwatDBRecordsetWrapper extends SwatObject
	implements Iterator, Serializable, Countable
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

	/**
	 * A class-mapping object
	 *
	 * @var SwatDBClassMap
	 */
	protected $class_map;

	// }}}
	// {{{ private properties

	/**
	 * An array of the objects created by this wrapper
	 *
	 * @var array
	 */
	private $objects = array();
	private $objects_by_index = array();
	private $removed_objects = array();

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
	public function __construct($rs = null)
	{
		$this->class_map = SwatDBClassMap::instance();

		$this->init();

		if ($rs === null)
			return;

		if (MDB2::isError($rs))
			throw new SwatDBException($rs->getMessage());

		if ($rs->numRows()) {
			while ($row = $rs->fetchRow(MDB2_FETCHMODE_OBJECT)) {
				if ($this->row_wrapper_class === null) {
					$object = $row;
				} else {
					$object = $this->instantiateRowWrapperObject($row);

					if ($object instanceof SwatDBDataObject)
						$object->setDatabase($rs->db);
				}

				$this->objects[] = $object;

				if ($this->index_field !== null &&
					isset($row->{$this->index_field})) {
					$index = $row->{$this->index_field};
					$this->objects_by_index[$index] = $object;
				}
			}
		}
	}

	// }}}
	// {{{ protected function instantiateRowWrapperObject()

	/**
	 * Creates a new dataobject
	 *
	 * @param $row data row to use.
	 */
	protected function instantiateRowWrapperObject($row)
	{
		return new $this->row_wrapper_class($row);
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
	 * @return integer the number of objects in this record-set.
	 *
	 * @deprecated this class now implements Countable. Use count($object)
	 *              instead of $object->getCount().
	 */
	public function getCount()
	{
		return count($this->objects);
	}

	// }}}
	// {{{ public function count()

	/**
	 * Gets the number of objects
	 *
	 * This satisfies the Countable interface.
	 *
	 * @return integer the number of objects in this record-set.
	 */
	public function count()
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
		if (count($this) == 0)
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
	 * Loads all sub-data-objects for an internal property of the data-objects
	 * in this recordset
	 *
	 * @param string $name name of the property to load.
	 * @param MDB2_Driver_Common $db database object.
	 * @param string $sql SQL to execute with placeholder for set of internal
	 *                     values.
	 * @param string $wrapper name of a recordset wrapper to use for
	 *                         sub-data-objects.
	 *
	 * @return SwatDBRecordsetWrapper an instance of the wrapper, or null.
	 */
	public function loadAllSubDataObjects($name, MDB2_Driver_Common $db, $sql,
		$wrapper, $type = 'integer')
	{
		$values = $this->getInternalValues($name);

		if (empty($values))
			return null;

		$quoted_values = array();
		foreach ($values as $value)
			if ($value !== null)
				$quoted_values[] = $db->quote($value, $type);

		if (empty($quoted_values))
			return null;

		$sql = sprintf($sql, implode(',', $quoted_values));
		$sub_data_objects = SwatDB::query($db, $sql, $wrapper);
		$this->attachSubDataObjects($name, $sub_data_objects);
		return $sub_data_objects;
	}

	// }}}
	// {{{ public function attachSubDataObjects()

	/**
	 * Attach existing sub-dataobjects for an internal property of the
	 * dataobjects in this recordset
	 *
	 * @param string $name name of the property to attach to.
	 * @param SwatDBRecordsetWrapper $sub_data_objects
	 */
	public function attachSubDataObjects($name,
		SwatDBRecordsetWrapper $sub_data_objects)
	{
		foreach ($this->objects as $object) {
			$value = $object->getInternalValue($name);
			$sub_dataobject = $sub_data_objects->getByIndex($value);
			$object->$name = $sub_dataobject;
		}
	}

	// }}}
	// {{{ public function isModified()

	/**
	 * Returns true if this object has been modified since it was loaded
	 *
	 * @return boolean true if this object was modified and false if this
	 *                  object was not modified.
	 */
	public function isModified()
	{
		if (count($this->removed_objects) > 0)
			return true;

		foreach ($this->objects as $name => $object)
			if ($object->isModified())
				return true;		

		return false;
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
	public function add(SwatDBDataObject $object)
	{
		if ($this->row_wrapper_class !== null &&
			!($object instanceof $this->row_wrapper_class))
			throw new SwatDBException(sprintf('You can only add instances of '.
				"'%s' to %s recordset wrappers.", $this->row_wrapper_class,
				get_class($this)));

		$this->objects[] = $object;

		// only set the db on added object if it is set for this recordset
		if ($this->db !== null)
			$object->setDatabase($this->db);

		// if index field is set, index this object
		if ($this->index_field !== null &&
			isset($object->{$this->index_field})) {
			$this->objects_by_index[$object->{$this->index_field}] = $object;
		}
	}

	// }}}
	// {{{ public function remove()

	/**
	 * Remove an object from this recordset
	 *
	 * @param SwatDBDataObject $object
	 */
	public function remove(SwatDBDataObject $remove_object)
	{
		foreach ($this->objects as $key => $object) {
			if ($object === $remove_object) {
				$this->removed_objects[] = $object;
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
	// {{{ public function reindex()

	/**
	 * Reindexes this recordset
	 *
	 * Reindexing is useful when you have added new data-objects to this
	 * recordset. Reindexing is only done if
	 * {@link SwatDBRecordsetWrapper::$index_field} is not null.
	 */
	public function reindex()
	{
		if ($this->index_field !== null) {
			foreach ($this->objects as $object) {
				if (isset($object->{$this->index_field})) {
					$this->objects_by_index[$object->{$this->index_field}] =
						$object;
				}
			}
		}
	}

	// }}}

	// database loading and saving
	// {{{ public function setDatabase()

	/**
	 * @param MDB2_Driver_Common $db
	 */
	public function setDatabase(MDB2_Driver_Common $db)
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
	public function save()
	{
		$transaction = new SwatDBTransaction($this->db);
		try {
			foreach ($this->objects as $object) {
				$object->setDatabase($this->db);
				$object->save();
			}

			foreach ($this->removed_objects as $object) {
				$object->setDatabase($this->db);
				$object->delete();
			}
		} catch (Exception $e) {
			$transaction->rollback();
			throw $e;
		}
		$transaction->commit();

		$this->removed_objects = array();
		$this->reindex();
	}

	// }}}
}

?>
