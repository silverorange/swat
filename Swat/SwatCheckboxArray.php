<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatCheckbox.php');

/**
 * An array of checkboxes.
 */
class SwatCheckboxArray extends SwatCheckbox {

	/*
	 * The state of the widget.
	 * @var Array
	 */
	public $values = array();
	
	function display($value) {
		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'checkbox';
		$input_tag->name = $this->name.'[]';
		$input_tag->id = $this->name;
		$input_tag->value = $value;

		if ($this->value)
			$input_tag->checked = "checked";

		$input_tag->display();
	}	

	function process() {
		$this->value = array_key_exists($this->name, $_POST);
	}
}

