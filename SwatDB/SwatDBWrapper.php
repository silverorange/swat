<?php

/**
 * MDB2 Wrapper
 *
 * @package SwatDB
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2005
 */
abstract class SwatDBWrapper {

	private $_found = false;

	function __construct($rs) {
		if (MDB2::isError($rs))
			throw new Exception($rs->getMessage());

		if ($rs->numrows()) {
			$this->_found = true;

			$row = $rs->fetchRow(MDB2_FETCHMODE_OBJECT);

			foreach (get_object_vars($this) as $var => $val)
				$this->$var = $row->$var;
		}
	}

	function isFound() {
		return $this->_found;
	}
}

?>
