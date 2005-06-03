<?php

require_once('Swat/SwatControl.php');
require_once('Swat/SwatHtmlTag.php');

/**
 * A checkbox entry widget
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatCheckbox extends SwatControl implements SwatState {

	/*
	 * Checkbox value
	 *
	 * The state of the widget.
	 *
	 * @var bool
	 */
	public $value = false;
	
	public function display() {
		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'checkbox';
		$input_tag->name = $this->id;
		$input_tag->id = $this->id;
		$input_tag->value = '1';

		if ($this->value)
			$input_tag->checked = 'checked';

		$input_tag->display();
	}	

	public function process() {
		$this->value = array_key_exists($this->id, $_POST);
	}

	public function getState() {
		return $this->value;
	}
	
	public function setState($state) {
		$this->value = $state;
	}
}

?>
