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
	 * Width of the order box (in stylesheet units)
	 *
	 * @var string
	 */
	public $width = '300px';

	/**
	 * Height of the order box (in stylesheet units)
	 *
	 * @var string
	 */
	public $height = '180px';

	/**
	 * Onclick HTML attribute of the buttons
	 *
	 * @var string
	 */
	//TODO: get rid of this (currently AdminOrder is dependant on it, as well as line 105-106 below
	public $onclick = null;

	/**
	 * Creates a new change-order widget
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->requires_id = true;

		$this->addJavaScript('swat/javascript/swat-change-order.js');
	}

	/**
	 * Displays this changeorder control
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		if ($this->values !== null) {
			$array = array();
			foreach ($this->values as $id)
				$array[$id] = $this->options[$id];

			$this->options = $array;
		}

		$div_tag = new SwatHtmlTag('div');
		$div_tag->class = 'swat-order-control-div';
		$div_tag->open();

		$list_div = new SwatHtmlTag('div');
		$list_div->style = "width: {$this->width}; height: {$this->height};";
		$list_div->id = "{$this->id}_list";
		$list_div->class = 'swat-order-control-list';
		$list_div->open();

		$option_tag = new SwatHtmltag('div');
		$option_tag->onclick = "{$this->id}_obj.choose(this);";
		if ($this->onclick !== null) 
			$option_tag->onclick.=' '.$this->onclick;
			
		$option_tag->class = 'swat-order-control-active';
		$count = 0;
		foreach ($this->options as $key => $option) {
			$option_tag->content = $option;
			$option_tag->display();
			$option_tag->class = 'swat-order-control';
			$count++;
		}

		$list_div->close();

		$controls_div = new SwatHtmlTag('div');
		$controls_div->class = 'swat-order-control-buttons';
		$controls_div->open();

		// TODO: these buttons should use class names not ids
		$top_btn = new SwatHtmlTag('input');
		$top_btn->type = 'button';
		$top_btn->value = Swat::_('Move to Top');
		$top_btn->onclick = "{$this->id}_obj.moveToTop();";
		$top_btn->id = "swat-order-control-top";
		$top_btn->display();
		
		echo '<br />';

		$up_btn = new SwatHtmlTag('input');
		$up_btn->type = 'button';
		$up_btn->value = Swat::_('Move Up');
		$up_btn->onclick = "{$this->id}_obj.moveUp();";
		$up_btn->id = "swat-order-control-up";
		$up_btn->display();
		
		echo '<br />';

		$down_btn = new SwatHtmlTag('input');
		$down_btn->type = 'button';
		$down_btn->value = Swat::_('Move Down');
		$down_btn->onclick = "{$this->id}_obj.moveDown();";
		$down_btn->id = "swat-order-control-down";
		$down_btn->display();
		
		echo '<br />';
		
		$bottom_btn = new SwatHtmlTag('input');
		$bottom_btn->type = 'button';
		$bottom_btn->value = Swat::_('Move to Bottom');
		$bottom_btn->onclick = "{$this->id}_obj.moveToBottom();";
		$bottom_btn->id = "swat-order-control-bottom";
		$bottom_btn->display();

		$controls_div->close();

		echo '<div style="clear: both;"></div>';

		$hidden_tag = new SwatHtmlTag('input');
		$hidden_tag->type = 'hidden';
		$hidden_tag->id = $this->id;
		$hidden_tag->name = $this->id;
		$hidden_tag->value = implode(',', array_keys($this->options));
		$hidden_tag->display();

		$div_tag->close();

		$this->displayJavascript();
	}

	public function process()
	{
		$this->values = explode(',', $_POST[$this->id]);
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

	private function displayJavascript()
	{
		echo '<script type="text/javascript">';
		echo "//<![CDATA[\n";

		$num_elements = count($this->options);
		
		echo "{$this->id}_obj = new SwatChangeOrder('{$this->id}');\n";

		echo "\n//]]>";
		echo '</script>';
	}
}

?>
