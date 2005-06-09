<?php

require_once('Swat/SwatControl.php');
require_once('Swat/SwatHtmlTag.php');
require_once('Swat/SwatState.php');

/**
 * A multi-line text entry widget
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTextarea extends SwatControl implements SwatState {

	/**
	 * Text content of the widget
	 *
	 * @var string
	 */
	public $value = '';

	/**
	 * Required
	 *
	 * Must have a non-empty value when processed
	 *
	 * @var bool
	 */
	public $required = false;
	
	/**
	 * Rows
	 *
	 * Number of rows for the HTML textarea tag
	 *
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
	
	public function display() {
		$textarea_tag = new SwatHtmlTag('textarea');
		$textarea_tag->name = $this->id;
		$textarea_tag->id = $this->id;
		// Attributes rows and cols are required in a textarea for XHTML strict.
		$textarea_tag->rows = $this->rows;
		$textarea_tag->cols = $this->cols;

		$textarea_tag->open();
		echo $this->value;
		$textarea_tag->close();
	}	

	public function process() {
		$this->value = $_POST[$this->id];

		if ($this->required && !strlen($this->value)) {
			$msg = _S("The %s field is required.");
			$this->addMessage(new SwatMessage($msg, SwatMessage::USER_ERROR));
		}
	}
	
	public function getState() {
		return $this->value;
	}

	public function setState($state) {
		$this->value = $state;
	}
}

?>
