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
	 * @var MDB2
	 */
	protected $db = null;
	
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

	protected function init()
	{
	}

	protected function registerInternalField($name)
	{
		$this->internal_fields[$name] = null;
	}

	protected function getInternalValue($name)
	{
		return $this->internal_fields[$name];
	}

	/**
	 * @param MDB2 $db
	 */
	public function setDatabase($db)
	{
		$this->db = $db;
	}

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

	/**
	 * Loads this object's properties from the database given an id
	 *
	 * @param mixed id the id of the database row to set this object's
	 *               properties with.
	 */
	public function loadFromDB($id) {
		$this->checkDB();
		$this->loadFromDBInternal($id);
	}

	/**
	 * Loads this object's properties from the database given an id
	 *
	 * @param mixed id the id of the database row to set this object's
	 *               properties with.
	 */
	protected function loadFromDBInternal($id) {

	}

	/**
	 * Saves this object to the database
	 *
	 * Only modified properties are updated.
	 */
	public function saveToDB() {
		$this->checkDB();
		$this->saveToDBInternal();
	}

	/**
	 * Saves this object to the database
	 *
	 * Only modified properties are updated.
	 */
	protected function saveToDBInternal() {

	}

	public function __get($key) {
		if (isset($this->sub_data_objects[$key]))
			return $this->sub_data_objects[$key];

		$loader_method = 'load'.str_replace(' ', '', ucwords(strtr($key, '_', ' ')));

		if (method_exists($this, $loader_method)) {
			$this->checkDB();
			$this->sub_data_objects[$key] = call_user_func(array($this, $loader_method));
			return $this->sub_data_objects[$key];
		}
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

	private function checkDB()
	{
		if ($this->db === null)
			throw new SwatDBException('No database available to this dataobject. Call the setDatabase method.');
	}
}

?>
