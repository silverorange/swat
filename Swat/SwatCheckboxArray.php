<?php
require_once('Swat/SwatCheckbox.php');

/**
 * An array of checkboxes
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatCheckboxArray extends SwatCheckbox {
	//TODO: review how values work here
	
	/**
	 * Checkbox Values
	 *
	 * The state of the widget.
	 * @var Array
	 */
	public $values = array();

	/**
	 * Checkbox Value
	 *
	 * The value of the checkbox.
	 * @var Array
	 */
	public $value = array();

	function display() {
		$value = 1;
		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'checkbox';
		$input_tag->name = $this->name.'[]';
		$input_tag->id = $this->name;
		$input_tag->value = $this->value;

		if (in_array($this->values, $this->value))
			$input_tag->checked = "checked";

		$input_tag->display();
	}	

	function process() {
		$this->value = array_key_exists($this->name, $_POST);
	}
}

