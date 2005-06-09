<?php

require_once('Swat/SwatObject.php');
require_once('Swat/SwatTableModel.php');

/**
 * A data structure that can be used with the SwatTableView
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTableStore extends SwatObject implements SwatTableModel {

	private $rows = array();

	public function __construct() {

	}

	public function getRowCount() {
		return count($this->rows);
	}

	public function &getRows() {
		return $this->rows;
	}

	public function addRow($data, $id = null) {
		if ($id === null)
			$this->rows[] = $data;
		else
			$this->rows[$id] = $data;
	}
}

?>
