<?php

require_once('Swat/SwatCellRenderer.php');

/**
 * A text renderer.
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTextCellRenderer extends SwatCellRenderer {

	/**
	 * Cell value
	 *
	 * The content to place within the cell.
	 * @var string
	 */
	public $value = '';

	public function render($prefix) {
		echo $this->value;
	}
}

?>
