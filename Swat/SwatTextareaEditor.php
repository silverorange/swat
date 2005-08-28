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
	
	
	public function process()
	{
		parent::process();
		$this->value = str_replace("\n", "", $this->value);
	}

	public function display()
	{
		if (!$this->visible)
			return

		$this->displayJavascript();
	}	
	
	private function displayJavascript()
	{
		$value = $this->rteSafe($this->value);

		$basehref = ($this->basehref === null) ? 'null' : $this->basehref;
	
		echo '<script type="text/javascript" src="swat/javascript/swat-textarea-editor.js"></script>';
		echo '<script type="text/javascript">';
		echo "//<![CDATA[\n";

		$this->displayJavascriptTranslations();

		echo 'initRTE("swat/images/textarea-editor/", "swat/", "", false);';
		echo "writeRichText('{$this->id}', '{$value}', '{$this->width}', ".
			"'{$this->height}', '{$basehref}');\n";

		echo "\n//]]>";
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
			'bold' => Swat::_('Bold'),
			'italic' => Swat::_('Italic'),
			'underline' => Swat::_('Underline'),
			'align_left' => Swat::_('Align Left'),
			'align_right' => Swat::_('Align Right'),
			'align_center' => Swat::_('Align Center'),
			'ordered_list' => Swat::_('Ordered List'),
			'unordered_list' => Swat::_('Unordered List'),
			'indent' => Swat::_('Indent'),
			'outdent' => Swat::_('Outdent'),
			'insert_link' => Swat::_('Insert Link'),
			'horizontal_rule' => Swat::_('Horizontal Rule'),
			'highlight' => Swat::_('Highlight'),
			'quote' => Swat::_('Quote'),
			'style' => Swat::_('Style'),
			'clear_formatting' => Swat::_('Clear Formatting'),
			'paragraph' => Swat::_('Paragraph'),
			'heading' => Swat::_('Heading'),
			'address' => Swat::_('Address'),
			'formatted' => Swat::_('Formatted'),
			
			//pop-up link
			'enter_url' => Swat::_('A URL is required'),
			'url' => Swat::_('URL'),
			'link_text' => Swat::_('Link Text'),
			'target' => Swat::_('Target'),
			'insert_link' => Swat::_('Insert Link'),
			'cancel' => Swat::_('Cancel')
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
		$value = str_replace('"', '&quot;', $value);
	
		//replace carriage returns & line feeds
		$value = str_replace(chr(10), " ", $value);
		$value = str_replace(chr(13), " ", $value);
	
		return $value;
	}
}

?>
