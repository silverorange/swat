<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatObject.php';

/**
 * A data structure that can be used with the SwatDetailsView
 *
 * A new details store is empty by default unless is it initialized with
 * another object.
 *
 * @package   Swat
 * @copyright 2006-2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDetailsStore extends SwatObject
{
	// {{{ private properties

	/**
	 * The base object for this details store
	 *
	 * Properties of this details store are taken from this base object unless
	 * they are manually specified.
	 *
	 * @var stdClass
	 */
	private $base_object;

	/**
	 * Manually set data of this details store
	 *
	 * @var array
	 */
	private $data = array();

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new details store
	 *
	 * @param stdClass $base_object optional. The object to initialize this
	 *                               details store with. Properties in this
	 *                               details store will be taken from the base
	 *                               object unless they are manually set on
	 *                               this details store.
	 */
	public function __construct($base_object = null)
	{
		if ($base_object !== null && is_object($base_object)
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

		if ($this->base_object !== null && isset($this->base_object->$name))
			return $this->base_object->$name;

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

	/**
	 * Gets whether or not a property is set for this details store
	 *
	 * First, the manually set properties are checked. Then the properties of
	 * the base object are checked if there is a base object.
	 *
	 * @param string $name the name of the property to check.
	 *
	 * @return boolean true if the property is set for this details store and
	 *                  false if it is not.
	 */
	private function __isset($name)
	{
		$is_set = isset($this->data[$name]);

		if (!$is_set && $this->base_object !== null)
			$is_set = isset($this->base_object->$name);

		return $is_set;
	}

	// }}}
}

?>
