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

	/**
	 * The name attribute in the HTML input tag.
	 * @var string
	 */
	public $name;

	/**
	 * The value attribute in the HTML input tag.
	 * @var string
	 */
	public $value;

	/**
	 * The checked attribute in the HTML input tag.
	 * @var boolean
	 */
	public $checked = false;

	public function render() {
		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'checkbox';
		$input_tag->name = $this->name;
		$input_tag->value = $this->value;

		if ($this->checked)
			$input_tag->checked = 'checked';

		$input_tag->display();
	}
}
