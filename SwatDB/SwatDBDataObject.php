<?php

require_once 'MDB2.php';
require_once 'Swat/SwatObject.php';
require_once 'Swat/SwatDate.php';
require_once 'SwatDB/SwatDB.php';
require_once 'SwatDB/exceptions/SwatDBException.php';

/**
 * All public properties correspond to database fields
 *
 * @package   SwatDB
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDBDataObject extends SwatObject
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
	private $internal_fields = array();
	
	/**
	 * @var array
	 */
	private $internal_field_classes = array();

	/**
	 * @var array
	 */
	private $date_fields = array();
	
	// }}}
	// {{{ protected properties

	/**
	 * @var MDB2
	 */
	protected $db = null;
	
	protected $table = null;
	protected $id_field = null;
	
	// }}}
	// {{{ public function __construct()

	/**
	 * @param mixed $data
	 */
	public function __construct($data = null)
	{
		$this->init();

		if ($data !== null)
			$this->initFromRow($data);

		$this->generatePropertyHashes();
	}

	// }}}
	// {{{ public function setDatabase()

	/**
	 * @param MDB2 $db
	 */
	public function setDatabase($db)
	{
		$this->db = $db;
	}

	// }}}
	// {{{ public function setTable()

	/**
	 * @param database $table
	 */
	public function setTable($table)
	{
		$this->table = $table;
	}

	// }}}
	// {{{ public function loadFromDB()

	/**
	 * Loads this object's properties from the database given an id
	 *
	 * @param mixed $id the id of the database row to set this object's
	 *               properties with.
	 *
	 * @return boolean whether data was sucessfully loaded.
	 */
	public function loadFromDB($id)
	{
		$this->checkDB();
		$row = $this->loadFromDBInternal($id);

		if ($row === null)
			return false;

		$this->initFromRow($row);
		return true;
	}

	// }}}
	// {{{ public function saveToDB()

	/**
	 * Saves this object to the database
	 *
	 * Only modified properties are updated.
	 */
	public function saveToDB() {
		$this->checkDB();
		$this->saveToDBInternal();
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
		$property_array = $this->getPublicProperties();

		foreach ($property_array as $name => $value) {
			$hashed_value = md5(serialize($value));
			if (strcmp($hashed_value, $this->property_hashes[$name]) != 0)
				return false;
		}
		
		return true;
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
		$property_array = $this->getPublicProperties();
		$modified_properties = array();

		foreach ($property_array as $name => $value) {
			$hashed_value = md5(serialize($value));
			if (strcmp($hashed_value, $this->property_hashes[$name]) != 0)
				$modified_properties[$name] = $value;
		}

		return $modified_properties;
	}

	// }}}
	// {{{ public function __get()

	public function __get($key)
	{
		if (isset($this->sub_data_objects[$key]))
			return $this->sub_data_objects[$key];

		$loader_method = 'load'.str_replace(' ', '', ucwords(strtr($key, '_', ' ')));

		if (method_exists($this, $loader_method)) {
			$this->checkDB();
			$this->sub_data_objects[$key] = call_user_func(array($this, $loader_method));
			return $this->sub_data_objects[$key];

		} elseif ($this->hasInternalValue($key)) {
			$id = $this->getInternalValue($key);

			if ($id === null)
				return null;

			if (array_key_exists($key, $this->internal_field_classes)) {
				$class = $this->internal_field_classes[$key];

				if (class_exists($class)) {
			        $object = new $class();
					$object->setDatabase($this->db);
					$object->loadFromDB($id);
					$this->sub_data_objects[$key] = $object;
					return $object;
				} else {
					// TODO: throw bad class exception
				}
			}
		}

		throw new SwatDBException("A property named '$key' does not exist on this dataobject.  If the property corresponds directly to a database field it should be added as a public property of this data object.  If the property should access a sub-dataobject, either specify a class when registering the internal field named '$key' or define a custom loader method named '$loader_method()'.");
	}

	// }}}
	// {{{ public function __set()

	public function __set($key, $value)
	{
		$loader_method = 'load'.str_replace(' ', '', ucwords(strtr($key, '_', ' ')));

		if (method_exists($this, $loader_method)) {
			$this->sub_data_objects[$key] = $value;
		} elseif ($this->hasInternalValue($key)) {
			if (is_object($value)) {
				$this->sub_data_objects[$key] = $value;
				$this->setInternalValue($key, $value->getId());
			} else {
				$this->setInternalValue($key, $value);
			}
		} else {
			throw new SwatDBException("A property named '$key' does not exist on this dataobject.  If the property corresponds directly to a database field it should be added as a public property of this data object.  If the property should access a sub-dataobject, specify a class when registering the internal field named '$key'.");
		}
	}

	// }}}
	// {{{ protected function loadFromDBInternal()

	/**
	 * Loads this object's properties from the database given an id
	 *
	 * @param mixed $id the id of the database row to set this object's
	 *               properties with.
	 *
	 * @return object data row or null.
	 */
    protected function loadFromDBInternal($id)
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
	// {{{ protected function saveToDBInternal()

	/**
	 * Saves this object to the database
	 *
	 * Only modified properties are updated.
	 */
	protected function saveToDBInternal() {
		if ($this->table === null || $this->id_field === null)
			return;

		$id_field = new SwatDBField($this->id_field, 'integer');

		if (!property_exists($this, $id_field->name))
			return;

		$modified_properties = $this->getModifiedProperties();

		if (count($modified_properties) == 0)
			return;

		$id_ref = $id_field->name;
		$id = $this->$id_ref;

		$fields = array();
		$values = array();

		foreach ($this->getModifiedProperties() as $name => $value) {
			$type = $this->guessType($name, $value);

			if ($type == 'date')
				$value = $value->getDate();

			$fields[] = sprintf('%s:%s', $type, $name);
			$values[$name] = $value;
		}

		if ($id === null) {
			$this->$id_ref = 
				SwatDB::insertRow($this->db, $this->table, $fields, $values, $id_field->__toString());
		} else {
			SwatDB::updateRow($this->db, $this->table, $fields, $values, $id_field->__toString(), $id);
		}
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
			throw new SwatDBException('Attempting to initialize dataobject with a null row.');

		$property_array = $this->getPublicProperties();

		if (is_object($row))
			$row = get_object_vars($row);

		foreach ($property_array as $name => $value) {
			if (isset($row[$name])) {
				if (in_array($name, $this->date_fields) && $row[$name] !== null)
					$this->$name = new SwatDate($row[$name]);
				else
					$this->$name = $row[$name];
			}
		}

		foreach ($this->internal_fields as $name => $value) {
			if (isset($row[$name]))
				$this->internal_fields[$name] = $row[$name];
		}
	}

	// }}}
	// {{{ protected function getPropertyHashes()

	/**
	 * Generates the set of md5 hashes for this data object
	 *
	 * The md5 hashes represent all the public properties of this object and
	 * are used to tell if a property has been modified.
	 */
	protected function generatePropertyHashes()
	{
		$property_array = $this->getPublicProperties();

		foreach ($property_array as $name => $value) {
			$hashed_value = md5(serialize($value));
			$this->property_hashes[$name] = $hashed_value;
		}
	}

	// }}}
	// {{{ protected function init()

	protected function init()
	{
	}

	// }}}
	// {{{ protected function registerDateField()

	protected function registerDateField($name)
	{
		$this->date_fields[] = $name;
	}

	// }}}
	// {{{ protected function registerInternalField()

	protected function registerInternalField($name, $class = null)
	{
		$this->internal_fields[$name] = null;

		if ($class === null)
			unset($this->internal_field_classes[$name]);
		else
			$this->internal_field_classes[$name] = $class;
	}

	// }}}
	// {{{ public function getInternalValue()

	public function getInternalValue($name)
	{
		if (array_key_exists($name, $this->internal_fields))
			return $this->internal_fields[$name];
		else
			return null;
	}

	// }}}
	// {{{ protected function setInternalValue()

	protected function setInternalValue($name, $value)
	{
		if (array_key_exists($name, $this->internal_fields))
			$this->internal_fields[$name] = $value;
	}

	// }}}
	// {{{ protected function hasInternalValue()

	protected function hasInternalValue($name)
	{
		return array_key_exists($name, $this->internal_fields);
	}

	// }}}
	// {{{ protected function getId()

	protected function getId()
	{
		$id_field = new SwatDBField($this->id_field, 'integer');
		$temp = $id_field->name;
		return $this->$temp;
	}

	// }}}
	// {{{ protected function checkDB()

	protected function checkDB()
	{
		if ($this->db === null)
			throw new SwatDBException('No database available to this dataobject. Call the setDatabase method.');
	}

	// }}}
	// {{{ private function getPublicProperties()

	private function getPublicProperties()
	{
		$property_array = get_object_vars($this);
		unset($property_array['db']);
		unset($property_array['table']);
		unset($property_array['id_field']);
		$property_array = array_merge($property_array, $this->internal_fields);
		return $property_array;
	}

	// }}}
	// {{{ public function __toString()

	/**
	 * Gets this object as a string
	 *
	 * @see SwatObject::__toString()
	 * @return string this object represented as a string.
	 */
	public function __toString()
	{
		// prevent printing of MDB2 object for dataobjects
		$db = $this->db;
		$this->db = null;

		return parent::__toString();

		// set db back again
		$this->db = $db;
	}

	// }}}
}

?>
