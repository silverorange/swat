<?
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

	public $text = '';
	public $size = 50;
	
	function display() {
		$inputtag = new SwatHtmlTag('input');
		$inputtag->type = 'text';
		$inputtag->name = $this->name;
		$inputtag->id = $this->name;
		$inputtag->value = $this->text;
		$inputtag->onfocus = "this.select();";
		$inputtag->size = $this->size;

		$inputtag->display();
	}	

	function process() {
		$this->text = $_POST[$this->name];

		if ($this->required && !strlen($this->text))
			$this->addErrorMessage(_S("The %s field is required."));
	}
}

/**
 * An integer entry widget.
 */
class SwatEntryInteger extends SwatEntry {
	function process() {
		parent::process();

		if (is_numeric($this->text))
			$this->text = intval($this->text);
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

		if (is_numeric($this->text))
			$this->text = floatval($this->text);
		else
			$this->addErrorMessage(_S("The %s field must be a number."));
	}
}
?>
