<?php

/**
 * All public properties correspond to database fields
 *
 * @package   SwatDB
 * @copyright 2005-2024 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDBDataObject extends SwatObject implements
    Serializable,
    SwatDBRecordable,
    SwatDBMarshallable,
    SwatDBFlushable
{


    /**
     * @var array
     */
    private $property_hashes = [];

    /**
     * @var array
     */
    private $sub_data_objects = [];

    /**
     * @var array
     */
    private $internal_properties = [];

    /**
     * @var array
     */
    private $internal_property_autosave = [];

    /**
     * @var array
     */
    private $internal_property_accessible = [];

    /**
     * @var array
     */
    private $internal_property_classes = [];

    /**
     * @var array
     */
    private $date_properties = [];

    /**
     * @var boolean
     */
    private $loaded_from_database = false;

    /**
     * @var array
     */
    private $deprecated_properties = [];



    /**
     * @var MDB2
     */
    protected $db = null;

    protected $table = null;
    protected $id_field = null;

    /**
     * A class-mapping object
     *
     * @var SwatDBClassMap
     */
    protected $class_map;

    /**
     * @var boolean
     */
    protected $read_only = false;

    /**
     * @var SwatDBCacheNsFlushable
     * @see SwatDBDataObject::setFlushableCache()
     */
    protected $flushable_cache;



    /**
     * Cache of public property names indexed by class name
     *
     * @var array
     */
    private static $public_properties_cache = [];



    /**
     * @param mixed $data
     * @param boolean $read_only Whether this data object is read only. Setting
     *                read-only to true will improve the performance of
     *                creating large amounts of dataobjects that will never be
     *                saved.
     */
    public function __construct($data = null, $read_only = false)
    {
        $this->class_map = SwatDBClassMap::instance();
        $this->read_only = $read_only;

        $this->init();

        if ($data !== null) {
            $this->initFromRow($data);
        }

        $this->generatePropertyHashes();
    }



    /**
     * @param string $table Database table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }



    /**
     * Gets a list of all the modified properties of this object
     *
     * @return array an array of modified properties and their values in the
     *                form of: name => value
     */
    public function getModifiedProperties()
    {
        if ($this->read_only) {
            return [];
        }

        $modified_properties = [];
        foreach ($this->getProperties() as $name => $value) {
            $hashed_value = $this->getHashValue($value);
            if (
                array_key_exists($name, $this->property_hashes) &&
                $hashed_value !== $this->property_hashes[$name]
            ) {
                $modified_properties[$name] = $value;
            }
        }

        return $modified_properties;
    }



    public function __get($key)
    {
        if (in_array($key, $this->deprecated_properties)) {
            return $this->getDeprecatedProperty($key);
        }

        $property_list = $this->getProtectedPropertyList();
        if (array_key_exists($key, $property_list)) {
            if (array_key_exists('get', $property_list[$key])) {
                $get = $property_list[$key]['get'];
                return $this->$get();
            } else {
                return $this->$key;
            }
        }

        $value = $this->getUsingLoaderMethod($key);

        if ($value === false) {
            $value = $this->getUsingInternalProperty($key);
        }

        if ($value === false) {
            $loader_method = $this->getLoaderMethod($key);
            throw new SwatDBException(
                sprintf(
                    "A property named '%s' does not " .
                        'exist on the %s data-object. If the property corresponds ' .
                        'directly to a database field it should be added as a public ' .
                        'property of this data object. If the property should access ' .
                        'a sub-data-object, either specify a class when registering ' .
                        "the internal property named '%s' or define a custom loader " .
                        "method named '%s()'.",
                    $key,
                    static::class,
                    $key,
                    $loader_method,
                ),
            );
        }

        return $value;
    }



    public function __set($key, $value)
    {
        if (in_array($key, $this->deprecated_properties)) {
            $this->setDeprecatedProperty($key, $value);
            return;
        }

        $property_list = $this->getProtectedPropertyList();
        if (array_key_exists($key, $property_list)) {
            if (array_key_exists('set', $property_list[$key])) {
                $set = $property_list[$key]['set'];
                $this->$set($value);
                return;
            } else {
                $this->$key = $value;
                return;
            }
        }

        if (method_exists($this, $this->getLoaderMethod($key))) {
            if ($value === null) {
                $this->unsetSubDataObject($key);
            } else {
                $this->setSubDataObject($key, $value);
            }
        } elseif (
            $this->hasInternalValue($key) &&
            $this->internal_property_accessible[$key]
        ) {
            if ($value instanceof SwatDBDataObject) {
                $this->setSubDataObject($key, $value);
                $this->setInternalValue($key, $value->getId());
            } elseif ($value === null) {
                $this->unsetSubDataObject($key);
                $this->setInternalValue($key, $value);
            } else {
                // TODO: Maybe unset sub dataobject here
                $this->setInternalValue($key, $value);
            }
        } else {
            throw new SwatDBException(
                "A property named '{$key}' does not exist on this " .
                    'dataobject.  If the property corresponds directly to ' .
                    'a database field it should be added as a public property ' .
                    'of this data object.  If the property should access a ' .
                    'sub-dataobject, specify a class when registering the ' .
                    "internal field named '{$key}'.",
            );
        }
    }



    public function __isset($key)
    {
        $is_set = false;

        if (!in_array($key, $this->deprecated_properties)) {
            $is_set =
                method_exists($this, $this->getLoaderMethod($key)) ||
                ($this->hasInternalValue($key) &&
                    $this->internal_property_accessible[$key]) ||
                array_key_exists($key, $this->getProtectedPropertyList());
        }

        return $is_set;
    }



    /**
     * Gets a string representation of this data-object
     *
     * @return string this data-object represented as a string.
     *
     * @see SwatObject::__toString()
     */
    public function __toString(): string
    {
        // prevent printing of MDB2 object for dataobjects
        $db = $this->db;
        $this->db = null;

        $modified_properties = $this->getModifiedProperties();
        $properties = $this->getProperties();

        foreach ($this->getSerializableSubDataObjects() as $name) {
            if (!isset($properties[$name])) {
                $properties[$name] = null;
            }
        }

        ob_start();
        printf('<h3>%s</h3>', static::class);
        echo $this->isModified() ? '(modified)' : '(not modified)', '<br />';
        foreach ($properties as $name => $value) {
            if ($this->hasSubDataObject($name)) {
                $value = $this->getSubDataObject($name);
            }

            $modified = isset($modified_properties[$name]);

            if ($value instanceof SwatDBRecordable) {
                $value = $value::class;
            }

            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }

            if ($value === null) {
                $value = '<null>';
            }

            if (is_array($value)) {
                $value = print_r($value, true);
            }

            $value = (string) $value;

            printf(
                "%s = %s%s<br />\n",
                SwatString::minimizeEntities($name),
                SwatString::minimizeEntities($value),
                $modified ? ' (modified)' : '',
            );
        }
        /*
        $reflector = new ReflectionClass(get_class($this));
        foreach ($reflector->getMethods() as $method) {
            if ($method->isProtected()) {
                $name = $method->getName();
                if (mb_substr($name, 0, 4) === 'load')
                    echo $name;
            }
        }
        */
        $string = (string) ob_get_clean();

        // set db back again
        $this->db = $db;

        return $string;
    }



    public function getInternalValue($name)
    {
        if (array_key_exists($name, $this->internal_properties)) {
            return $this->internal_properties[$name];
        } else {
            return null;
        }
    }



    public function hasInternalValue($name)
    {
        return array_key_exists($name, $this->internal_properties);
    }



    /**
     * Whether or not a public property exists for the given property name
     *
     * @param string $name the property name to check.
     *
     * @return boolean true if a public property exists and false if it does
     *                 not.
     *
     * @see SwatDBDataObject::getPublicProperties()
     */
    public function hasPublicProperty($name)
    {
        $public_properties = $this->getPublicProperties();

        return array_key_exists($name, $public_properties);
    }



    /**
     * Whether or not a registered date property exists for the given property
     * name
     *
     * @param string $name the property name to check.
     *
     * @return boolean true if a registered date property exists and false if
     *                 it does not.
     *
     * @see SwatDBDataObject::registerDateProperty()
     */
    public function hasDateProperty($name)
    {
        return in_array($name, $this->date_properties);
    }



    /**
     * Duplicates this object
     *
     * A duplicate is less of an exact copy than a true clone. Like a clone, a
     * duplicate has all the same public property values.  Unlike a clone, a
     * duplicate does not have an id and therefore can be saved to the
     * database as a new row. This method recursively duplicates
     * sub-dataobjects which were registered with <i>$autosave</i> set to true.
     *
     * @return SwatDBDataobject a duplicate of this object.
     */
    public function duplicate()
    {
        $class = static::class;
        $new_object = new $class();
        $id_field = new SwatDBField($this->id_field, 'integer');

        // public properties
        $properties = array_merge(
            $this->getPublicProperties(),
            $this->getSerializableProtectedProperties(),
        );

        foreach ($properties as $name => $value) {
            if ($name !== $id_field->name) {
                $new_object->$name = $this->$name;
            }
        }

        // sub-dataobjects
        foreach ($this->sub_data_objects as $name => $object) {
            $saver_method =
                'save' . str_replace(' ', '', ucwords(strtr($name, '_', ' ')));

            $object->setDatabase($this->db);
            if (method_exists($this, $saver_method)) {
                $new_object->$name = $object->duplicate();
            } elseif (!array_key_exists($name, $this->internal_properties)) {
                $new_object->$name = $object;
            }
        }

        // internal properties
        foreach ($this->internal_properties as $name => $value) {
            if (
                !(
                    array_key_exists(
                        $name,
                        $this->internal_property_accessible,
                    ) && $this->internal_property_accessible[$name]
                )
            ) {
                continue;
            }

            $autosave = $this->internal_property_autosave[$name];

            if ($this->hasSubDataObject($name)) {
                $object = $this->getSubDataObject($name);
                if ($autosave) {
                    $new_object->$name = $object->duplicate();
                } else {
                    $new_object->$name = $object;
                }
            } else {
                $new_object->$name = $value;
            }
        }

        $new_object->setDatabase($this->db);

        return $new_object;
    }



    /**
     * Returns an array of the public and protected properties of this object
     *
     * This array is useful for places where get_object_vars() is useful but we
     * also want to return the protected properties alongside the public onces.
     * For example when using getter and setter methods instead of public
     * properties on a dataobject.
     *
     * @return array an array of public and protected properties.
     *
     * @see SwatDBDataObject::getPublicProperties()
     * @see SwatDBDataObject::getSerializableProtectedProperties()
     */
    public function getAttributes()
    {
        return array_merge(
            $this->getPublicProperties(),
            $this->getProtectedProperties(),
        );
    }



    protected function setInternalValue($name, $value)
    {
        if (array_key_exists($name, $this->internal_properties)) {
            $this->internal_properties[$name] = $value;
        }
    }



    protected function init()
    {
    }



    protected function registerDateProperty($name)
    {
        $this->date_properties[] = $name;
    }



    protected function registerInternalProperty(
        $name,
        $class = null,
        $autosave = false,
        $accessible = true,
    ) {
        $this->internal_properties[$name] = null;
        $this->internal_property_autosave[$name] = $autosave;
        $this->internal_property_accessible[$name] = $accessible;
        $this->internal_property_classes[$name] = $class;
    }



    protected function registerDeprecatedProperty($name)
    {
        $this->deprecated_properties[] = $name;
    }



    /**
     * Takes a data row and sets the properties of this object according to
     * the values of the row
     *
     * Subclasses can override this method to provide additional
     * functionality.
     *
     * @param mixed $row the row to use as either an array or object.
     */
    protected function initFromRow($row)
    {
        if ($row === null) {
            throw new SwatDBException(
                'Attempting to initialize dataobject with a null row.',
            );
        }

        $property_array = array_merge(
            $this->getPublicProperties(),
            $this->getSerializableProtectedProperties(),
        );

        if (is_object($row)) {
            $row = get_object_vars($row);
        }

        foreach ($property_array as $name => $value) {
            // Use array_key_exists() instead of isset(), because isset() will
            // return false when the value is null. Null values on properties
            // should not be ignored - otherwise calling initFromRow() on an
            // existing dataobject can leave out of date values on properties
            // when those values were updated to null.
            if (array_key_exists($name, $row)) {
                if (
                    in_array($name, $this->date_properties) &&
                    $row[$name] !== null
                ) {
                    $this->$name = new SwatDate($row[$name]);
                } else {
                    $this->$name = $row[$name];
                }
            }
        }

        foreach ($this->internal_properties as $name => $value) {
            if (isset($row[$name])) {
                $this->internal_properties[$name] = $row[$name];
            }
        }

        $this->loaded_from_database = true;
    }



    /**
     * Generates the set of md5 hashes for this data object
     *
     * The md5 hashes represent all the public properties of this object and
     * are used to tell if a property has been modified.
     */
    protected function generatePropertyHashes()
    {
        if ($this->read_only) {
            return;
        }

        // Note: SwatDBDataObject::generatePropertyHash() is not used
        // here because it would mean calling the expensive getProperties()
        // method in a loop.
        foreach ($this->getProperties() as $name => $value) {
            $hashed_value = $this->getHashValue($value);
            $this->property_hashes[$name] = $hashed_value;
        }
    }



    /**
     * Generates the MD5 hash for a property of this object
     *
     * @param string $property the name of the property for which to generate
     *                          the hash.
     */
    protected function generatePropertyHash($property)
    {
        if ($this->read_only) {
            return;
        }

        $property_array = $this->getProperties();

        if (isset($property_array[$property])) {
            $hashed_value = $this->getHashValue($property_array[$property]);
            $this->property_hashes[$property] = $hashed_value;
        }
    }



    /**
     * Gets the hash of a value
     *
     * Used to detect modified properties.
     *
     * @param mixed $value the value to hash.
     *
     * @return string the hashed value.
     */
    protected function getHashValue($value)
    {
        return md5(serialize($value));
    }



    protected function getId()
    {
        if ($this->id_field === null) {
            throw new SwatDBException(
                sprintf(
                    'Property $id_field is not set for class %s.',
                    static::class,
                ),
            );
        }

        $id_field = new SwatDBField($this->id_field, 'integer');
        $temp = $id_field->name;
        return $this->$temp;
    }



    protected function getSubDataObject($name)
    {
        return $this->sub_data_objects[$name];
    }



    protected function setSubDataObject($name, $value)
    {
        // Can't add type-hinting because dataobjects may not be dataobjects.
        // Go figure.
        $this->sub_data_objects[$name] = $value;
        if ($value instanceof SwatDBRecordable && $this->db !== null) {
            $value->setDatabase($this->db);
        }
    }



    protected function unsetSubDataObject($name)
    {
        unset($this->sub_data_objects[$name]);
    }



    /**
     * Whether or not a sub data object is loaded for the given key
     *
     * @param string $key the key to check.
     *
     * @return boolean true if a sub data object is loaded and false if it is
     *                 not.
     */
    protected function hasSubDataObject($key)
    {
        return isset($this->sub_data_objects[(string) $key]);
    }



    protected function setDeprecatedProperty($key, $value)
    {
    }



    protected function getDeprecatedProperty($key)
    {
        return null;
    }



    /**
     * Gets a list of all protected properties of this data-object
     *
     * The keys of this array should map to protected properties on this
     * object. The value of each entry is a second array in the format
     * [ 'get' => 'getMethodName', 'set' => 'setMethodName' ]
     * where the keys of each array correspond to methods on this object that
     * will be called by the magic getter and setter when the protected
     * property is accessed.
     *
     * @return array an array of protected property keys and method values.
     */
    protected function getProtectedPropertyList()
    {
        return [];
    }



    /**
     * Gets the public properties of this data-object
     *
     * Public properties should correspond directly to database fields.
     *
     * @return array a reference to an associative array of public properties
     *                of this data-object. The array is of the form
     *                'property name' => 'property value'.
     */
    private function getPublicProperties()
    {
        $class = static::class;

        // cache class public property names since reflection is expensive
        if (!array_key_exists($class, self::$public_properties_cache)) {
            $public_properties = [];

            $reflector = new ReflectionClass($class);
            foreach ($reflector->getProperties() as $property) {
                if ($property->isPublic() && !$property->isStatic()) {
                    $public_properties[] = $property->getName();
                }
            }

            self::$public_properties_cache[$class] = $public_properties;
        }

        // get property values for this object
        $names = self::$public_properties_cache[$class];
        $properties = [];
        foreach ($names as $name) {
            $properties[$name] = $this->$name;
        }

        return $properties;
    }



    /**
     * Gets the serializable protected properties of this data-object.
     *
     * Protected properties should correspond directly to database fields.
     *
     * @return array a reference to an associative array of protected properties
     *                of this data-object. The array is of the form
     *                'property name' => 'property value'.
     */
    private function getSerializableProtectedProperties()
    {
        $properties = [];
        foreach ($this->getProtectedPropertyList() as $property => $accessors) {
            // We want to maintain what is internally stored in this object so
            // we don't want to use the getter. What the getter returns
            // publicly may be different than what we have internally.
            $properties[$property] = $this->$property;
        }

        return $properties;
    }



    /**
     * Gets the protected properties of this data-object using the getter
     * accessor.
     *
     * Protected properties should correspond directly to database fields.
     *
     * @return array a reference to an associative array of protected properties
     *                of this data-object. The array is of the form
     *                'property name' => 'property value'.
     */
    private function getProtectedProperties()
    {
        $properties = [];
        foreach ($this->getProtectedPropertyList() as $property => $accessors) {
            // Use the getter for the property.
            $properties[$property] = $this->{$accessors['get']}();
        }

        return $properties;
    }



    /**
     * Gets all the modifyable properties of this data-object
     *
     * This includes the public and protected properties that correspond to
     * database fields and the internal values that also correspond to database
     * fields.
     *
     * @return array a reference to an associative array of properties of this
     *                data-object. The array is of the form
     *                'property name' => 'property value'.
     */
    private function &getProperties()
    {
        $property_array = array_merge(
            $this->getPublicProperties(),
            $this->getSerializableProtectedProperties(),
            $this->internal_properties,
        );

        return $property_array;
    }



    private function getLoaderMethod($key)
    {
        /*
         * Because this method is called so frequently, we cache the calculated
         * loader method names so we don't have to calculate them thousands of
         * times.
         */
        static $cache = [];

        if (!array_key_exists($key, $cache)) {
            $cache[$key] =
                'load' .
                str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
        }

        return $cache[$key];
    }



    private function getUsingLoaderMethod($key)
    {
        $value = false;

        $loader_method = $this->getLoaderMethod($key);
        if (method_exists($this, $loader_method)) {
            if ($this->hasSubDataObject($key)) {
                // return loaded sub-dataobject
                $value = $this->getSubDataObject($key);
            } else {
                // use loader method to load sub-dataobject
                $this->checkDB();
                $this->setSubDataObject(
                    $key,
                    call_user_func([$this, $loader_method]),
                );

                $value = $this->getSubDataObject($key);
            }
        }

        return $value;
    }



    private function getUsingInternalProperty($key)
    {
        $value = false;

        if (
            array_key_exists($key, $this->internal_property_accessible) &&
            $this->internal_property_accessible[$key]
        ) {
            if ($this->hasSubDataObject($key)) {
                // return loaded sub-dataobject
                $value = $this->getSubDataObject($key);
            } elseif ($this->hasInternalValue($key)) {
                $value = $this->getInternalValue($key);

                if (
                    $value !== null &&
                    isset($this->internal_property_classes[$key])
                ) {
                    // autoload sub-dataobject
                    $class = $this->internal_property_classes[$key];

                    if (!class_exists($class)) {
                        throw new SwatClassNotFoundException(
                            sprintf(
                                "Class '%s' registered for internal property '%s' " .
                                    'does not exist.',
                                $class,
                                $key,
                            ),
                            0,
                            $class,
                        );
                    }

                    $this->checkDB();

                    $object = new $class();
                    $object->setDatabase($this->db);
                    $object->load($value); // autoload
                    $this->setSubDataObject($key, $object);

                    $value = $object;
                }
            }
        }

        return $value;
    }


    // database loading and saving


    /**
     * Sets the database driver for this data-object
     *
     * The database is automatically set for all recordable sub-objects of this
     * data-object.
     *
     * @param MDB2_Driver_Common $db  the database driver to use for this
     *                                data-object.
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

        foreach ($this->sub_data_objects as $name => $object) {
            if ($object instanceof SwatDBRecordable) {
                $object->setDatabase($db, $set);
            }
        }
    }



    /**
     * Saves this object to the database
     *
     * Only modified properties are updated.
     */
    public function save()
    {
        if ($this->read_only) {
            throw new SwatDBException(
                'This dataobject was loaded read-only and cannot be saved.',
            );
        }

        $this->checkDB();

        $transaction = new SwatDBTransaction($this->db);
        try {
            $rollback_property_hashes = $this->property_hashes;

            $this->saveInternalProperties();
            $this->saveInternal();
            $this->generatePropertyHashes();
            $this->saveSubDataObjects();

            // Save again in-case values have been changed in saveSubDataObjects()
            if ($this->id_field !== null) {
                $this->saveInternal();
            }

            $transaction->commit();
        } catch (Throwable $e) {
            $this->rollback($transaction, $rollback_property_hashes);
            throw $e;
        }

        $this->generatePropertyHashes();
    }



    /**
     * Loads this object's properties from the database given an id
     *
     * @param mixed $id the id of the database row to set this object's
     *               properties with.
     *
     * @return boolean whether data was sucessfully loaded.
     */
    public function load($id)
    {
        $this->checkDB();
        $row = $this->loadInternal($id);

        if ($row === null) {
            return false;
        }

        $this->initFromRow($row);
        $this->generatePropertyHashes();
        return true;
    }



    /**
     * Deletes this object from the database
     */
    public function delete()
    {
        if ($this->read_only) {
            throw new SwatDBException(
                'This dataobject was loaded read-only ' .
                    'and cannot be deleted.',
            );
        }

        $this->checkDB();

        $transaction = new SwatDBTransaction($this->db);
        try {
            $rollback_property_hashes = $this->property_hashes;
            $this->deleteInternal();
            $transaction->commit();
        } catch (Throwable $e) {
            $this->rollback($transaction, $rollback_property_hashes);
            throw $e;
        }
    }



    /**
     * Returns true if this object has been modified since it was loaded
     *
     * @return boolean true if this object was modified and false if this
     *                  object was not modified.
     */
    public function isModified()
    {
        if ($this->read_only) {
            return false;
        }

        foreach ($this->getProperties() as $name => $value) {
            $hashed_value = $this->getHashValue($value);
            if (
                isset($this->property_hashes[$name]) &&
                $hashed_value !== $this->property_hashes[$name]
            ) {
                return true;
            }
        }

        foreach ($this->internal_property_autosave as $name => $autosave) {
            if ($autosave && isset($this->sub_data_objects[$name])) {
                $object = $this->sub_data_objects[$name];
                if (
                    $object instanceof SwatDBRecordable &&
                    $object->isModified()
                ) {
                    return true;
                }
            }
        }

        foreach ($this->sub_data_objects as $name => $object) {
            $saver_method =
                'save' . str_replace(' ', '', ucwords(strtr($name, '_', ' ')));

            if (method_exists($this, $saver_method)) {
                $object = $this->sub_data_objects[$name];
                if (
                    $object instanceof SwatDBRecordable &&
                    $object->isModified()
                ) {
                    return true;
                }
            }
        }

        return false;
    }



    protected function checkDB()
    {
        if ($this->db === null) {
            throw new SwatDBNoDatabaseException(
                sprintf(
                    'No database available to this dataobject (%s). ' .
                        'Call the setDatabase method.',
                    static::class,
                ),
            );
        }
    }



    /**
     * Loads this object's properties from the database given an id
     *
     * @param mixed $id the id of the database row to set this object's
     *               properties with.
     *
     * @return object data row or null.
     */
    protected function loadInternal($id)
    {
        if ($this->table !== null && $this->id_field !== null) {
            $id_field = new SwatDBField($this->id_field, 'integer');
            $sql = 'select * from %s where %s = %s';

            $sql = sprintf(
                $sql,
                $this->table,
                $id_field->name,
                $this->db->quote($id, $id_field->type),
            );

            $rs = SwatDB::query($this->db, $sql, null);
            $row = $rs->fetchRow(MDB2_FETCHMODE_ASSOC);

            return $row;
        }
        return null;
    }



    /**
     * Saves this object to the database
     *
     * Only modified properties are updated.
     */
    protected function saveInternal()
    {
        $modified_properties = $this->getModifiedProperties();

        if (count($modified_properties) === 0) {
            return;
        }

        if ($this->table === null) {
            trigger_error(
                sprintf('No table defined for %s', static::class),
                E_USER_NOTICE,
            );

            return;
        }

        if ($this->id_field === null) {
            if (!$this->loaded_from_database) {
                $this->saveNewBinding();
                return;
            }

            trigger_error(
                sprintf('No id_field defined for %s', static::class),
                E_USER_NOTICE,
            );

            return;
        }

        $id_field = new SwatDBField($this->id_field, 'integer');

        if (!property_exists($this, $id_field->name)) {
            trigger_error(
                sprintf(
                    "The id_field '%s' is not defined for %s",
                    $id_field->name,
                    static::class,
                ),
                E_USER_NOTICE,
            );

            return;
        }

        $id_ref = $id_field->name;
        $id = $this->$id_ref;

        $fields = [];
        $values = [];

        foreach ($modified_properties as $name => $value) {
            if ($name === $id_field->name) {
                continue;
            }

            $type = $this->guessType($name, $value);

            if ($type == 'date') {
                $value = $value->getDate();
            }

            $fields[] = sprintf('%s:%s', $type, $name);
            $values[$name] = $value;
        }

        if ($id === null) {
            $this->$id_ref = SwatDB::insertRow(
                $this->db,
                $this->table,
                $fields,
                $values,
                $id_field->__toString(),
            );
        } else {
            SwatDB::updateRow(
                $this->db,
                $this->table,
                $fields,
                $values,
                $id_field->__toString(),
                $id,
            );
        }

        // Note: This flushes any name-spaces with the newly saved
        // data-object data. In theory you may need to flush name-spaces
        // that rely on the pre-changed values. You must handle this case
        // manually in your application's code by cloning the object
        // before setting the values.
        $this->flushCacheNamespaces();
    }



    protected function saveInternalProperties()
    {
        foreach ($this->internal_property_autosave as $name => $autosave) {
            if ($autosave && $this->hasSubDataObject($name)) {
                $object = $this->getSubDataObject($name);
                $object->save();
                $this->setInternalValue($name, $object->getId());
            }
        }
    }



    protected function saveSubDataObjects()
    {
        foreach ($this->sub_data_objects as $name => $object) {
            $saver_method =
                'save' . str_replace(' ', '', ucwords(strtr($name, '_', ' ')));

            if (method_exists($this, $saver_method)) {
                call_user_func([$this, $saver_method]);
            }
        }

        /*
         * This handles the case where an internal property sub-dataobject also
         * exists in a sub-dataobject using a saver method. After the saver
         * method runs, the id of the internal property sub-dataobject is known
         * so we update it and then save this object's internal values again.
         */
        foreach (array_keys($this->internal_properties) as $name) {
            if ($this->hasSubDataObject($name)) {
                $sub_data_object = $this->getSubDataObject($name);
                if ($sub_data_object instanceof SwatDBDataObject) {
                    $id = $sub_data_object->getId();
                    $this->setInternalValue($name, $id);
                }
            }
        }
    }



    /**
     * Deletes this object from the database
     */
    protected function deleteInternal()
    {
        if ($this->table === null || $this->id_field === null) {
            return;
        }

        $id_field = new SwatDBField($this->id_field, 'integer');

        if (!property_exists($this, $id_field->name)) {
            return;
        }

        $id_ref = $id_field->name;
        $id = $this->$id_ref;

        if ($id !== null) {
            $ns_array = $this->getCacheNamespaces();

            SwatDB::deleteRow(
                $this->db,
                $this->table,
                $id_field->__toString(),
                $id,
            );

            $this->flushCacheNamespaces($ns_array);
        }
    }



    /**
     * Saves a new binding object without an id to the database
     *
     * Only modified properties are saved. It is always inserted,
     * never updated.
     */
    protected function saveNewBinding()
    {
        $modified_properties = $this->getModifiedProperties();

        if (count($modified_properties) === 0) {
            return;
        }

        $fields = [];
        $values = [];

        foreach ($this->getModifiedProperties() as $name => $value) {
            $type = $this->guessType($name, $value);
            $fields[] = sprintf('%s:%s', $type, $name);
            $values[$name] = $value;
        }

        SwatDB::insertRow($this->db, $this->table, $fields, $values);
        $this->flushCacheNamespaces();
    }



    protected function guessType($name, $value)
    {
        switch (gettype($value)) {
            case 'boolean':
                return 'boolean';
            case 'integer':
                return 'integer';
            case 'float':
                return 'float';
            case 'object':
                if ($value instanceof SwatDate) {
                    return 'date';
                }
                return 'text';
            case 'string':
            default:
                return 'text';
        }
    }



    protected function rollback(
        SwatDBTransaction $transaction,
        array $rollback_property_hashes,
    ) {
        $this->property_hashes = $rollback_property_hashes;
        $transaction->rollback();
    }


    // cache flushing


    /**
     * Sets the flushable cache to use for this dataobject
     *
     * Using a flushable cache allows clearing the cache when the dataobject
     * is modified or deleted.
     *
     * @param SwatDBCacheNsFlushable $cache The flushable cache to use for
     *                                      this dataobject.
     */
    public function setFlushableCache(SwatDBCacheNsFlushable $cache)
    {
        $this->flushable_cache = $cache;
    }



    /**
     * Gets the name-spaces that should be flushed for this dataobject.
     *
     * @return array An array of name-spaces that should be flushed.
     */
    public function getCacheNamespaces()
    {
        return [];
    }



    /**
     * Gets all available name-spaces that should be flushed for this dataobject
     * ignoring any logic attempting to be smart about namespace.
     *
     * @return array An array of name-spaces that should be flushed.
     */
    public function getAvailableCacheNamespaces()
    {
        return [];
    }



    /**
     * Flushes the cache name-spaces for this object.
     *
     * @param array $ns_array An optional array of name-spaces to flush.
     *                        If no name-spaces are specified,
     *                        {@link SwatDBDataObject::getCacheNamespaces()} is
     *                        used to get the array of name-spaces.
     *
     * @see SwatDBDataObject::setFlushableCache()
     * @see SwatDBDataObject::getCacheNamespaces()
     */
    public function flushCacheNamespaces($ns_array = null)
    {
        if ($ns_array === null) {
            $ns_array = $this->getCacheNamespaces();
        }

        if ($this->flushable_cache instanceof SwatDBCacheNsFlushable) {
            $ns_array = array_unique($ns_array);
            foreach ($ns_array as $ns) {
                $this->flushable_cache->flushNs($ns);
            }
        }
    }



    /**
     * Flushes all possible cache name-spaces for this object.
     *
     * @see SwatDBDataObject::setFlushableCache()
     * @see SwatDBDataObject::getAvailableCacheNamespaces()
     * @see SwatDBDataObject::flushCacheNamespaces()
     */
    public function flushAvailableCacheNamespaces()
    {
        $namespaces = $this->getAvailableCacheNamespaces();
        $this->flushCacheNamespaces($namespaces);
    }


    // serialization


    public function serialize(): string
    {
        return serialize($this->__serialize());
    }



    public function unserialize(string $data): void
    {
        $data = unserialize($data);
        $this->__unserialize($data);
    }



    public function __serialize(): array
    {
        $data = [];

        // unset subdataobjects that are not to be serialized
        $serializable_sub_data_objects = $this->getSerializableSubDataObjects();
        $unset_objects = [];
        foreach ($this->sub_data_objects as $name => $object) {
            if (!in_array($name, $serializable_sub_data_objects)) {
                $unset_objects[$name] = $this->getSubDataObject($name);
                $this->unsetSubDataObject($name);
            }
        }

        foreach ($this->getSerializablePrivateProperties() as $property) {
            $data[$property] = $this->$property;
        }

        $reflector = new ReflectionObject($this);
        foreach ($reflector->getProperties() as $property) {
            if ($property->isPublic() && !$property->isStatic()) {
                $name = $property->getName();
                $data[$name] = $this->$name;
            }
        }

        foreach ($this->getProtectedPropertyList() as $property => $accessor) {
            // We want to maintain what is internally stored in this object so
            // we don't want to use the getter. What the getter returns
            // publicly may be different than what we have internally.
            $data[$property] = $this->$property;
        }

        // restore unset sub-dataobjects on this object
        foreach ($unset_objects as $name => $object) {
            $this->setSubDataObject($name, $object);
        }

        return $data;
    }



    public function __unserialize(array $data): void
    {
        $this->wakeup();
        $this->init();

        // Ignore properties that shouldn't have been serialized. These
        // can be removed in the future.
        $ignored_properties = [
            'internal_property_autosave',
            'internal_property_accessible',
            'internal_property_classes',
            'date_properties',
        ];

        foreach ($data as $property => $value) {
            if ($property === 'internal_properties') {
                // merge with null properties from init() so that newly
                // defined properties work on old serialized data.
                $this->$property = array_merge($this->$property, $value);
            } elseif (!isset($ignored_properties[$property])) {
                $this->$property = $value;
            }
        }
    }



    public function marshall(array $tree = [])
    {
        $data = [];

        // specified tree for sub-data-objects
        foreach ($tree as $key => $value) {
            if (is_array($value)) {
                $tree = $value;
            } else {
                $key = $value;
                $tree = [];
            }

            if ($this->hasSubDataObject($key)) {
                $sub_data_object = $this->getSubDataObject($key);
                if ($sub_data_object instanceof SwatDBMarshallable) {
                    // need to save class name here because magic loaders
                    // have completely dynamic return classes.
                    $data['sub_data_objects'][$key] = [
                        $sub_data_object::class,
                        $sub_data_object->marshall($tree),
                    ];
                } elseif (is_scalar($sub_data_object)) {
                    $data['sub_data_objects'][$key] = $sub_data_object;
                } else {
                    throw new SwatDBMarshallException(
                        sprintf(
                            'Unable to marshall requested property "%s" ' .
                                'for object of class %s.',
                            $key,
                            static::class,
                        ),
                    );
                }
            }
        }

        // private properties sans sub-data-objects property
        $private_properties = $this->getSerializablePrivateProperties();
        $private_properties = array_diff($private_properties, [
            'sub_data_objects',
        ]);
        foreach ($private_properties as $property) {
            $data[$property] = $this->$property;
        }

        // public properties
        $reflector = new ReflectionObject($this);
        foreach ($reflector->getProperties() as $property) {
            if ($property->isPublic() && !$property->isStatic()) {
                $name = $property->getName();
                $data[$name] = $this->$name;
            }
        }

        foreach ($this->getProtectedPropertyList() as $property => $accessor) {
            // We want to maintain what is internally stored in this object so
            // we don't want to use the getter. What the getter returns
            // publicly may be different than what we have internally.
            $data[$property] = $this->$property;
        }

        return $data;
    }



    public function unmarshall(array $data = [])
    {
        // public properties
        $reflector = new ReflectionObject($this);
        foreach ($reflector->getProperties() as $property) {
            if ($property->isPublic() && !$property->isStatic()) {
                $name = $property->getName();
                if (isset($data[$name])) {
                    $this->$name = $data[$name];
                } else {
                    $this->$name = null;
                }
            }
        }

        foreach ($this->getProtectedPropertyList() as $property => $accessor) {
            if (isset($data[$property])) {
                // We want to maintain what is internally stored in this object
                // so we don't want to use the getter. What the setter sets may
                // be different than what we have internally.
                $this->$property = $data[$property];
            }
        }

        // private properties sans sub-data-objects property
        $private_properties = $this->getSerializablePrivateProperties();
        $private_properties = array_diff($private_properties, [
            'sub_data_objects',
        ]);

        foreach ($private_properties as $property) {
            if (isset($data[$property])) {
                $this->$property = $data[$property];
            } else {
                $this->$property = null;
            }
        }

        // restore sub-data-objects;
        if (isset($data['sub_data_objects'])) {
            foreach ($data['sub_data_objects'] as $key => $object_data) {
                if (is_array($object_data)) {
                    $class_name = $object_data[0];
                    if (is_subclass_of($class_name, 'SwatDBMarshallable')) {
                        $object_data = $object_data[1];
                        $object = new $class_name();
                        $object->unmarshall($object_data);
                        $this->sub_data_objects[$key] = $object;
                    }
                } else {
                    $this->sub_data_objects[$key] = $object_data;
                }
            }
        }
    }



    protected function wakeup()
    {
        $this->class_map = SwatDBClassMap::instance();
    }



    protected function getSerializableSubDataObjects()
    {
        return [];
    }



    protected function getSerializablePrivateProperties()
    {
        return [
            'table',
            'id_field',
            'sub_data_objects',
            'property_hashes',
            'internal_properties',
            'loaded_from_database',
            'read_only',
        ];
    }

}
