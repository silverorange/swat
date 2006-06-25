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
	// {{{ private properties

	private $base_object = null;
	private $data = array();

	// }}}
	// {{{ public function __construct()

	public function __construct($base_object = null)
	{
		$this->base_object = $base_object;
	}

	// }}}
	// {{{ private function parsePath()

	private function parsePath($object, $path)
	{
		$pos = strpos($path, '.');
		$name = substr($path, 0, $pos);
		$rest = substr($path, $pos + 1);
		$sub_object = $object->$name;

		if ($sub_object === null)
			return null;
		elseif (strpos($rest, '.') === false)
			return $sub_object->$rest;
		else
			return $this->parsePath($sub_object, $rest);
	}

	// }}}
	// {{{ private function __get()

	private function __get($name)
	{
		if (strpos($name, '.') !== false)
			return $this->parsePath($this, $name);

		if (array_key_exists($name, $this->data))
			return $this->data[$name];

		if ($this->base_object !== null) {
			// TODO: use reflector to ensure this is public
			if (property_exists($this->base_object, $name))
				return $this->base_object->$name;

			if (method_exists($this->base_object, '__get'))
				return $this->base_object->$name;
		}

		throw new SwatInvalidPropertyException(
			"Property '{$name}' does not exist in details store.",
			0, $this, $name);
	}

	// }}}
	// {{{ private function __set()

	private function __set($name, $value)
	{
		$this->data[$name] = $value;
	}

	// }}}
	// {{{ private function __isset()

	private function __isset($name)
	{
		if (sset($this->data[$name]))
			return true;

		if (isset($this->base_object->$name))
			return true;

		return false;
	}

	// }}}
}

?>
