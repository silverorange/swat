<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */

require_once('Swat/SwatObject.php');

/**
 * A single entry in a SwatActions widget.
 */
class SwatActionItem extends SwatObject {
	public $name;
	public $title = '';
	public $widget = null;

	function __construct($name = '') {
		$this->name = $name;
	}
	
}
?>
