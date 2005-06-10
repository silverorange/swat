<?php

require_once 'Swat/SwatTextarea.php';

/**
 * A wysiwyg text entry widget
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTextareaEditor extends SwatTextarea
{

	/**
	 * Width
	 *
	 * Width of the editor. In percent, pixels, or ems.
	 *
	 * @var string
	 */
	public $width = '100%';

	/**
	 * Height
	 *
	 * Height of the editor. In percent, pixels, or ems.
	 *
	 * @var string
	 */
	public $height = '15em';
	
	/**
	 * Base-Href
	 *
	 * Optional base-href, used to reference images and other urls in the editor.
	 *
	 * @var string
	 */
	public $basehref = null; 
	
	
	public function display()
	{
		$this->displayJavascript();
	}	
	
	private function displayJavascript()
	{
		$value = $this->rteSafe($this->value);

		$basehref = ($this->basehref === null) ? 'null' : $this->basehref;
		
		echo '<script type="text/javascript">';
		include_once('Swat/javascript/swat-textarea-editor.js');

		$this->displayJavascriptTranslations();
		echo 'initRTE("swat/images/textarea-editor/", "swat/", "", false);';
		echo "writeRichText('{$this->id}', '{$value}', '{$this->width}', '{$this->height}', '{$basehref}');";
		
		echo '</script>';
	}

	private function displayJavascriptTranslations()
	{
		echo " var rteT = new Array();";
		
		foreach($this->translations() as $k => $word)
			echo "\n rteT['{$k}'] = '".str_replace("'", "\'", $word)."';";
	}

	private function translations()
	{
		return array(
			'bold' => _S("Bold"),
			'italic' => _S("Italic"),
			'underline' => _S("Underline"),
			'align_left' => _S("Align Left"),
			'align_right' => _S("Align Right"),
			'align_center' => _S("Align Center"),
			'ordered_list' => _S("Ordered List"),
			'unordered_list' => _S("Unordered List"),
			'indent' => _S("Indent"),
			'outdent' => _S("Outdent"),
			'insert_link' => _S("Insert Link"),
			'horizontal_rule' => _S("Horizontal Rule"),
			'highlight' => _S("Highlight"),
			'quote' => _S("Quote"),
			'style' => _S("Style"),
			'clear_formatting' => _S("Clear Formatting"),
			'paragraph' => _S("Paragraph"),
			'heading' => _S("Heading"),
			'address' => _S("Address"),
			'formatted' => _S("Formatted"),
			
			//pop-up link
			'enter_url' => _S("A URL is required"),
			'url' => _S("URL"),
			'link_text' => _S("Link Text"),
			'target' => _S("Target"),
			'insert_link' => _S("Insert Link"),
			'cancel' => _S("Cancel")
		);
	}
	
	private function rteSafe($value)
	{
		//returns safe code for preloading in the RTE
	
		//convert all types of single quotes
		$value = str_replace(chr(145), chr(39), $value);
		$value = str_replace(chr(146), chr(39), $value);
		$value = str_replace("'", "&#39;", $value);
	
		//convert all types of double quotes
		$value = str_replace(chr(147), chr(34), $value);
		$value = str_replace(chr(148), chr(34), $value);
		//	$value = str_replace("\"", "\"", $value);
	
		//replace carriage returns & line feeds
		$value = str_replace(chr(10), " ", $value);
		$value = str_replace(chr(13), " ", $value);
	
		return $value;
	}
}

?>
