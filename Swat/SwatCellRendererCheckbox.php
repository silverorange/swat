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

	public function render($prefix) {
		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'checkbox';
		$input_tag->name = $prefix.$this->name.'[]';
		$input_tag->value = $this->value;

		if (isset($_POST[$this->name]))
			if (in_array($this->value, $_POST[$this->name]))
				$input_tag->checked = 'checked';

		$input_tag->display();
	}
}
