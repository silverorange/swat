<?php
/**
 * MDB2 Recordset Wrapper
 *
 * Used to wrap an MDB2 recordset with an array of SwatDBRowWrapper objects.
 *
 * @package SwatDB
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2005
 */

abstract class SwatDBRecordsetWrapper {

	/**
	 * Name of the {@link SwatDBRowWrapper} class
	 * @var SwatDBRowWrapper 
	 */
	protected $row_wrapper_class;
	
	/**
	 * Array of SwatDBRowWrapper items
	 * @var array
	 */
	public $items = array();

	/**
	 * Constructor
	 * @param MDB2 recordset
	 */
	function __construct($rs) {
		if (MDB2::isError($rs))
			throw new Exception($rs->getMessage());

		if ($rs->numrows())
			while ($row = $rs->fetchRow(MDB2_FETCHMODE_OBJECT))
				$this->items[] = new $this->row_wrapper_class($row);
	}
}
?>
