<?php
require_once('Swat/SwatObject.php');

/**
 * The data model for a SwatTableView
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
interface SwatTableModel {

	public function &getRows();

	public function getRowCount();

	public function addRow($data, $id = null);

}
