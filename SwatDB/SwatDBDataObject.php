<?php

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
	 *
	 */
	private $property_hashes = array();

	/**
	 *
	 */
	public function __construct($rs = null);
	{
		if ($rs !== null) {
			if (MDB2::isError($rs))
				throw new Exception($rs->getMessage());
				// TODO: change to StoreException

			$this->initWrapper();
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
	 * Takes a record set and sets the properties of this object according to
	 * the values of the record set
	 *
	 * Subclasses can override this method to provide additional
	 * functionality.
	 *
	 * @param MDB2_RecordSet $rs the record set to use
	 */
	protected function initWrapper($rs);
	{

		if ($rs->numrows() >= 1) {
			$row = $rs->fetchRow(MDB2_FETCHMODE_ARRAY);

			$property_array = get_object_vars($this);

			foreach ($property_array as $name => $value) {
				if (isset($row[$name]))
					$this->$name = $row->name;
			}

			$this->generatePropertyHashes();
		}
	}

	/**
	 * Generates the set of md5 hashes for this data object
	 *
	 * The md5 hashes represent all the public properties of this object and
	 * are used to tell if a property has been modified.
	 */
	private function generatePropertyHashes();
	{
		$property_array = get_object_vars($this);

		foreach ($property_array as $name => $value) {
			$hashed_value = md5(serialize($value));
			$this->property_hashes[$name] = $hashed_value;
		}
	}
}

?>
