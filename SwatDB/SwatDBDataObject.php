<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatObject.php';
require_once 'Swat/SwatDate.php';
require_once 'Swat/exceptions/SwatClassNotFoundException.php';
require_once 'SwatDB/SwatDB.php';
require_once 'SwatDB/SwatDBClassMap.php';
require_once 'SwatDB/SwatDBTransaction.php';
require_once 'SwatDB/SwatDBRecordable.php';
require_once 'SwatDB/exceptions/SwatDBException.php';

/**
 * All public properties correspond to database fields
 *
 * @package   SwatDB
 * @copyright 2005-2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDBDataObject extends SwatObject
	implements Serializable, SwatDBRecordable
{
	// {{{ private properties

	/**
	 * @var array
	 */
	private $property_hashes = array();

	/**
	 * @var array
	 */
	private $sub_data_objects = array();

	/**
	 * @var array
	 */
	private $internal_properties = array();

	/**
	 * @var array
	 */
	private $internal_property_autosave = array();

	/**
	 * @var array
	 */
	private $internal_property_accessible = array();

	/**
	 * @var array
	 */
	private $internal_property_classes = array();

	/**
	 * @var array
	 */
	private $date_properties = array();

	/**
	 * @var boolean
	 */
	private $loaded_from_database = false;

	// }}}
	// {{{ protected properties

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

	// }}}
	// {{{ public function __construct()

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

		if ($data !== null)
			$this->initFromRow($data);

		$this->generatePropertyHashes();
	}

	// }}}
	// {{{ public function setTable()

	/**
	 * @param string $table Database table
	 */
	public function setTable($table)
	{
		$this->table = $table;
	}

	// }}}
	// {{{ public function getModifiedProperties()

	/**
	 * Gets a list of all the modified properties of this object
	 *
	 * @return array an array of modified properties and their values in the
	 *                form of: name => value
	 */
	public function getModifiedProperties()
	{
		if ($this->read_only)
			return array();

		$property_array = $this->getProperties();
		$modified_properties = array();

		foreach ($property_array as $name => $value) {
			$hashed_value = md5(serialize($value));
			if (strcmp($hashed_value, $this->property_hashes[$name]) != 0)
				$modified_properties[$name] = $value;
		}

		return $modified_properties;
	}

	// }}}
	// {{{ public function __toString()

	/**
	 * Gets a string representation of this data-object
	 *
	 * @return string this data-object represented as a string.
	 *
	 * @see SwatObject::__toString()
	 */
	public function __toString()
	{
		// prevent printing of MDB2 object for dataobjects
		$db = $this->db;
		$this->db = null;

		$modified_properties = $this->getModifiedProperties();
		$properties = $this->getPublicProperties();

		foreach ($this->getSerializableSubDataObjects() as $name) {
			if (!isset($properties[$name]))
				$properties[$name] = null;
		}

		ob_start();
		printf('<h3>%s</h3>', get_class($this));
		echo $this->isModified() ? '(modified)' : '(not modified)', '<br />';
		foreach ($properties as $name => $value) {
			if ($this->hasSubDataObject($name))
				$value = $this->getSubDataObject($name);

			$modified = isset($modified_properties[$name]);

			if ($value instanceof SwatDBRecordable) {
				$modified = $value->isModified();
				$value = get_class($value);
			}

			if ($value === null)
				$value = '<null>';

			printf("%s = %s%s<br />\n",
				$name, $value, $modified ? ' (modified)' : '');
		}
		/*
		$reflector = new ReflectionClass(get_class($this));
		foreach ($reflector->getMethods() as $method) {
			if ($method->isProtected()) {
				$name = $method->getName();
				if (substr($name, 0, 4) === 'load')
					echo $name;
			}
		}
		*/
		$string = ob_get_clean();


		// set db back again
		$this->db = $db;

		return $string;
	}

	// }}}
	// {{{ public function getInternalValue()

	public function getInternalValue($name)
	{
		if (array_key_exists($name, $this->internal_properties))
			return $this->internal_properties[$name];
		else
			return null;
	}

	// }}}
	// {{{ public function hasInternalValue()

	public function hasInternalValue($name)
	{
		return array_key_exists($name, $this->internal_properties);
	}

	// }}}
	// {{{ public function duplicate()

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
		$class = get_class($this);
		$new_object = new $class();
		$id_field = new SwatDBField($this->id_field, 'integer');

		$properties = $this->getProperties();
		foreach ($properties as $name => $value)
			if ($name !== $id_field->name)
				$new_object->$name = $value;

		foreach ($this->internal_property_autosave as $name => $autosave) {
			if ($autosave && $this->hasSubDataObject($name)) {
				$object = $this->getSubDataObject($name);
				$new_object->$name = $object->duplicate();
			}
		}

		$new_object->setDatabase($this->db);

		return $new_object;
	}

	// }}}
	// {{{ protected function setInternalValue()

	protected function setInternalValue($name, $value)
	{
		if (array_key_exists($name, $this->internal_properties))
			$this->internal_properties[$name] = $value;
	}

	// }}}
	// {{{ protected function init()

	protected function init()
	{
	}

	// }}}
	// {{{ protected function registerDateProperty()

	protected function registerDateProperty($name)
	{
		$this->date_properties[] = $name;
	}

	// }}}
	// {{{ protected function registerInternalProperty()

	protected function registerInternalProperty($name, $class = null,
		$autosave = false, $accessible = true)
	{
		$this->internal_properties[$name] = null;
		$this->internal_property_autosave[$name] = $autosave;
		$this->internal_property_accessible[$name] = $accessible;
		$this->internal_property_classes[$name] = $class;
	}

	// }}}
	// {{{ protected function initFromRow()

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
		if ($row === null)
			throw new SwatDBException(
				'Attempting to initialize dataobject with a null row.');

		$property_array = $this->getPublicProperties();

		if (is_object($row))
			$row = get_object_vars($row);

		foreach ($property_array as $name => $value) {
			if (isset($row[$name])) {
				if (in_array($name, $this->date_properties) && $row[$name] !== null)
					$this->$name = new SwatDate($row[$name]);
				else
					$this->$name = $row[$name];
			}
		}

		foreach ($this->internal_properties as $name => $value) {
			if (isset($row[$name]))
				$this->internal_properties[$name] = $row[$name];
		}

		$this->loaded_from_database = true;
	}

	// }}}
	// {{{ protected function generatePropertyHashes()

	/**
	 * Generates the set of md5 hashes for this data object
	 *
	 * The md5 hashes represent all the public properties of this object and
	 * are used to tell if a property has been modified.
	 */
	protected function generatePropertyHashes()
	{
		if ($this->read_only)
			return;

		$property_array = $this->getProperties();

		foreach ($property_array as $name => $value) {
			$hashed_value = md5(serialize($value));
			$this->property_hashes[$name] = $hashed_value;
		}
	}

	// }}}
	// {{{ protected function getId()

	protected function getId()
	{
		if ($this->id_field === null)
			throw new SwatDBException(
				sprintf('Property $id_field is not set for class %s.',
				get_class($this)));

		$id_field = new SwatDBField($this->id_field, 'integer');
		$temp = $id_field->name;
		return $this->$temp;
	}

	// }}}
	// {{{ protected function getSubDataObject()

	protected function getSubDataObject($name)
	{
		return $this->sub_data_objects[$name];
	}

	// }}}
	// {{{ protected function setSubDataObject()

	protected function setSubDataObject($name, $value)
	{
		// Can't add type-hinting because dataobjects may not be dataobjects.
		// Go figure.
		$this->sub_data_objects[$name] = $value;
	}

	// }}}
	// {{{ protected function unsetSubDataObject()

	protected function unsetSubDataObject($name)
	{
		unset($this->sub_data_objects[$name]);
	}

	// }}}
	// {{{ protected function hasSubDataObject()

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
		return (isset($this->sub_data_objects[(string)$key]));
	}

	// }}}
	// {{{ private function getPublicProperties()

	/**
	 * Gets the public properties of this data-object
	 *
	 * Public properties should correspond directly to database fields.
	 *
	 * @return array a reference to an associative array of public properties
	 *                of this data-object. The array is of the form
	 *                'property name' => 'property value'.
	 */
	private function &getPublicProperties()
	{
		$public_properties = array();
		$reflector = new ReflectionClass(get_class($this));
		foreach ($reflector->getProperties() as $property)
			if ($property->isPublic() && !$property->isStatic())
				$public_properties[$property->getName()] =
					$property->getValue($this);

		return $public_properties;
	}

	// }}}
	// {{{ private function getProperties()

	/**
	 * Gets all the modifyable properties of this data-object
	 *
	 * This includes the public properties that correspond to database fields
	 * and the internal values that also correspond to database fields.
	 *
	 * @return array a reference to an associative array of properties of this
	 *                data-object. The array is of the form
	 *                'property name' => 'property value'.
	 */
	private function &getProperties()
	{
		$property_array = $this->getPublicProperties();
		$property_array = array_merge($property_array,
			$this->internal_properties);

		return $property_array;
	}

	// }}}
	// {{{ private function __get()

	private function __get($key)
	{
		$value = $this->getUsingLoaderMethod($key);

		if ($value === false)
			$value = $this->getUsingInternalProperty($key);

		if ($value === false) {
			$loader_method = $this->getLoaderMethod($key);
			throw new SwatDBException(sprintf("A property named '%s' does not ".
				"exist on the %s data-object. If the property corresponds ".
				"directly to a database field it should be added as a public ".
				"property of this data object. If the property should access ".
				"a sub-data-object, either specify a class when registering ".
				"the internal property named '%s' or define a custom loader ".
				"method named '%s()'.",
				$key, get_class($this), $key, $loader_method));
		}

		return $value;
	}

	// }}}
	// {{{ private function __set()

	private function __set($key, $value)
	{
		if (method_exists($this, $this->getLoaderMethod($key))) {

			if ($value === null) {
				$this->unsetSubDataObject($key);
			} else {
				$this->setSubDataObject($key, $value);
			}

		} elseif ($this->hasInternalValue($key) &&
			$this->internal_property_accessible[$key]) {

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
				"A property named '{$key}' does not exist on this ".
				'dataobject.  If the property corresponds directly to '.
				'a database field it should be added as a public property '.
				'of this data object.  If the property should access a '.
				'sub-dataobject, specify a class when registering the '.
				"internal field named '{$key}'.");
		}
	}

	// }}}
	// {{{ private function __isset()

	private function __isset($key)
	{
		return
			(method_exists($this, $this->getLoaderMethod($key))) ||
			($this->hasInternalValue($key) &&
			$this->internal_property_accessible[$key]);
	}

	// }}}
	// {{{ private function getLoaderMethod()

	private function getLoaderMethod($key)
	{
		$loader_method = 'load'.str_replace(' ', '',
			ucwords(str_replace('_', ' ', $key)));

		return $loader_method;
	}

	// }}}
	// {{{ private function getUsingLoaderMethod()

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
				$this->setSubDataObject($key,
					call_user_func(array($this, $loader_method)));

				$value = $this->getSubDataObject($key);
			}
		}

		return $value;
	}

	// }}}
	// {{{ private function getUsingInternalProperty()

	private function getUsingInternalProperty($key)
	{
		$value = false;

		if (array_key_exists($key, $this->internal_property_accessible) &&
			$this->internal_property_accessible[$key]) {

			if ($this->hasSubDataObject($key)) {
				// return loaded sub-dataobject
				$value = $this->getSubDataObject($key);
			} elseif ($this->hasInternalValue($key)) {
				$value = $this->getInternalValue($key);

				if ($value !== null &&
					isset($this->internal_property_classes[$key])) {

					// autoload sub-dataobject
					$class = $this->internal_property_classes[$key];

					if (!class_exists($class)) {
						throw new SwatClassNotFoundException(sprintf(
							"Class '%s' registered for internal property '%s' ".
							"does not exist.",
							$class, $key), 0, $class);
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

	// }}}

	// database loading and saving
	// {{{ public function setDatabase()

	/**
	 * Sets the database driver for this data-object
	 *
	 * The database is automatically set for all recordable sub-objects of this
	 * data-object.
	 *
	 * @param MDB2_Driver_Common $db the database driver to use for this
	 *                                data-object.
	 */
	public function setDatabase(MDB2_Driver_Common $db)
	{
		$this->db = $db;
		$serializable_sub_data_objects = $this->getSerializableSubDataObjects();

		foreach ($this->sub_data_objects as $name => $object) {
			if (($object instanceof SwatDBRecordable) &&
				in_array($name, $serializable_sub_data_objects)) {
				$object->setDatabase($db);
			}
		}
	}

	// }}}
	// {{{ public function save()

	/**
	 * Saves this object to the database
	 *
	 * Only modified properties are updated.
	 */
	public function save()
	{
		if ($this->read_only)
			throw new SwatDBException('This dataobject was loaded read-only '.
				'and cannot be saved.');

		$this->checkDB();

		$transaction = new SwatDBTransaction($this->db);
		try {
			$property_hashes = $this->property_hashes;
			$this->saveInternalProperties();
			$this->saveInternal();
			$this->generatePropertyHashes();
			$this->saveSubDataObjects();

			// Save again in-case values have been changed in saveSubDataObjects()
			if ($this->id_field !== null)
				$this->saveInternal();

			$transaction->commit();
		} catch (Exception $e) {
			$this->property_hashes = $property_hashes;
			$transaction->rollback();
			throw $e;
		}

		$this->generatePropertyHashes();
	}

	// }}}
	// {{{ public function load()

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

		if ($row === null)
			return false;

		$this->initFromRow($row);
		$this->generatePropertyHashes();
		return true;
	}

	// }}}
	// {{{ public function delete()

	/**
	 * Deletes this object from the database
	 */
	public function delete()
	{
		$this->checkDB();
		$this->deleteInternal();
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
		if ($this->read_only)
			return false;

		$property_array = $this->getProperties();

		foreach ($property_array as $name => $value) {
			$hashed_value = md5(serialize($value));
			if (strcmp($hashed_value, $this->property_hashes[$name]) != 0)
				return true;
		}

		foreach ($this->sub_data_objects as $name => $object)
			if ($object instanceof SwatDBRecordable && $object->isModified())
				return true;

		return false;
	}

	// }}}
	// {{{ protected function checkDB()

	protected function checkDB()
	{
		if ($this->db === null)
			throw new SwatDBException(
				sprintf('No database available to this dataobject (%s). '.
					'Call the setDatabase method.', get_class($this)));
	}

	// }}}
	// {{{ protected function loadInternal()

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

			$sql = sprintf($sql,
				$this->table,
				$id_field->name,
				$this->db->quote($id, $id_field->type));

			$rs = SwatDB::query($this->db, $sql, null);
			$row = $rs->fetchRow(MDB2_FETCHMODE_ASSOC);

			return $row;
		}
		return null;
	}

	// }}}
	// {{{ protected function saveInternal()

	/**
	 * Saves this object to the database
	 *
	 * Only modified properties are updated.
	 */
	protected function saveInternal()
	{
		if ($this->table === null) {
			trigger_error(
				sprintf('No table defined for %s', get_class($this)),
				E_USER_NOTICE);

			return;
		}

		if ($this->id_field === null) {
			if (!$this->loaded_from_database) {
				$this->saveNewBinding();
				return;
			}

			trigger_error(
				sprintf('No id_field defined for %s', get_class($this)),
				E_USER_NOTICE);

			return;
		}

		$id_field = new SwatDBField($this->id_field, 'integer');

		if (!property_exists($this, $id_field->name)) {
			trigger_error(
				sprintf("The id_field '%s' is not defined for %s",
					$id_field->name, get_class($this)),
				E_USER_NOTICE);

			return;
		}

		$modified_properties = $this->getModifiedProperties();

		if (count($modified_properties) == 0)
			return;

		$id_ref = $id_field->name;
		$id = $this->$id_ref;

		$fields = array();
		$values = array();

		foreach ($this->getModifiedProperties() as $name => $value) {
			if ($name === $id_field->name)
				continue;

			$type = $this->guessType($name, $value);

			if ($type == 'date')
				$value = $value->getDate();

			$fields[] = sprintf('%s:%s', $type, $name);
			$values[$name] = $value;
		}

		if ($id === null) {
			$this->$id_ref =
				SwatDB::insertRow($this->db, $this->table, $fields, $values,
					$id_field->__toString());
		} else {
			SwatDB::updateRow($this->db, $this->table, $fields, $values,
				$id_field->__toString(), $id);
		}
	}

	// }}}
	// {{{ protected function saveInternalProperties()

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

	// }}}
	// {{{ protected function saveSubDataObjects()

	protected function saveSubDataObjects()
	{
		foreach ($this->sub_data_objects as $name => $object) {
			$saver_method = 'save'.
				str_replace(' ', '', ucwords(strtr($name, '_', ' ')));

			if (method_exists($this, $saver_method))
				call_user_func(array($this, $saver_method));
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

	// }}}
	// {{{ protected function deleteInternal()

	/**
	 * Deletes this object from the database
	 */
	protected function deleteInternal()
	{
		if ($this->table === null || $this->id_field === null)
			return;

		$id_field = new SwatDBField($this->id_field, 'integer');

		if (!property_exists($this, $id_field->name))
			return;

		$id_ref = $id_field->name;
		$id = $this->$id_ref;

		if ($id !== null)
			SwatDB::deleteRow($this->db, $this->table,
				$id_field->__toString(), $id);
	}

	// }}}
	// {{{ protected function saveNewBinding()

	/**
	 * Saves a new binding object without an id to the database
	 *
	 * Only modified properties are saved. It is always inserted,
	 * never updated.
	 */
	protected function saveNewBinding()
	{
		$modified_properties = $this->getModifiedProperties();

		if (count($modified_properties) == 0)
			return;

		$fields = array();
		$values = array();

		foreach ($this->getModifiedProperties() as $name => $value) {
			$type = $this->guessType($name, $value);
			$fields[] = sprintf('%s:%s', $type, $name);
			$values[$name] = $value;
		}

		SwatDB::insertRow($this->db, $this->table, $fields, $values);
	}

	// }}}
	// {{{ protected function guessType()

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
			if ($value instanceof SwatDate)
				return 'date';
		case 'string':
		default:
			return 'text';
		}
	}

	// }}}

	// serialization
	// {{{ public function serialize()

	public function serialize()
	{
		$data = array();

		$serializable_sub_data_objects = $this->getSerializableSubDataObjects();
		foreach ($this->sub_data_objects as $name => $object)
			if (!in_array($name, $serializable_sub_data_objects))
				$this->unsetSubDataObject($name);

		foreach ($this->getSerializablePrivateProperties() as $property)
			$data[$property] = &$this->$property;

		$reflector = new ReflectionObject($this);
		foreach ($reflector->getProperties() as $property) {
			if ($property->isPublic()) {
				$name = $property->getName();
				$data[$name] = &$this->$name;
			}
		}

		return serialize($data);
	}

	// }}}
	// {{{ public function unserialize()

	public function unserialize($data)
	{
		$this->wakeup();
		$this->init();

		$data = unserialize($data);

		foreach ($data as $property => $value)
			$this->$property = $value;
	}

	// }}}
	// {{{ protected function wakeup()

	protected function wakeup()
	{
		$this->class_map = SwatDBClassMap::instance();
	}

	// }}}
	// {{{ protected function getSerializableSubDataObjects()

	protected function getSerializableSubDataObjects()
	{
		return array();
	}

	// }}}
	// {{{ protected function getSerializablePrivateProperties()

	protected function getSerializablePrivateProperties()
	{
		return array('table', 'id_field',
			'sub_data_objects', 'property_hashes', 'internal_properties',
			'internal_property_autosave', 'internal_property_classes',
			'internal_property_accessible', 'date_properties',
			'loaded_from_database');
	}

	// }}}
}

?>
