<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatCellRenderer.php');

/**
 * A renderer for a column of checkboxes.
 */
class SwatCellRendererCheckbox extends SwatCellRenderer {

	public $name;
	public $id;

	public function render() {
		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'checkbox';
		$input_tag->name = $this->name;
		$input_tag->value = $this->id;
		$input_tag->display();
	}
}
