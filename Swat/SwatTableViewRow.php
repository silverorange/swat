<?php
require_once('Swat/SwatObject.php');

/**
 * Base class for a extra row displayed that the bottom of a SwatTableView
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
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
