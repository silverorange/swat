<?php
/**
 * @package Admin
 * @copyright silverorange 2004
 */
require_once('Swat/SwatObject.php');

/**
 * A single entry in a AdminActions widget.
 */
class AdminActionItem extends SwatObject {
	public $name;
	public $title = '';
	public $widget = null;

	function __construct($name = '') {
		$this->name = $name;
	}
	
}
?>
