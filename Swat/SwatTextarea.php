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
 * A multi-line text entry widget.
 */
class SwatTextarea extends SwatControl {

	public $text = '';

	/**
	 * @var int Number of rows for the HTML textarea tag.
	 */
	public $rows = 10;

	/**
	 * @var int Number of columns for the HTML textarea tag.
	 */
	public $cols = 50;
	
	function display() {
		$textareatag = new SwatHtmlTag('textarea');
		$textareatag->name = $this->name;
		$textareatag->id = $this->name;
		// Attributes rows and cols are required in a textarea for XHTML strict.
		$textareatag->rows = $this->rows;
		$textareatag->cols = $this->cols;

		$textareatag->open();
		echo $this->text;
		$textareatag->close();
	}	

	function process() {
		$this->text = $_POST[$this->name];

		if ($this->required && !strlen($this->text))
			$this->addErrorMessage(_S("The %s field is required."));
	}
}

?>
