<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'MDB2.php';
require_once 'Swat/SwatObject.php';
require_once 'SwatDB/exceptions/SwatDBException.php';

/**
 * A database transaction that is safe to use with database drivers that do
 * not support nested transactions
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
 * @copyright 2006-2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDBTransaction extends SwatObject
{
	// {{{ private properties

	/**
	 * The database driver object to perform the transaction with
	 *
	 * @var MDB2_Driver_Common
	 */
	private $db;

	/**
	 * Whether or not this database transaction exists inside another database
	 * transaction (nested)
	 *
	 * @var boolean
	 */
	private $in_another_transaction;

	/**
	 * Whether or not this transaction is finished
	 *
	 * A transaction is finished after either it is either successfully
	 * committed or it is successfully rolled-back.
	 *
	 * @var boolean
	 */
	private $finished;

	// }}}
	// {{{ public function __construct()

	/**
	 * Begins a new database transaction
	 *
	 * For databases that do not support nested transactions, this method
	 * prevents opening a new transaction if we are already in a transaction.
	 *
	 * @param MDB2_Driver_Common the database connection to perform the
	 *                            transaction with.
	 */
	public function __construct(MDB2_Driver_Common $db)
	{
		$this->db = $db;
		$this->in_another_transaction = ($this->db->in_transaction);
		if (!$this->in_another_transaction)
			$this->db->beginTransaction();
	}

	// }}}
	// {{{ public function commit()

	/**
	 * Commits this database transaction
	 *
	 * For databases that do not support nested transactions, this method
	 * prevents comitting a transaction if we are already inside another
	 * transaction.
	 */
	public function commit()
	{
		if ($this->finished) {
			throw new SwatDBException('Transaction objects cannot be reused. '.
				'Create a new SwatDBTransaction object to begin a new '.
				'transaction.');
		} else {
			if (!$this->in_another_transaction)
				$this->db->commit();

			$this->finished = true;
		}
	}

	// }}}
	// {{{ public function rollback()

	/**
	 * Rolls-back this database transaction
	 *
	 * For databases that do not support nested transactions, this method
	 * prevents rolling-back a transaction if we are already inside another
	 * transaction.
	 */
	public function rollback()
	{
		if ($this->finished) {
			throw new SwatDBException('Transaction objects cannot be reused. '.
				'Create a new SwatDBTransaction object to begin a new '.
				'transaction.');
		} else {
			if (!$this->in_another_transaction)
				$this->db->rollback();

			$this->finished = true;
		}
	}

	// }}}
}

?>
