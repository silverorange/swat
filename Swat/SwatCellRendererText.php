<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatCellRenderer.php');

/**
 * A text renderer.
 */
class SwatCellRendererText extends SwatCellRenderer {

	public $text = '';

	public function render() {
		echo $this->text;
	}
}
