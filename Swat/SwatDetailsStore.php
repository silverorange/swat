<?php

require_once 'Swat/SwatObject.php';

/**
 * A data structure that can be used with the SwatDetailsView
 *
 * A new details store is empty by default unless is it initialized with
 * another object.
 *
 * @package   Swat
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDetailsStore extends SwatObject
{
	private $base_object = null;
	private $data = array();

	public function __construct($base_object = null)
	{
		$this->base_object = $base_object;
	}

	public function __get($name)
	{
		if (array_key_exists($name, $this->data))
			return $this->data[$name];

		if ($this->base_object !== null) {
			if (property_exists($this->base_object, $name))
				return $this->base_object->$name;

			if (method_exists($this->base_object, '__get'))
				return $this->base_object->$name;
		}

		throw new SwatInvalidPropertyException(
			"Property '$name' does not exist in details store.",
			0, $this, $name);
	}

	public function __set($name, $value)
	{
		$this->data[$name] = $value;
	}
}

?>
