<?php

/**
 * Interface for data-bound objects that are recordable (saveable and loadable).
 *
 * @copyright 2007-2026 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
interface SwatDBRecordable
{
    /**
     * Sets the database driver to use for this object.
     *
     * @param MDB2_Driver_Common  $db  the database driver to use for this object
     * @param array<string, bool> $set optional array of objects passed through
     *                                 recursive call.  Array keys represent hashes
     *                                 of all objects that have already been set.
     *                                 Prevents infinite recursion.
     */
    public function setDatabase(MDB2_Driver_Common $db, array $set = []): void;

    /**
     * Saves this object to the database.
     */
    public function save(): void;

    /**
     * Loads this object from the database.
     *
     * @param mixed $data the data required to load this object from the
     *                    database (typically an id or array of ids)
     *
     * @return bool true if this object was sucessfully loaded and false if
     *              it was not
     */
    public function load(mixed $data): bool;

    /**
     * Deletes this object from the database.
     */
    public function delete(): void;

    /**
     * Whether this object is modified.
     *
     * @return bool true if this object is modified and false if it is not
     */
    public function isModified(): bool;
}
