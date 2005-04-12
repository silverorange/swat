<?php
require_once('Swat/SwatControl.php');
require_once('Swat/SwatHtmlTag.php');
require_once('Swat/SwatState.php');

/**
 * An element ordering widget
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatChangeOrder extends SwatControl implements SwatState {

	/*
	 * Order Options
	 *
	 * An array containing the options to display. The array should be
	 * of the form $id => $title
	 * @var array
	 */
	public $options = array();

	/*
	 * Value ordered array
	 *
	 * The current ordering of options in the widget. If null, options are
	 * displayed in the order of the options array.
	 * @var array
	 */
	public $values = null;
	
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
	
	/*
	 * onClick javascript attribute of the buttons
	 * @var string
	 */
	public $onclick = null;
	
	public function display() {
		
		if ($this->values !== null) {
			$array = array();
			foreach ($this->values as $id)
				$array[$id] = $this->options[$id];

			$this->options = $array;
		}
	
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

		$hidden_tag = new SwatHtmlTag('input');
		$hidden_tag->type = "hidden";
		$hidden_tag->id = $this->name;
		$hidden_tag->name = $this->name;
		$hidden_tag->value = implode(',',array_keys($this->options));
		$hidden_tag->display();
		
		$up_btn = new SwatHtmlTag('input');
		$up_btn->type = 'button';
		$up_btn->value = _S("Move Up");
		$up_btn->onclick = $obj.".updown('up');";
		if ($this->onclick !== null)
			$up_btn->onclick.= $this->onclick;
		$up_btn->display();

		echo '<br />';
	
		$down_btn = new SwatHtmlTag('input');
		$down_btn->type = 'button';
		$down_btn->value = _S("Move Down");
		$down_btn->onclick = $obj.".updown('down');";
		if ($this->onclick !== null)
			$down_btn->onclick.= $this->onclick;
		$down_btn->display();

		//TODO: maybe clean up the way the buttons are positioned
		echo '<div style="clear:left;"></div>';
	
		$div_tag->close();
	}	

	public function process() {
		$this->values = explode(',', $_POST[$this->name]);
	}

	private function displayJavascript() {
		echo '<script type="text/javascript">';
		include_once('Swat/javascript/swat-change-order.js');
		
		$warning = _S("You must first select the item to reorder.");
		$style = '../swat/swat.css'; //TODO: figure out how to work the stylesheets better
		
		$values = array();
		foreach ($this->options as $id => $title)
			$values[$id] = addslashes($title);
		$values = implode("','", $values);
		
		printf("\n {$this->name}_obj = new SwatChangeOrder('%s'); ",
				$this->name, $warning);
		echo "\n {$this->name}_obj.stylesheet = '{$style}';";
		echo "\n {$this->name}_obj.draw(new Array('{$values}')); ";
				
		echo '</script>';
	}

	public function getState() {
		if ($this->values === null)
			return array_keys($this->options)
		else
			return $this->values;
	}

	public function setState($state) {
		$this->values = $state;
	}
}

?>
