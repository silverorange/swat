<?php

require_once('Swat/SwatCellRenderer.php');

/**
 * A text renderer.
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
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
