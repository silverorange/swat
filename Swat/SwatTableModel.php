<?php

require_once 'Swat/SwatObject.php';

/**
 * The data model for a SwatTableView
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
interface SwatTableModel {

	public function &getRows();

	public function getRowCount();

	public function addRow($data, $id = null);

}

?>
