<?php
require_once('Swat/SwatControl.php');
require_once('Swat/SwatHtmlTag.php');

/**
 * A multi-line text entry widget
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatTextarea extends SwatControl {

	/*
	 * Text content of the widget
	 * @var string
	 */
	public $value = '';

	/*
	 * Required
	 *
	 * Must have a non-empty value when processed
	 * @var bool
	 */
	public $required = false;
	
	/**
	 * Rows
	 *
	 * Number of rows for the HTML textarea tag
	 * @var int
	 */
	public $rows = 10;

	/**
	 * Columns
	 *
	 * Number of columns for the HTML textarea tag
	 * @var int
	 */
	public $cols = 50;
	
	function display() {
		$textarea_tag = new SwatHtmlTag('textarea');
		$textarea_tag->name = $this->name;
		$textarea_tag->id = $this->name;
		// Attributes rows and cols are required in a textarea for XHTML strict.
		$textarea_tag->rows = $this->rows;
		$textarea_tag->cols = $this->cols;

		$textarea_tag->open();
		echo $this->value;
		$textarea_tag->close();
	}	

	function process() {
		$this->value = $_POST[$this->name];

		if ($this->required && !strlen($this->value))
			$this->addErrorMessage(_S("The %s field is required."));
	}
}

?>
