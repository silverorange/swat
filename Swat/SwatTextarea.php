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
 * A multi-line text entry widget.
 */
class SwatTextarea extends SwatControl {

	public $text = '';
	
	function display() {
		$textareatag = new SwatHtmlTag('textarea');
		$textareatag->name = $this->name;
		$textareatag->id = $this->name;

		$textareatag->open();
		echo $this->text;
		$textareatag->close();
	}	

	function process() {
		$this->text = $_POST[$this->name];

		if ($this->required && !strlen($this->text))
			$this->appendMessage(S_("%s is required"));
	}
}

?>
