<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatObject.php';

/**
 * Database field
 * 
 * Data class to represent a database field, a (name, type) pair.
 *
 * @package   SwatDB
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDBField extends SwatObject
{
	// {{{ public properties

	/**
	 * The name of the database field
	 *
	 * @var string
	 */
	public $name;

	/**
	 * The type of the database field
	 *
	 * Any standard MDB2 datatype is valid here.
	 *
	 * @var string
	 */
	public $type;

	// }}}
	// {{{ public function __construct()

	/**
	 * @param string $field A string representation of a database field in the
	 *        form [<type>:]<name> where <name> is the name of the database
	 *        field and <type> is any standard MDB2 datatype.
	 *
	 * @param string $default_type The type to use by default if it is not
	 *        specified in the $field string. Any standard MDB2 datatype
	 *        is valid here.
	 */
	public function __construct($field, $default_type = 'text')
	{
		$x = explode(':', $field);

		if (isset($x[1])) {
			$this->name = $x[1];
			$this->type = $x[0];
		} else {
			$this->name = $x[0];
			$this->type = $default_type;
		}
	}

	// }}}
	// {{{ public function __toString()

	/**
	 * Get the field as a string
	 *
	 * @return string A string representation of a database field in the
	 *        form <type>:<name> where <name> is the name of the database
	 *        field and <type> is a standard MDB2 datatype.
	 */
	public function __toString()
	{
		return $this->type.':'.$this->name;
	}

	// }}}
}

?>
