<?php
require_once('Swat/SwatObject.php');

/**
 * Base class for a extra row displayed that the bottom of a SwatTableView
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
abstract class SwatTableViewRow extends SwatObject {

	/**
	 * The {@link SwatTableView} associated with this row
	 * @var SwatTableView
	 */
	public $view = null;

	public abstract function display(&$columns);

	public function init() {

	}
}
