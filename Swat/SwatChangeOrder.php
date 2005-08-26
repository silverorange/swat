<?php

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatState.php';

/**
 * An element ordering widget
 *
 * This widget uses javascript to present an orderable list of elements. The
 * ordering of elements is what this widget returns.
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatChangeOrder extends SwatControl implements SwatState
{

	/**
	 * Order options
	 *
	 * An array containing the options to display. The array should be
	 * of the form $id => $title.
	 *
	 * @var array
	 */
	public $options = array();

	/**
	 * Value ordered array
	 *
	 * The current ordering of options in the widget. If null, options are
	 * displayed in the order of the options array.
	 *
	 * @var array
	 */
	public $values = null;
	
	/**
	 * Width of the order box (either pixels or %)
	 *
	 * @var string
	 */
	public $width = '400';

	/**
	 * Height of the order box (either pixels or %)
	 *
	 * @var string
	 */
	public $height = '150';
	
	/**
	 * onclick html attribute of the buttons
	 *
	 * @var string
	 */
	public $onclick = null;
	
	public function display()
	{
		
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
		$iframe_tag->id = $this->id.'_iframe';
		$iframe_tag->align = 'left';
		$iframe_tag->class = 'swat-order-control-iframe';
		$iframe_tag->open();
		$iframe_tag->close();
		
		$this->displayJavascript();

		$obj = $this->id.'_obj';

		$hidden_tag = new SwatHtmlTag('input');
		$hidden_tag->type = 'hidden';
		$hidden_tag->id = $this->id;
		$hidden_tag->name = $this->id;
		$hidden_tag->value = implode(',', array_keys($this->options));
		$hidden_tag->display();
		
		$up_btn = new SwatHtmlTag('input');
		$up_btn->type = 'button';
		$up_btn->value = Swat::_('Move Up');
		$up_btn->onclick = $obj.".updown('up');";
		if ($this->onclick !== null)
			$up_btn->onclick.= $this->onclick;

		$up_btn->display();

		echo '<br />';
	
		$down_btn = new SwatHtmlTag('input');
		$down_btn->type = 'button';
		$down_btn->value = Swat::_('Move Down');
		$down_btn->onclick = $obj.".updown('down');";
		if ($this->onclick !== null)
			$down_btn->onclick.= $this->onclick;

		$down_btn->display();

		// TODO: maybe clean up the way the buttons are positioned
		echo '<div style="clear:left;"></div>';
	
		$div_tag->close();
	}	

	public function process()
	{
		$this->values = explode(',', $_POST[$this->id]);
	}

	private function displayJavascript()
	{
		echo '<script type="text/javascript" src="swat/javascript/swat-change-order.js"></script>';

		echo '<script type="text/javascript">';
		echo "//<![CDATA[\n";
		
		$warning = Swat::_('You must first select the item to reorder.');
		// TODO: figure out how to make the stylesheets work better.
		$style = '../swat/swat.css';
		$values = array();
		foreach ($this->options as $id => $title)
			$values[$id] = addslashes($title);

		$values = implode("','", $values);
		
		printf("\n {$this->id}_obj = new SwatChangeOrder('%s'); ",
				$this->id, $warning);

		echo "\n {$this->id}_obj.stylesheet = '{$style}';";
		echo "\n {$this->id}_obj.draw(new Array('{$values}')); ";

		echo "\n//]]>";
		echo '</script>';
	}

	public function getState()
	{
		if ($this->values === null)
			return array_keys($this->options);
		else
			return $this->values;
	}

	public function setState($state)
	{
		$this->values = $state;
	}
}

?>
