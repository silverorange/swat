<?php
require_once('Swat/SwatControl.php');
require_once('Swat/SwatHtmlTag.php');

/**
 * An element ordering widget
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatChangeOrder extends SwatControl {

	/*
	 * Value ordered array
	 *
	 * An array of element ids passed back in the user-specified order.
	 * @var array
	 */
	public $value = null;

	/*
	 * Width of the order box (either px or %)
	 * @var string
	 */
	public $width = 400;

	/*
	 * Height of the order box (either px or %)
	 * @var string
	 */
	public $height = 150;
	
	public function display() {
		$div_tag = new SwatHtmlTag('div');
		$div_tag->class = 'swat-order-control-div';
		$div_tag->open();

		//this has to go above the javascript
		$iframe_tag = new SwatHtmlTag('iframe');
		$iframe_tag->width = $this->width;
		$iframe_tag->height = $this->height;
		$iframe_tag->id = $this->name.'_iframe';
		$iframe_tag->align = 'left';
		$iframe_tag->class = 'swat-order-control-iframe';
		$iframe_tag->open();
		$iframe_tag->close();
		
		$this->displayJavascript();

		$obj = $this->name.'_obj';
		/*
		$box_tag = new SwatHtmlTag('div');
		$box_tag->class = 'swat-order-control-box';
		$box_tag->open();


		$div_tag = new SwatHtmlTag('div');
		$div_tag->class = 'swat-order-control';
		$div_tag->onclick = "{$obj}.choose(this);";
		$div_tag->class = 'swat-order-control';
		
		$count = 0;
		foreach ($this->value as $id=>$title) {
			$div_tag->id = $obj.'_'.$count;
			$div_tag->open();
			echo $title;
			$div_tag->close();
			$count++;
		}
		*/

		$hidden_tag = new SwatHtmlTag('input');
		$hidden_tag->type = "hidden";
		$hidden_tag->id = $this->name;
		$hidden_tag->name = $this->name;
		$hidden_tag->value = implode(',',array_keys($this->value));
		$hidden_tag->display();
		
		$up_btn = new SwatHtmlTag('input');
		$up_btn->type = 'button';
		$up_btn->value = _S("Move Up");
		$up_btn->onclick = $obj.".updown('up');";
		$up_btn->display();

		echo '<br />';
	
		$down_btn = new SwatHtmlTag('input');
		$down_btn->type = 'button';
		$down_btn->value = _S("Move Down");
		$down_btn->onclick = $obj.".updown('down');";
		$down_btn->display();

		//TODO: maybe clean up the way the buttons are positioned
		echo '<div style="clear:left;"></div>';
	
		$div_tag->close();
		//$box_tag->close();
	}	

	public function process() {
		$this->value = $_POST[$this->name];
		$this->value = explode(',',$this->value);
		print_r($this->value);
		exit();
	}

	private function displayJavascript() {
		echo '<script type="text/javascript">';
		include_once('Swat/javascript/swat-change-order.js');
		
		$warning = _S("You must first select the item to reorder.");
		//TODO: figure out how to work the stylesheets better
		$style = '../swat/swat.css';
	
		$elements = $this->value;
		foreach ($elements as $k=>$v)
			$elements[$k] = addslashes($v);
		
		$elements = implode("','",$elements);
		printf("\n {$this->name}_obj = new SwatChangeOrder('%s'); ",
				$this->name, $warning);
		echo "\n {$this->name}_obj.stylesheet = '{$style}';";
		echo "\n {$this->name}_obj.draw(new Array('{$elements}')); ";
				
		echo '</script>';
	}
}

?>
