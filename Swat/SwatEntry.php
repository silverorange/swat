<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatControl.php');
require_once('Swat/SwatHtmlTag.php');
require_once('Swat/SwatException.php');

/**
 * A single line text entry widget.
 */
class SwatEntry extends SwatControl {

	/*
	 * @var string Text content of the widget.
	 */
	public $value = '';

	/**
	 * @var int Size in characters of the HTML text form input.
	 */
	public $size = 50;
	
	/**
	 * @var int Maximum number of allowable characters in HTML text form input, or null.
	 */
	public $maxlength = null;


	function display() {
		$inputtag = new SwatHtmlTag('input');
		$inputtag->type = 'text';
		$inputtag->name = $this->name;
		$inputtag->id = $this->name;
		$inputtag->value = $this->value;
		$inputtag->onfocus = "this.select();";
		$inputtag->size = $this->size;

		if ($this->maxlength != null)
			$inputtag->maxlength = $this->maxlength;

		$inputtag->display();
	}	

	function process() {
		$this->value = $_POST[$this->name];

		if ($this->required && !strlen($this->value))
			$this->addErrorMessage(_S("The %s field is required."));
	}
}

/**
 * An integer entry widget.
 */
class SwatEntryInteger extends SwatEntry {
	function process() {
		parent::process();

		if (is_numeric($this->value))
			$this->value = intval($this->value);
		else
			$this->addErrorMessage(_S("The %s field must be an integer."));
	}
}

/**
 * A float entry widget.
 */
class SwatEntryFloat extends SwatEntry {
	function process() {
		parent::process();

		if (is_numeric($this->value))
			$this->value = floatval($this->value);
		else
			$this->addErrorMessage(_S("The %s field must be a number."));
	}
}
?>
