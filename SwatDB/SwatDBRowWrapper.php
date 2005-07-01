<?php

/**
 * MDB2 Row Wrapper
 *
 * Used to wrap an MDB2 result for a single row
 *
 * @package   SwatDB
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatDBRowWrapper
{
	private $_found = false;

	/**
	 * Constructor
	 * @param mixed Either a MDB2 recordset or MDB2 row
	 */
	function __construct($data)
	{
		if (get_class($data) == 'stdClass') {
			//row
			$this->_found = true;
			$this->setVars($data);
			
		} else {
			//recordset
			if (MDB2::isError($data))
				throw new Exception($data->getMessage());
		
			if ($data->numrows()) {
				$this->_found = true;

				$row = $data->fetchRow(MDB2_FETCHMODE_OBJECT);
				$this->setVars($row);	
			}
		}		
	}

	private function setVars($row)
	{
		foreach (get_object_vars($this) as $var => $val)
			if (isset($row->$var))
				$this->$var = $row->$var;
	}

	/**
	 * Result found
	 * @return boolean Whether or not a result exists
	 */
	public function isFound()
	{
		return $this->_found;
	}
}

?>
