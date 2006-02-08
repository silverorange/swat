<?php

require_once 'MDB2.php';
require_once 'SwatDB/exceptions/SwatDBException.php';

/**
 * All public properties correspond to database fields
 *
 * @package   SwatDB
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDBDataObject
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
		$property_array = get_object_vars($this);

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
		$property_array = get_object_vars($this);
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

	public function __get($key) {
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
			        $object = new $key();
					$object->setDatabase($this->db);
					$object->loadFromDB($id);
					return $object;
				}
			}
		}

		throw new SwatDBException("A property named '$key' does not exist on this dataobject.  If the property corresponds directly to a database field it should be added as a public property of this data object.  If the property should access a sub-dataobject, either specify a class when registering the internal field named '$key' or define a custom loader method named '$loader_method()'.");
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

		$property_array = get_object_vars($this);

		if (is_object($row))
			$row = get_object_vars($row);

		foreach ($property_array as $name => $value) {
			if (isset($row[$name]))
				$this->$name = $row[$name];
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
		$property_array = get_object_vars($this);

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
	// {{{ protected function registerInternalField()

	protected function registerInternalField($name, $class = null)
	{
		$this->internal_fields[$name] = null;

		if ($class !== null)
			$this->internal_field_classes[$name] = $class;
	}

	// }}}
	// {{{ protected function getInternalValue()

	protected function getInternalValue($name)
	{
		if (array_key_exists($name, $this->internal_fields))
			return $this->internal_fields[$name];
		else
			return null;
	}

	// }}}
	// {{{ protected function hasInternalValue()

	protected function hasInternalValue($name)
	{
		return array_key_exists($name, $this->internal_fields);
	}

	// }}}
	// {{{ private function checkDB()

	private function checkDB()
	{
		if ($this->db === null)
			throw new SwatDBException('No database available to this dataobject. Call the setDatabase method.');
	}

	// }}}
}

?>
