<?php

/**
 *
 * Example use:
 * <code>
 * $transaction = new SwatDBTransaction($database);
 * try {
 *     SwatDB::query($database, $sql);
 * } catch (SwatDBException $e) {
 *     $transaction->rollback();
 *     throw $e;
 * }
 * $transaction->commit();
 * </code>
 *
 * @package   SwatDB
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDBTransaction extends SwatObject
{


    /**
     * The database driver object to perform the transaction with
     *
     * @var MDB2_Driver_Common
     */
    private $db;



    /**
     * Begins a new database transaction
     *
     * @param MDB2_Driver_Common the database connection to perform the
     *                            transaction with.
     */
    public function __construct(MDB2_Driver_Common $db)
    {
        $this->db = $db;
        $this->db->beginNestedTransaction();
    }



    /**
     * Commits this database transaction
     */
    public function commit()
    {
        $this->db->completeNestedTransaction();
    }



    /**
     * Rolls-back this database transaction
     */
    public function rollback()
    {
        $this->db->failNestedTransaction();
        // this is required to actually rollback the transaction
        // since failNestedTransaction just sets a flag indicating
        // there is an error unless you pass the immediately param
        $this->db->completeNestedTransaction();
    }

}
