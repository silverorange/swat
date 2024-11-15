<?php

/**
 * Interface for data-bound objects that are recordable (saveable and loadable).
 *
 * @copyright 2007-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
interface SwatDBRecordable
{
    /**
     * Sets the database driver to use for this object.
     *
     * @param MDB2_Driver_Common $db  the database driver to use for this
     *                                object
     * @param array              $set optional array of objects passed through
     *                                recursive call containing all objects that
     *                                have been set already. Prevents infinite
     *                                recursion.
     */
    public function setDatabase(MDB2_Driver_Common $db, array $set = []);

    /**
     * Saves this object to the database.
     */
    public function save();

    /**
     * Loads this object from the database.
     *
     * @param mixed $data the data required to load this object from the
     *                    database
     *
     * @return bool true if this object was sucessfully loaded and false if
     *              it was not
     */
    public function load($data);

    /**
     * Deletes this object from the database.
     */
    public function delete();

    /**
     * Gets whether or not this object is modified.
     *
     * @return bool true if this object is modified and false if it is not
     */
    public function isModified();
}
