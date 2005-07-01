<?php

/**
 * MDB2 Recordset Wrapper
 *
 * Used to wrap an MDB2 recordset with an array of SwatDBRowWrapper objects.
 *
 * @package   SwatDB
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatDBRecordsetWrapper
{
	// {{{ protected properties

	/**
	 * Name of the {@link SwatDBRowWrapper} class
	 * @var SwatDBRowWrapper 
	 */
	protected $row_wrapper_class;

	// }}}
	// {{{ public properties

	/**
	 * Array of SwatDBRowWrapper items
	 * @var array
	 */
	public $items = array();

	// }}}
	// {{{ public function __construct

	/**
	 * Constructor
	 * @param MDB2 recordset
	 */
	function __construct($rs)
	{
		if (MDB2::isError($rs))
			throw new Exception($rs->getMessage());

		if ($rs->numrows())
			while ($row = $rs->fetchRow(MDB2_FETCHMODE_OBJECT))
				$this->items[] = new $this->row_wrapper_class($row);
	}

	// }}}
}

?>
