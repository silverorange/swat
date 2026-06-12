<?php

/**
 * Class for working with database transactions.
 *
 * ```php
 * $transaction = new SwatDBTransaction($database);
 * try {
 *     SwatDB::query($database, $sql);
 * } catch (SwatDBException $e) {
 *     $transaction->rollback();
 *     throw $e;
 * }
 * $transaction->commit();
 * ```
 *
 * @copyright 2006-2026 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDBTransaction extends SwatObject
{
    /**
     * Begins a new database transaction.
     *
     * @param MDB2_Driver_Common $db the database connection on which
     *                               to perform the transaction
     */
    public function __construct(
        private readonly MDB2_Driver_Common $db
    ) {
        $this->db->beginNestedTransaction();
    }

    /**
     * Commits this database transaction.
     */
    public function commit(): void
    {
        $this->db->completeNestedTransaction();
    }

    /**
     * Rolls back this database transaction.
     */
    public function rollback(): void
    {
        $this->db->failNestedTransaction();

        // This is required to actually rollback the transaction,
        // since `failNestedTransaction()` just sets a flag
        // indicating there is an error unless you pass
        // the `immediately` parameter.
        $this->db->completeNestedTransaction();
    }
}
