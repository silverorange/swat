<?php

/**
 * The base object type
 *
 * @package   Swat
 * @copyright 2004-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatObject
{

	/**
	 * Gets this object as a string
	 *
	 * This is a magic method that is called by PHP when this object is used
	 * in string context. For example:
	 *
	 * <code>
	 * $my_object = new SwatMessage('Hello, World!');
	 * echo $my_object;
	 * </code>
	 *
	 * @return string this object represented as a string.
	 */
	public function __toString()
	{
		ob_start();
		Swat::printObject($this);
		return ob_get_clean();
	}

}

?>
