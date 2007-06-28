<?php

/**
 * Interface for data-bound objects that are recordable (saveable and loadable)
 *
 * @package   SwatDB
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
interface SwatDBRecordable
{
	// {{{ public function setDatabase()

	/**
	 * Sets the database driver to use for this object
	 *
	 * @param MDB2_Driver_Common $db the database driver to use for this
	 *                                object.
	 */
	public function setDatabase(MDB2_Driver_Common $db);

	// }}}
	// {{{ public function save()

	/**
	 * Saves this object to the database
	 */
	public function save();

	// }}}
	// {{{ public function load()

	/**
	 * Loads this object from the database
	 *
	 * @param mixed $data the data required to load this object from the
	 *                     database.
	 *
	 * @return boolean true if this object was sucessfully loaded and false if
	 *                  it was not.
	 */
	public function load($data);

	// }}}
	// {{{ public function delete()

	/**
	 * Deletes this object from the database
	 */
	public function delete();

	// }}}
	// {{{ public function isModified()

	/**
	 * Gets whether or not this object is modified
	 *
	 * @return boolean true if this object is modified and false if it is not.
	 */
	public function isModified();

	// }}}
}

?>
