<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatControl.php');
require_once('Swat/SwatHtmlTag.php');

/**
 * A single line text entry widget.
 */
class SwatEntry extends SwatControl {

	/*
	 * Text content of the widget, or null.
	 * @var string
	 */
	public $value = null;

	/*
	 * Must have a non-empty value when processed.
	 * @var bool
	 */
	public $required = false;
	
	/**
	 * Size in characters of the HTML text form input, or null.
	 * @var int
	 */
	public $size = 50;
	
	/**
	 * @var int
	 * Maximum number of allowable characters in HTML text form input, or null.
	 */
	public $maxlength = null;


	function display() {
		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'text';
		$input_tag->name = $this->name;
		$input_tag->id = $this->name;
		$input_tag->onfocus = "this.select();";
                
		if ($this->value != null)
			$inputtag->value = $this->value;

		if ($this->size != null)
			$input_tag->size = $this->size;

		if ($this->maxlength != null)
			$input_tag->maxlength = $this->maxlength;

		$input_tag->display();
	}	

	function process() {
		$this->value = $_POST[$this->name];

		if ($this->required && !strlen($this->value))
			$this->addErrorMessage(_S("The %s field is required."));
	}
}

?>
