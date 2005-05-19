<?php
require_once('Swat/SwatTextarea.php');

/**
 * A multi-line text entry widget
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatTextareaEditor extends SwatTextarea {

	/**
	 * Width
	 *
	 * Width of the editor. In percent or pixels.
	 * @var mixed
	 */
	public $width = '90%';

	/**
	 * Height
	 *
	 * Height of the editor. In percent or pixels.
	 * @var mixed
	 */
	public $height = 200;
	
	public function display() {
		$this->displayJavascript();
	}	
	
	private function displayJavascript() {
		$value = $this->rteSafe($this->value);
		
		echo '<script type="text/javascript">';
		include_once('Swat/javascript/swat-textarea-editor.js');

		echo 'initRTE("swat/images/textarea-editor/", "swat/", "", false);';
		echo "writeRichText('{$this->name}', '{$value}', '{$this->width}', '{$this->height}');";
		
		echo '</script>';
	}
	
	private function rteSafe($strText) {
		//returns safe code for preloading in the RTE
		$tmpString = $strText;
	
		//convert all types of single quotes
		$tmpString = str_replace(chr(145), chr(39), $tmpString);
		$tmpString = str_replace(chr(146), chr(39), $tmpString);
		$tmpString = str_replace("'", "&#39;", $tmpString);
	
		//convert all types of double quotes
		$tmpString = str_replace(chr(147), chr(34), $tmpString);
		$tmpString = str_replace(chr(148), chr(34), $tmpString);
		//	$tmpString = str_replace("\"", "\"", $tmpString);
	
		//replace carriage returns & line feeds
		$tmpString = str_replace(chr(10), " ", $tmpString);
		$tmpString = str_replace(chr(13), " ", $tmpString);
	
		return $tmpString;
	}

	public function process() {
		echo $_POST[$this->name];
		exit();
	}
}

?>
