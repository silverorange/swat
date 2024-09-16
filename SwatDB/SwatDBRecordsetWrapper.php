<?php

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
 * @copyright 2005-2024 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatDBRecordsetWrapper extends SwatObject implements
    Serializable,
    ArrayAccess,
    SwatTableModel,
    SwatDBRecordable,
    SwatDBMarshallable,
    SwatDBFlushable
{


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

    /**
     * @var array
     *
     * @see SwatDBRecordsetWrapper::setOptions()
     */
    protected $options = [];



    /**
     * Records contained in this recordset
     *
     * This array is indexed numerically.
     *
     * @var array
     */
    private $objects = [];

    /**
     * Records contained in this recordset indexed by this recordset's
     * index field
     *
     * If this recordset does not have a defined index field, this array is
     * not used.
     *
     * @var array
     */
    private $objects_by_index = [];

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
    private $removed_objects = [];

    /**
     * The current index of the iterator interface
     *
     * @var integer
     */
    private $current_index = 0;



    /**
     * Creates a new recordset wrapper
     *
     * @param MDB2_Result_Common $rs      optional. The MDB2 result set to
     *                                    wrap.
     * @param array              $options optional. An array of options for
     *                                    this recordset.
     */
    public function __construct(
        MDB2_Result_Common $rs = null,
        array $options = [],
    ) {
        $this->init();
        $this->setOptions($options);

        if ($rs instanceof MDB2_Result_Common) {
            $this->initializeFromResultSet($rs);
        }
    }



    public function initializeFromResultSet(MDB2_Result_Common $rs)
    {
        if (MDB2::isError($rs)) {
            throw new SwatDBException($rs->getMessage());
        }

        $this->objects = [];
        $this->objects_by_index = [];

        $this->setDatabase($rs->db);

        do {
            $row = $rs->fetchRow(MDB2_FETCHMODE_OBJECT);
            while ($row) {
                $object = $this->instantiateRowWrapperObject($row);

                if ($object instanceof SwatDBRecordable) {
                    $object->setDatabase($rs->db);
                }

                $this->objects[] = $object;

                if (
                    $this->index_field !== null &&
                    isset($row->{$this->index_field})
                ) {
                    $index = $row->{$this->index_field};
                    $this->objects_by_index[$index] = $object;
                }

                $row = $rs->fetchRow(MDB2_FETCHMODE_OBJECT);
            }
        } while ($rs->nextResult());
    }



    /**
     * Duplicates this record set wrapper
     *
     * @return SwatDBRecordsetWrapper a duplicate of this object.
     * @see SwatDBDataobject::duplicate()
     */
    public function duplicate()
    {
        $class = get_class($this);
        $new_wrapper = new $class();

        foreach ($this->getArray() as $object) {
            $object->setDatabase($this->db);
            $duplicate_object = $object->duplicate();
            $duplicate_object->setDatabase($this->db);
            $new_wrapper->add($duplicate_object);
        }

        $new_wrapper->setDatabase($this->db);

        return $new_wrapper;
    }



    /**
     * Sets one or more options for this recordset wrapper
     *
     * Subclasses may define additional options. The default options are:
     *
     * - <kbd>boolean read_only</kbd> if true, records are initialized as
     *                                read only. Defaults to false.
     *
     * @param array|string $options either an array containing key-value pairs
     *                              or a string cotnaining the option name to
     *                              set.
     * @param mixed        $value   optional. If <kbd>$options</kbd> was passed
     *                              as a string, this is the option value.
     *
     * @return SwatDBRecordsetWrapper the current object for fluent interface.
     */
    public function setOptions($options, $value = null)
    {
        // if options passed as string then second param is option value
        if (is_string($options)) {
            $options = [$options => $value];
        }

        if (!is_array($options)) {
            throw new InvalidArgumentException(
                'The $options parameter must either be an array or a string ' .
                    'containing the option name.',
            );
        }

        // new options override existing options
        $this->options = array_merge($options, $this->options);

        return $this;
    }



    /**
     * Gets an option value or a default value if the option is not set
     *
     * @param string $name    the option name.
     * @param mixed  $default the default value to return if the option is
     *                        not set for this recordset wrapper.
     *
     * @return mixed the option value or the default value if the option is
     *               not set.
     */
    public function getOption($name, $default = null)
    {
        $value = $default;

        if (isset($this->options[$name])) {
            $value = $this->options[$name];
        }

        return $value;
    }



    /**
     * Creates a new empty copy of this recordset wrapper
     *
     * @return SwatDBRecordsetWrapper a new empty copy of this wrapper.
     */
    public function copyEmpty()
    {
        $class_name = get_class($this);
        $wrapper = new $class_name();

        $wrapper->row_wrapper_class = $this->row_wrapper_class;
        $wrapper->index_field = $this->index_field;
        $wrapper->options = $this->options;

        return $wrapper;
    }



    /**
     * Creates a new dataobject
     *
     * @param stdClass $row the data row to use.
     *
     * @return mixed the instantiated data object or the original object if
     *               no <i>$row_wrapper_class</i> is defined for this
     *               recordset wrapper.
     */
    protected function instantiateRowWrapperObject($row)
    {
        if ($this->row_wrapper_class === null) {
            $object = $row;
        } else {
            $object = new $this->row_wrapper_class(
                $row,
                $this->getOption('read_only'),
            );
        }

        return $object;
    }



    /**
     * Initializes this recordset wrapper
     *
     * Subclasses are encouraged to specify a SwatDBDataObject subclass as this
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



    protected function checkDB()
    {
        if ($this->db === null) {
            throw new SwatDBNoDatabaseException(
                sprintf(
                    'No database available to this wrapper (%s). ' .
                        'Call the setDatabase method.',
                    get_class($this),
                ),
            );
        }
    }


    // array access


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
    public function offsetExists($offset): bool
    {
        if ($this->index_field === null) {
            return isset($this->objects[$offset]);
        }

        return isset($this->objects_by_index[$offset]);
    }



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
    public function offsetGet($offset): mixed
    {
        if (!isset($this[$offset])) {
            throw new OutOfBoundsException(
                sprintf('Index %s is out of bounds.', $offset),
            );
        }

        if ($this->index_field === null) {
            return $this->objects[$offset];
        }

        return $this->objects_by_index[$offset];
    }



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
    public function offsetSet($offset, $value): void
    {
        if (
            $this->row_wrapper_class !== null &&
            !($value instanceof $this->row_wrapper_class)
        ) {
            throw new UnexpectedValueException(
                sprintf(
                    'Value should be an instance of %s.',
                    $this->row_wrapper_class,
                ),
            );
        }

        if ($offset === null) {
            // add
            $this->objects[] = $value;

            // if index field is set, index the object
            if (
                $this->index_field !== null &&
                isset($value->{$this->index_field})
            ) {
                if ($value->hasInternalValue($this->index_field)) {
                    $index = $value->getInternalValue($this->index_field);
                } else {
                    $index = $value->{$this->index_field};
                }
                $this->objects_by_index[$index] = $value;
            }
        } else {
            // replace at offset
            if (!isset($this[$offset])) {
                throw new OutOfBoundsException(
                    sprintf(
                        'No record to replace exists at offset %s.',
                        $offset,
                    ),
                );
            }

            if ($this->index_field === null) {
                $this->removed_objects[] = $this->objects[$offset];
                $this->objects[$offset] = $value;
            } else {
                $index_field = $this->index_field;

                // update object index field value
                $value->$index_field = $offset;

                // find and replace ordinally indexed objects
                foreach ($this->objects as $key => $object) {
                    if ($object->$index_field === $value->$index_field) {
                        $this->removed_objects[] = $object;
                        $this->objects[$key] = $value;
                    }
                }

                // add object to indexed array
                $this->objects_by_index[$offset] = $value;
            }
        }

        // only set the db on added object if it is set for this recordset
        if ($this->db !== null && $value instanceof SwatDBRecordable) {
            $value->setDatabase($this->db);
        }

        // Remove object from removed list if it was on list of removed
        // objects. This step needs to happen after adding the new record
        // for the case where we replaced an object with itself.
        $keys = array_keys($this->removed_objects, $value, true);
        foreach ($keys as $key) {
            unset($this->removed_objects[$key]);
        }
    }



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
    public function offsetUnset($offset): void
    {
        if (isset($this[$offset])) {
            if ($this->index_field === null) {
                $this->removed_objects[] = $this->objects[$offset];
                unset($this->objects[$offset]);

                // update iterator index
                if (
                    $this->current_index >= $offset &&
                    $this->current_index > 0
                ) {
                    $this->current_index--;
                }
            } else {
                $object = $this->objects_by_index[$offset];
                $this->removed_objects[] = $object;
                unset($this->objects_by_index[$offset]);

                $keys = array_keys($this->objects, $object, true);
                foreach ($keys as $key) {
                    unset($this->objects[$key]);

                    // update iterator index
                    if (
                        $this->current_index >= $key &&
                        $this->current_index > 0
                    ) {
                        $this->current_index--;
                    }
                }
            }

            // reindex ordinal array of records
            $this->objects = array_values($this->objects);
        }
    }


    // iteration


    /**
     * Returns the current element
     *
     * @return mixed the current element.
     */
    public function current(): mixed
    {
        return $this->objects[$this->current_index];
    }



    /**
     * Returns the key of the current record
     *
     * If this recordset has an index field defined and the current record has
     * an index value, this gets the index value. Otherwise this gets the
     * ordinal position of the record in this recordset.
     *
     * @return int the key of the current record.
     */
    public function key(): int
    {
        if (
            $this->index_field !== null &&
            isset($this->current()->{$this->index_field})
        ) {
            $key = $this->current()->{$this->index_field};
        } else {
            $key = $this->current_index;
        }

        return $key;
    }



    /**
     * Moves forward to the next element
     */
    public function next(): void
    {
        $this->current_index++;
    }



    /**
     * Rewinds this iterator to the first element
     */
    public function rewind(): void
    {
        $this->current_index = 0;
    }



    /**
     * Checks is there is a current element after calls to rewind() and next()
     *
     * @return boolean true if there is a current element and false if there
     *                  is not.
     */
    public function valid(): bool
    {
        return array_key_exists($this->current_index, $this->objects);
    }


    // counting


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



    /**
     * Gets the number of records in this recordset
     *
     * This satisfies the Countable interface.
     *
     * @return integer the number of records in this recordset.
     */
    public function count(): int
    {
        return count($this->objects);
    }


    // serialization


    public function serialize(): string
    {
        return serialize($this->__serialize());
    }



    public function unserialize(string $data): void
    {
        $this->__unserialize(unserialize($data));
    }



    public function __serialize(): array
    {
        $data = [];

        $private_properties = [
            'row_wrapper_class',
            'index_field',
            'objects',
            'objects_by_index',
            'options',
        ];

        foreach ($private_properties as $property) {
            $data[$property] = $this->$property;
        }

        return $data;
    }



    public function __unserialize(array $data): void
    {
        foreach ($data as $property => $value) {
            $this->$property = $value;
        }
    }



    public function marshall(array $tree = [])
    {
        $data = [];

        $private_properties = ['row_wrapper_class', 'index_field', 'options'];

        foreach ($private_properties as $property) {
            $data[$property] = $this->$property;
        }

        $data['objects'] = [];

        foreach ($this->objects as $object) {
            if ($object instanceof SwatDBMarshallable) {
                $object_data = $object->marshall($tree);
                $data['objects'][] = $object_data;
            }
        }

        return $data;
    }



    public function unmarshall(array $data = [])
    {
        $private_properties = ['row_wrapper_class', 'index_field', 'options'];

        foreach ($private_properties as $property) {
            if (isset($data[$property])) {
                $this->$property = $data[$property];
            } else {
                $this->$property = null;
            }
        }

        $this->objects = [];
        $this->objects_by_index = [];
        if (is_subclass_of($this->row_wrapper_class, 'SwatDBMarshallable')) {
            if (isset($data['objects'])) {
                foreach ($data['objects'] as $object_data) {
                    $object = new $this->row_wrapper_class();
                    $object->unmarshall($object_data);
                    $this->objects[] = $object;
                }
            }
            if ($this->index_field != '') {
                $this->reindex();
            }
        }
    }


    // manipulating of sub data objects


    /**
     * Gets the values of an internal property for each record in this set
     *
     * @param string $name name of the internal property to get.
     *
     * @return array an array of values.
     *
     * @throws SwatDBException if records in this recordset do not have an
     *                         internal value with the specified <i>$name</i>.
     *
     * @see SwatDBDataObject::getInternalValue()
     */
    public function getInternalValues($name)
    {
        $values = [];

        if (count($this) > 0) {
            if (!$this->getFirst()->hasInternalValue($name)) {
                throw new SwatDBException(
                    'Records in this recordset do not contain an internal ' .
                        "field named '{$name}'.",
                );
            }

            foreach ($this->objects as $object) {
                $values[] = $object->getInternalValue($name);
            }
        }

        return $values;
    }



    /**
     * Loads all sub-data-objects for an internal property of the data-objects
     * in this recordset
     *
     * This is used to efficiently load sub-objects when there is a one-to-one
     * relationship between the objects in this recordset and the sub-objects.
     * This is usually the case when there is a foreign key constraint in the
     * database table for the objects in this recordset.
     *
     * @param string $name name of the internal property to load.
     * @param MDB2_Driver_Common $db database object.
     * @param string $sql SQL to execute with placeholder for the set of
     *                     internal property values. For example:
     *                     <code>select * from Foo where id in (%s)</code>.
     * @param string $wrapper the class name of the recordset wrapper to use
     *                         for the sub-data-objects.
     * @param string $type optional. The MDB2 datatype of the internal property
     *                      values. If not specified, 'integer' is used.
     *
     * @return SwatDBRecordsetWrapper an instance of the wrapper, or null.
     */
    public function loadAllSubDataObjects(
        $name,
        MDB2_Driver_Common $db,
        $sql,
        $wrapper,
        $type = 'integer',
    ) {
        $sub_data_objects = null;

        $values = $this->getInternalValues($name);
        $values = array_filter($values, function ($value) {
            return $value !== null;
        });

        $values = array_unique($values);

        if (count($values) > 0) {
            $this->checkDB();
            $this->db->loadModule('Datatype', null, true);
            $quoted_values = $this->db->datatype->implodeArray($values, $type);

            $sql = sprintf($sql, $quoted_values);
            $sub_data_objects = SwatDB::query($db, $sql, $wrapper);
            $this->attachSubDataObjects($name, $sub_data_objects);
        }

        return $sub_data_objects;
    }



    /**
     * Attach existing sub-dataobjects for an internal property of the
     * dataobjects in this recordset
     *
     * @param string $name name of the property to attach to.
     * @param SwatDBRecordsetWrapper $sub_data_objects
     */
    public function attachSubDataObjects(
        $name,
        SwatDBRecordsetWrapper $sub_data_objects,
    ) {
        if ($sub_data_objects->index_field === null) {
            throw new SwatDBException(
                sprintf(
                    'Index field must be specified in the sub-data-object ' .
                        'recordset wrapper class (%s::init()) ' .
                        'in order to attach recordset as sub-dataobjects.',
                    get_class($sub_data_objects),
                ),
            );
        }

        foreach ($this->objects as $object) {
            $value = $object->getInternalValue($name);
            if (isset($sub_data_objects[$value])) {
                $object->$name = $sub_data_objects[$value];
            }
        }
    }


    // manipulating of sub-recordsets


    /**
     * Efficiently loads sub-recordsets for records in this recordset
     *
     * @param string $name the name of the sub-recordset.
     * @param string $wrapper the name of the recordset wrapper class to use
     *                         for the sub-recordsets.
     * @param string $table the name of the table containing sub-records.
     * @param string $binding_field the name of the binding field in the
     *                               table containing sub-records. This should
     *                               be a field that contains index values from
     *                               this recordset (i.e., a foreign key).
     * @param string $where optional additional where clause to apply to the
     *                       sub-records.
     * @param string $order_by optional ordering of sub-recordsets.
     * @param string $fields optional list of fields to return. By default
     *                       all fields are returned. This can be used to
     *                       optimize tables with large text fields or a lot
     *                       of fields that aren't used in this context.
     *
     * @throws SwatDBException if this recordset does not define an index
     *                         field.
     *
     * @return SwatDBRecordsetWrapper a wrapper of the sub-recordsets, or null.
     */
    public function loadAllSubRecordsets(
        $name,
        $wrapper,
        $table,
        $binding_field,
        $where = '',
        $order_by = '',
        $fields = '*',
    ) {
        $this->checkDB();

        if ($this->index_field === null) {
            throw new SwatDBException(
                sprintf(
                    'Index field must be specified in the recordset wrapper ' .
                        'class (%s::init()) in order to attach sub-recordsets.',
                    get_class($this),
                ),
            );
        }

        // default binding field type is integer
        $binding_field = new SwatDBField($binding_field, 'integer');

        // return empty recordset if this is an empty recordset
        if (count($this) === 0) {
            $recordset = new $wrapper();
            $recordset->setDatabase($this->db);
            return $recordset;
        }

        // get record ids
        $record_ids = [];
        foreach ($this as $record) {
            $record_ids[] = $record->{$this->index_field};
        }

        $record_ids = $this->db->implodeArray(
            $record_ids,
            $binding_field->type,
        );

        // build SQL to select all records
        $sql = sprintf(
            'select %s from %s
			where %s in (%s)',
            $fields,
            $table,
            $binding_field->name,
            $record_ids,
        );

        if ($where != '') {
            $sql .= ' and ' . $where;
        }

        $sql .= ' order by ' . $binding_field->name;
        if ($order_by != '') {
            $sql .= ', ' . $order_by;
        }

        // get all records
        $recordset = SwatDB::query($this->db, $sql, $wrapper);

        return $this->attachSubRecordset(
            $name,
            $wrapper,
            $binding_field->name,
            $recordset,
        );
    }



    /**
     * Efficiently loads sub-recordsets for records in this recordset
     *
     * @param string $name the name of the sub-recordset.
     * @param string $wrapper the name of the recordset wrapper class to use
     *                         for the sub-recordsets.
     * @param string $binding_field the name of the binding field in the
     *                               table containing sub-records. This should
     *                               be a field that contains index values from
     *                               this recordset (i.e., a foreign key).
     * @param SwatDBRecordsetWrapper $recordset the recordset to attach
     *
     * @throws SwatDBException if this recordset does not define an index
     *                         field.
     *
     * @return SwatDBRecordsetWrapper a wrapper of the sub-recordsets, or null.
     */
    public function attachSubRecordset(
        $name,
        $wrapper,
        $binding_field,
        SwatDBRecordsetWrapper $recordset,
    ) {
        $this->checkDB();

        // assign empty recordsets for all records in this set
        foreach ($this as $record) {
            if ($wrapper instanceof SwatDBRecordsetWrapper) {
                $empty_recordset = $wrapper->copyEmpty();
            } else {
                $empty_recordset = new $wrapper(null);
            }
            $record->$name = $empty_recordset;
        }

        // split records into separate recordsets for records in this set
        $current_record_id = null;
        $current_recordset = null;
        foreach ($recordset as $record) {
            $record_id = $record->getInternalValue($binding_field);

            // if recordset being attached references records not in
            // this recordset, ignore them
            if (!isset($this[$record_id])) {
                continue;
            }

            if ($record_id !== $current_record_id) {
                $current_record_id = $record_id;
                $current_recordset = $this[$record_id]->$name;
            }

            $current_recordset->add($record);
        }

        return $recordset;
    }


    // manipulating of objects


    /**
     * Gets the index values of the records in this recordset
     *
     * @return array the index values of the records in this recordset.
     */
    public function getIndexes()
    {
        if ($this->index_field === null) {
            throw new SwatDBException(
                sprintf(
                    'Index field must be specified in the recordset wrapper ' .
                        'class (%s::init()) in order to get the record indexes.',
                    get_class($this),
                ),
            );
        }

        return array_keys($this->objects_by_index);
    }



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



    /**
     * Retrieves the first object in this recordset
     *
     * @return mixed the first object or null if there are no objects in this
     *                recordset.
     */
    public function getFirst()
    {
        $first = null;

        if (count($this->objects) > 0) {
            $first = reset($this->objects);
        }

        return $first;
    }



    /**
     * Retrieves the last object in this recordset
     *
     * @return mixed the last object or null if there are no objects in this
     *                recordset.
     */
    public function getLast()
    {
        $last = null;

        if (count($this->objects) > 0) {
            $last = end($this->objects);
        }

        return $last;
    }



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
        return isset($this[$index]) ? $this[$index] : null;
    }



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
                if ($this->current_index >= $key && $this->current_index > 0) {
                    $this->current_index--;
                }
            }

            // reindex ordinal array of records
            $this->objects = array_values($this->objects);
        }
    }



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



    /**
     * Removes all records from this recordset
     */
    public function removeAll()
    {
        $this->removed_objects = array_values($this->objects);
        $this->objects = [];
        $this->objects_by_index = [];
        $this->current_index = 0;
    }



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
            $this->objects_by_index = [];
            $index_field = $this->index_field;
            foreach ($this->objects as $object) {
                if (isset($object->$index_field)) {
                    $this->objects_by_index[$object->$index_field] = $object;
                }
            }
        }
    }



    /**
     * Gets the values of a property for each record in this set
     *
     * @param string $name name of the property to get.
     *
     * @return array an array of values.
     *
     * @throws SwatDBException if records in this recordset do not have a
     *                          property with the specified <i>$name</i>.
     */
    public function getPropertyValues($name)
    {
        $values = [];

        if (count($this) > 0) {
            if (!isset($this->getFirst()->$name)) {
                throw new SwatDBException(
                    'Records in this recordset do not contain a property ' .
                        "named '{$name}'.",
                );
            }

            foreach ($this->objects as $object) {
                $values[] = $object->$name;
            }
        }

        return $values;
    }


    // database loading and saving


    /**
     * Sets the database driver for this recordset
     *
     * The database is automatically set for all recordable records of this
     * recordset.
     *
     * @param MDB2_Driver_Common $db  the database driver to use for this
     *                                recordset.
     * @param array              $set optional array of objects passed through
     *                                recursive call containing all objects that
     *                                have been set already. Prevents infinite
     *                                recursion.
     */
    public function setDatabase(MDB2_Driver_Common $db, array $set = [])
    {
        $key = spl_object_hash($this);

        if (isset($set[$key])) {
            // prevent infinite recursion on datastructure cycles
            return;
        }

        $this->db = $db;
        $set[$key] = true;

        foreach ($this->objects as $object) {
            if ($object instanceof SwatDBRecordable) {
                $object->setDatabase($db, $set);
            }
        }
    }



    /**
     * Saves this recordset to the database
     *
     * Saving a recordset works as follows:
     *  1. Objects that were removed are deleted from the database.
     *  2. Objects that were added are inserted into the database,
     *  3. Objects that were modified are updated in the database,
     *
     * Deleting is performed before adding incase a new row with the same
     * values as a deleted row is added. For example, a binding is removed and
     * an identical binding is added.
     */
    public function save()
    {
        $this->checkDB();
        $transaction = new SwatDBTransaction($this->db);
        try {
            foreach ($this->removed_objects as $object) {
                $object->setDatabase($this->db);
                $object->delete();
            }

            foreach ($this->objects as $object) {
                $object->setDatabase($this->db);
                $object->save();
            }

            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollback();
            throw $e;
        }

        $this->removed_objects = [];
        $this->reindex();
    }



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
        if (!is_array($object_indexes)) {
            throw new SwatInvalidTypeException(
                'The $object_indexes property must be an array.',
                0,
                $object_indexes,
            );
        }

        $interfaces = class_implements($this->row_wrapper_class);
        if (!in_array('SwatDBRecordable', $interfaces)) {
            throw new SwatInvalidClassException(
                'The recordset must define a row wrapper class that is an ' .
                    'instance of SwatDBRecordable for recordset loading to work.',
                0,
                $this->row_wrapper_class,
            );
        }

        $success = true;

        // try to load all records
        $records = [];
        $class_name = $this->row_wrapper_class;
        foreach ($object_indexes as $index) {
            $record = new $class_name();
            $record->setDatabase($this->db);
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
            $this->objects = [];
            $this->objects_by_index = [];
            $this->removed_objects = [];

            foreach ($records as $record) {
                $this[] = $record;
            }

            $this->reindex();
        }

        return $success;
    }



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
        if (count($this->removed_objects) > 0) {
            return true;
        }

        foreach ($this->objects as $name => $object) {
            if ($object->isModified()) {
                return true;
            }
        }

        return false;
    }



    /**
     * Sets the flushable cache to use for this record-set
     *
     * Using a flushable cache allows clearing the cache when the records
     * are modified or deleted.
     *
     * @param SwatDBCacheNsFlushable $cache The flushable cache to use for
     *                                      this dataobject.
     */
    public function setFlushableCache(SwatDBCacheNsFlushable $cache)
    {
        foreach ($this->objects as $object) {
            if ($object instanceof SwatDBFlushable) {
                $object->setFlushableCache($cache);
            }
        }
    }

}
