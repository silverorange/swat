<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatObject.php');

/**
 * The data model for a SwatTableView.
 */
interface SwatTableModel {

	public function &getRows();

	public function addRow($data, $id = null);

}
