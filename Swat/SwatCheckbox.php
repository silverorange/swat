<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatControl.php');
require_once('Swat/SwatHtmlTag.php');

/**
 * A checkbox entry widget.
 */
class SwatCheckbox extends SwatControl {

	/*
	 * The state of the widget.
	 * @var bool
	 */
	public $value = false;
	
	function display() {
		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'checkbox';
		$input_tag->name = $this->name;
		$input_tag->id = $this->name;
		$input_tag->value = '1';

		if ($this->value)
			$input_tag->checked = "checked";

		$input_tag->display();
	}	

	function process() {
		$this->value = array_key_exists($this->name, $_POST);
	}
}

