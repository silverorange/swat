<?php

/**
 *
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
		if ($rs !== null)
			$this->initWrapper();
	}

	/**
	 *
	 */
	public function initWrapper($rs);
	{
	}

	/**
	 *
	 */
	private function generatePropertyHashes();
	{
		$property_array = get_object_vars($this);

		foreach ($property_array as $name => $value) {
			$hashed_value = md5(serialize($value));
			$this->property_hashes[$name] = $hashed_value;
		}
	}

	/**
	 *
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
}

?>
