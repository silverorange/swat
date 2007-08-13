<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatObject.php';
require_once 'Swat/SwatTableModel.php';
require_once 'SwatDB/SwatDBTransaction.php';
require_once 'SwatDB/SwatDBClassMap.php';
require_once 'SwatDB/SwatDBRecordable.php';
require_once 'SwatDB/exceptions/SwatDBException.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';
require_once 'Swat/exceptions/SwatInvalidTypeException.php';

/**
 * MDB2 recordset wrapper
 *
 * Used to wrap an MDB2 recordset into a traversable collection of record
 * objects. Implements SwatTableModel so it can be used directly as a data
 * model for a recordset view. See {@link SwatView}.
 *
 * Recordsets are iterable and accessible using array access notation. One
 * important point about recordsets is that <strong>iteration will always visit
 * every record in this recordset</strong>, but if an index field is defined
 * for this recordset, <strong>array access notation can only access records
 * with their index field set</strong>. This is normally not a problem but
 * inconsistencies can arise if records are added to this recordset that do not
 * have an index value.
 *
 * @package   SwatDB
 * @copyright 2005-2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @todo      Add lazy instantiation of records.
 */
abstract class SwatDBRecordsetWrapper extends SwatObject
	implements Serializable, ArrayAccess, SwatTableModel, SwatDBRecordable
{
	// {{{ protected properties

	/**
	 * The name of the row wrapper class to use for this recordset wrapper
	 *
	 * @var string
	 */
	protected $row_wrapper_class;

	/**
	 * The name of the record field to use as an index
	 *
	 * This field is used to lookup objects using getIndex(). If unspecified
	 * by a recordset subclass, the subclass records will not be indexed.
	 *
	 * @var string
	 */
	protected $index_field;

	/**
	 * The database driver to use for this recordset
	 *
	 * @var MDB2_Driver_Common
	 *
	 * @see SwatDBRecordsetWrapper::setDatabase()
	 */
	protected $db;

	// }}}
	// {{{ private properties

	/**
	 * Records contained in this recordset
	 *
	 * If this recordset wrapper has a defined $index_field, this array is
	 * indexed by the index field values of the objects. Otherwise, this array
	 * is indexed numerically.
	 *
	 * @var array
	 */
	private $objects = array();

	/**
	 * Records contained in this recordset indexed by this recordset's
	 * index field
	 *
	 * If this recordset does not have a defined index field, this array is
	 * not used.
	 *
	 * @var array
	 */
	private $objects_by_index = array();

	/**
	 * Records removed from this recordset
	 *
	 * This array contains records removed from this recordset before this
	 * recordset is saved. When this recordset is saved, all records contained
	 * in this array are deleted from the database. This array is indexed
	 * numerically.
	 *
	 * @var array
	 */
	private $removed_objects = array();

	/**
	 * The current index of the iterator interface
	 *
	 * @var integer
	 */
	private $current_index = 0;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new recordset wrapper
	 *
	 * @param MDB2_Result $recordset optional. The MDB2 recordset to wrap.
	 */
	public function __construct($recordset = null)
	{
		$this->init();

		if ($recordset === null)
			return;

		if (MDB2::isError($recordset))
			throw new SwatDBException($recordset->getMessage());

		$this->setDatabase($recordset->db);

		if ($recordset->numRows() > 0) {
			while ($row = $recordset->fetchRow(MDB2_FETCHMODE_OBJECT)) {
				$object = $this->instantiateRowWrapperObject($row);

				if ($object instanceof SwatDBRecordable)
					$object->setDatabase($recordset->db);

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
	 * @param stdClass $row the data row to use.
	 *
	 * @return stdClass the instantiated data object or the original object if
	 *                   no <i>$row_wrapper_class</i> is defined for this
	 *                   recordset wrapper.
	 */
	protected function instantiateRowWrapperObject($row)
	{
		if ($this->row_wrapper_class === null) {
			$object = $row;
		} else {
			$object = new $this->row_wrapper_class($row);
		}

		return $object;
	}

	// }}}
	// {{{ protected function init()

	/**
	 * Initializes this recordset wrapper
	 *
	 * Subclasses are encoraged to specify a SwatDBDataObject subclass as this
	 * recordset's row wrapper class. See
	 * {@link SwatDBRecordsetWrapper::$row_wrapper_class}.
	 *
	 * Subclasses are also encoraged to specify an index field here. This
	 * enables lookup of records in this recordset by the index field value.
	 * See {@link SwatDBRecordsetWrapper::$index_field}.
	 *
	 * Other initialization may be performed here. This method is the first
	 * thing called in the constructor.
	 */
	protected function init()
	{
	}

	// }}}
	// {{{ protected function checkDB()

	protected function checkDB()
	{
		if ($this->db === null)
			throw new SwatDBException(
				sprintf('No database available to this wrapper (%s). '.
					'Call the setDatabase method.', get_class($this)));
	}

	// }}}

	// array access
	// {{{ public function offsetExists()

	/**
	 * Gets whether or not a value exists for the given offset
	 *
	 * @param mixed $offset the offset to check. If this recordset has a
	 *                       defined index field, the offset is an index
	 *                       value. Otherwise, the offset is an ordinal value.
	 *
	 * @return boolean true if this recordset has a value for the given offset
	 *                  and false if it does not.
	 */
	public function offsetExists($offset)
	{
		if ($this->index_field === null)
			return isset($this->objects[$offset]);

		return isset($this->objects_by_index[$offset]);
	}

	// }}}
	// {{{ public function offsetGet()

	/**
	 * Gets a record in this recordset by an offset value
	 *
	 * @param mixed $offset the offset for which to get the record. If this
	 *                       recordset has a defined index field, the offset is
	 *                       an index value. Otherwise, the offset is an
	 *                       ordinal value.
	 *
	 * @return SwatDBDataObject the record at the specified offset.
	 *
	 * @throws OutOfBoundsException if no record exists at the specified offset
	 *                               in this recordset.
	 */
	public function offsetGet($offset)
	{
		if (!isset($this[$offset]))
			throw new OutOfBoundsException(sprintf(
				'Index %s is out of bounds.',
				$offset));

		if ($this->index_field === null)
			return $this->objects[$offset];

		return $this->objects_by_index[$offset];
	}

	// }}}
	// {{{ public function offsetSet()

	/**
	 * Sets a record at a specified offset
	 *
	 * @param mixed $offset optional. The offset to set the record at. If this
	 *                       recordset has a defined index field, the offset is
	 *                       an index value. Otherwise, the offset is an
	 *                       ordinal value. If no offset is given, the record
	 *                       will be added at the end of this recordset.
	 *
	 * @param mixed $value the record to add.
	 *
	 * @throws UnexpectedValueException if this recordset has a defined row
	 *                                  wrapper class and the specified value
	 *                                  is not an instance of the row wrapper
	 *                                  class.
	 * @throws OutOfBoundsException if the specified offset does not exist in
	 *                              this recordset. Records can only be added
	 *                              to the end of the recordset or replace
	 *                              existing records in this recordset.
	 */
	public function offsetSet($offset, $value)
	{
		if ($this->row_wrapper_class !== null &&
			!($value instanceof $this->row_wrapper_class))
			throw new UnexpectedValueException(sprintf(
				'Value should be an instance of %s.',
				$this->row_wrapper_class));

		// add
		if ($offset === null) {
			$this->objects[] = $value;

			// if index field is set, index the object
			if ($this->index_field !== null &&
				isset($value->{$this->index_field}))
				$this->objects_by_index[$value->{$this->index_field}] =
					$value;

		// replace at offset
		} else {
			if (!isset($this[$offset]))
				throw new OutOfBoundsException(sprintf(
					'No record to replace exists at offset %s.',
					$offset));

			if ($this->index_field === null) {
				$this->removed_objects[] = $this->objects[$offset];
				$this->objects[$offset] = $value;
			} else {
				// update object index field value
				$value->{$this->index_field} = $offset;

				// find and replace ordinally indexed objects
				$keys = array_keys($this->objects, $value, true);
				foreach ($keys as $key) {
					$this->removed_objects[] = $this->objects[$key];
					$this->objects[$key] = $value;
				}

				// add object to indexed array
				$this->objects_by_index[$offset] = $value;
			}
		}

		// only set the db on added object if it is set for this recordset
		if ($this->db !== null && $value instanceof SwatDBRecordable)
			$value->setDatabase($this->db);

		// Remove object from removed list if it was on list of removed
		// objects. This should happen after adding the new object above
		// in case we replaced the same object.
		$keys = array_keys($this->removed_objects, $value, true);
		foreach ($keys as $key)
			unset($this->removed_objects[$key]);
	}

	// }}}
	// {{{ public function offsetUnset()

	/**
	 * Unsets a record in this recordset at the specified offset
	 *
	 * This removes the record at the specified offset from this recordset.
	 * If no such record exists, nothing is done. The record object itself
	 * still exists if there is an external reference to it elsewhere.
	 *
	 * @param mixed $offset the offset for which to unset the record. If this
	 *                       recordset has a defined index field, the offset is
	 *                       an index value. Otherwise, the offset is an
	 *                       ordinal value.
	 */
	public function offsetUnset($offset)
	{
		if (isset($this[$offset])) {
			if ($this->index_field === null) {
				$this->removed_objects[] = $this->objects[$offset];
				unset($this->objects[$offset]);

				// update iterator index
				if ($this->current_index >= $offset && $this->current_index > 0)
					$this->current_index--;

			} else {
				$object = $this->objects_by_index[$offset];
				$this->removed_objects[] = $object;
				unset($this->objects_by_index[$offset]);

				$keys = array_keys($this->objects, $object, true);
				foreach ($keys as $key) {
					unset($this->objects[$key]);

					// update iterator index
					if ($this->current_index >= $key &&
						$this->current_index > 0)
						$this->current_index--;
				}
			}

			// reindex ordinal array of records
			$this->objects = array_values($this->objects);
		}
	}

	// }}}

	// iteration
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
	 * Returns the key of the current record
	 *
	 * If this recordset has an index field defined and the current record has
	 * an index value, this gets the index value. Otherwise this gets the
	 * ordinal position of the record in this recordset.
	 *
	 * @return integer the key of the current record.
	 */
	public function key()
	{
		if ($this->index_field !== null &&
			isset($this->current()->{$this->index_field}))
			$key = $this->current()->{$this->index_field};
		else
			$key = $this->current_index;
		
		return $key;
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
		return array_key_exists($this->current_index, $this->objects);
	}

	// }}}

	// counting
	// {{{ public function getCount()

	/**
	 * Gets the number of records in this recordset
	 *
	 * @return integer the number of records in this recordset.
	 *
	 * @deprecated this class now implements Countable. Use count($object)
	 *              instead of $object->getCount().
	 */
	public function getCount()
	{
		return count($this);
	}

	// }}}
	// {{{ public function count()

	/**
	 * Gets the number of records in this recordset
	 *
	 * This satisfies the Countable interface.
	 *
	 * @return integer the number of records in this recordset.
	 */
	public function count()
	{
		return count($this->objects);
	}

	// }}}

	// serialization
	// {{{ public function serialize()

	public function serialize()
	{
		$data = array();

		$private_properties = array(
			'row_wrapper_class',
			'index_field',
			'objects',
			'objects_by_index',
		);

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
		$values = array_filter($values,
			create_function('$value', 'return $value !== null;'));

		if (empty($values))
			return null;

		$this->checkDB();
		$this->db->loadModule('Datatype', null, true);
		$quoted_values = $this->db->datatype->implodeArray($values, $type);

		$sql = sprintf($sql, $quoted_values);
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
			if (isset($sub_data_objects[$value]))
				$object->$name = $sub_data_objects[$value];
		}
	}

	// }}}

	// manipulating of objects
	// {{{ public function getArray()

	/**
	 * Gets this recordset as an array of objects
	 *
	 * @return array this record set as an array. This gets a copy of the
	 *                internal object array (indexed ordinally).
	 */
	public function getArray()
	{
		return $this->objects;
	}

	// }}}
	// {{{ public function getFirst()

	/**
	 * Retrieves the first object in this recordset
	 *
	 * @return mixed the first object or null if there are no objects in this
	 *                recordset.
	 */
	public function getFirst()
	{
		$first = null;

		if (count($this->objects) > 0)
			$first = reset($this->objects);

		return $first;
	}

	// }}}
	// {{{ public function getByIndex()

	/**
	 * Retrieves a record in this recordset by index
	 *
	 * By default indexes are ordinal numbers unless this class's
	 * $index_field property is set.
	 *
	 * You can use also get records using array acces notation. For example:
	 * <code>
	 * $value = (isset($set['index'])) ? $set['index'] : null;
	 * </code>
	 *
	 * @param mixed $index the offset for which to get the record. If this
	 *                      recordset has a defined index field, the offset is
	 *                      an index value. Otherwise, the offset is an
	 *                      ordinal value.
	 *
	 * @return mixed the record object or null if not found.
	 */
	public function getByIndex($index)
	{
		return (isset($this[$index])) ? $this[$index] : null;
	}

	// }}}
	// {{{ public function add()

	/**
	 * Adds a record to this recordset
	 *
	 * You can also add records to this recordset using array access notation.
	 * For example:
	 * <code>
	 * $set[] = $new_record;
	 * </code>
	 *
	 * @param SwatDBDataObject $object the object to add. If this recordset has
	 *                                  a row wrapper class defined, the object
	 *                                  must be an instance of that class.
	 */
	public function add(SwatDBDataObject $object)
	{
		$this[] = $object;
	}

	// }}}
	// {{{ public function remove()

	/**
	 * Removes a record from this recordset
	 *
	 * @param SwatDBDataObject $remove_object the record to remove.
	 */
	public function remove(SwatDBDataObject $remove_object)
	{
		if (in_array($remove_object, $this->objects, true)) {
			$this->removed_objects[] = $remove_object;

			if ($this->index_field !== null) {
				$index = $remove_object->{$this->index_field};
				unset($this->objects_by_index[$index]);
			}

			$keys = array_keys($this->objects, $remove_object, true);
			foreach ($keys as $key) {
				unset($this->objects[$key]);

				// update iterator index
				if ($this->current_index >= $key && $this->current_index > 0)
					$this->current_index--;
			}

			// reindex ordinal array of records
			$this->objects = array_values($this->objects);
		}
	}

	// }}}
	// {{{ public function removeByIndex()

	/**
	 * Removes a record from this recordset given the record's index value
	 *
	 * You can also remove records from this recordset using array access
	 * notation. For example:
	 * <code>
	 * unset($set[$index]);
	 * </code>
	 *
	 * @param mixed $index the offset of the record to remove. If this
	 *                      recordset has a defined index field, the offset is
	 *                      an index value. Otherwise, the offset is an
	 *                      ordinal value.
	 */
	public function removeByIndex($index)
	{
		unset($this[$index]);
	}

	// }}}
	// {{{ public function removeAll()

	/**
	 * Removes all records from this recordset
	 */
	public function removeAll()
	{
		$this->removed_objects = array_values($this->objects);
		$this->objects = array();
		$this->objects_by_index = array();
		$this->current_index = 0;
	}

	// }}}
	// {{{ public function reindex()

	/**
	 * Reindexes this recordset
	 *
	 * Reindexing is useful when you have added new data-objects to this
	 * recordset. Reindexing is only done if this recordset has a defined
	 * index field.
	 */
	public function reindex()
	{
		if ($this->index_field !== null) {
			$this->objects_by_index = array();
			$index_field = $this->index_field;
			foreach ($this->objects as $object)
				if (isset($object->$index_field))
					$this->objects_by_index[$object->$index_field] = $object;
		}
	}

	// }}}

	// database loading and saving
	// {{{ public function setDatabase()

	/**
	 * Sets the database driver for this recordset
	 *
	 * The database is automatically set for all recordable records of this
	 * recordset.
	 *
	 * @param MDB2_Driver_Common $db the database driver to use for this
	 *                                recordset.
	 */
	public function setDatabase(MDB2_Driver_Common $db)
	{
		$this->db = $db;

		foreach ($this->objects as $object)
			if ($object instanceof SwatDBRecordable)
				$object->setDatabase($db);
	}

	// }}}
	// {{{ public function save()

	/**
	 * Saves this recordset to the database
	 *
	 * Saving a recordset works as follows:
	 *  1. Objects that were added are inserted into the database,
	 *  2. Objects that were modified are updated in the database,
	 *  3. Objects that were removed are deleted from the database.
	 */
	public function save()
	{
		$this->checkDB();
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

			$transaction->commit();
		} catch (Exception $e) {
			$transaction->rollback();
			throw $e;
		}

		$this->removed_objects = array();
		$this->reindex();
	}

	// }}}
	// {{{ public function load()

	/**
	 * Loads a set of records into this recordset
	 *
	 * It is recommended for performance that you use recordset wrappers to
	 * wrap a MDB2 result set rather than using this load() method. Using this
	 * method performs N queries where N is the size of the passed array of
	 * object indexes.
	 *
	 * @param array $object_indexes the index field values of the records to
	 *                               load into this recordset.
	 *
	 * @return boolean true if all records loaded properly and false if one
	 *                  or more records could not be loaded. If any records
	 *                  fail to load, the recordset state remains unchanged.
	 *
	 * @throws SwatInvalidTypeException if the <i>$object_indexes</i> property
	 *                                   is not an array.
	 * @throws SwatInvalidClassException if this recordset's
	 *                                    {@link SwatDBRecordsetWrapper::$row_wrapper_class}
	 *                                    is not an instance of
	 *                                    {@link SwatDBRecordable}.
	 */
	public function load($object_indexes)
	{
		if (!is_array($object_indexes))
			throw new SwatInvalidTypeException(
				'The $object_indexes property must be an array.',
				0, $object_indexes);

		if (!($this->row_wrapper_class instanceof SwatDBRecordable))
			throw new SwatInvalidClassException(
				'The recordset must define a row wrapper class that is an '.
				'instance of SwatDBRecordable for recordset loading to work.',
				0, $this->row_wrapper_class);

		$success = true;

		// try to load all records
		$records = array();
		foreach ($object_indexes as $index) {
			$record = new $this->row_wrapper_class();
			if ($record->load($index)) {
				$records[] = $record;
			} else {
				$success = false;
				break;
			}
		}

		// successfully loaded all records, set this set's records to the
		// loaded records
		if ($success) {
			$this->objects = array();
			$this->objects_by_index = array();
			$this->removed_objects = array();

			foreach($records as $record)
				$this[] = $record;

			$this->reindex();
		}

		return $success;
	}

	// }}}
	// {{{ public function delete()

	/**
	 * Deletes this recordset from the database
	 *
	 * All records contained in this recordset are removed from this set and
	 * are deleted from the database.
	 */
	public function delete()
	{
		$this->removeAll();
		$this->save();
	}

	// }}}
	// {{{ public function isModified()

	/**
	 * Returns true if this recordset has been modified since it was loaded
	 *
	 * A recordset is considered modified if any of the contained records have
	 * been modified or if any records have been removed from this set. Adding
	 * an unmodified record to this set does not constitute modifying the set.
	 *
	 * @return boolean true if this recordset was modified and false if this
	 *                  recordset was not modified.
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
}

?>
