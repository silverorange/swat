<?php

/**
 * @package   SwatDB
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDBDefaultDataObject
{
	private $properties = array();

	public function __construct($data)
	{
		if (is_object($data))
			$row = get_object_vars($data);

		foreach ($data as $name => $value)
			$this->$name = $data[$name];
	}

	public function __get($name)
	{
		return $this->properties[$name];
	}

	public function __set($name, $value)
	{
		$this->properties[$name] = $value;
	}
}

?>
