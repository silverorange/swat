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
	public $width = '400px';

	/**
	 * Height of the order box (in stylesheet units)
	 *
	 * @var string
	 */
	public $height = '150px';

	/**
	 * Onclick HTML attribute of the buttons
	 *
	 * @var string
	 */
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

		$this->addJavaScript('swat/javascript/swat-change-order.js');
	}

	/**
	 * Initializes this change-order widget 
	 */
	public function init()
	{
		// an id is required for this widget.
		if ($this->id === null)
			$this->id = $this->getUniqueId();
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

		$up_btn = new SwatHtmlTag('input');
		$up_btn->type = 'button';
		$up_btn->value = Swat::_('Move Up');
		$up_btn->onclick = "{$this->id}_obj.updown('up');";
		if ($this->onclick !== null)
			$up_btn->onclick.= $this->onclick;

		$up_btn->display();

		$list_div = new SwatHtmlTag('div');
		$list_div->style = "width: {$this->width}; height: {$this->height};";
		$list_div->id = "{$this->id}_list";
		$list_div->class = 'swat-order-control-list';
		$list_div->open();

		$option_tag = new SwatHtmltag('div');
		$option_tag->onclick = "{$this->id}_obj.choose(this);";
		$option_tag->class = 'swat-order-control-active';
		$count = 0;
		foreach ($this->options as $key => $option) {
			$option_tag->id = "{$this->id}_option_{$count}";
			$option_tag->content = $option;
			$option_tag->display();
			$option_tag->class = 'swat-order-control';
			$count++;
		}

		$list_div->close();

		$down_btn = new SwatHtmlTag('input');
		$down_btn->type = 'button';
		$down_btn->value = Swat::_('Move Down');
		$down_btn->onclick = "{$this->id}_obj.updown('down');";
		if ($this->onclick !== null)
			$down_btn->onclick.= $this->onclick;

		$down_btn->display();
		
		$this->displayJavascript();

		$hidden_tag = new SwatHtmlTag('input');
		$hidden_tag->type = 'hidden';
		$hidden_tag->id = $this->id;
		$hidden_tag->name = $this->id;
		$hidden_tag->value = implode(',', array_keys($this->options));
		$hidden_tag->display();

		$div_tag->close();
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
		
		echo "{$this->id}_obj = new SwatChangeOrder('{$this->id}', ".
			"{$num_elements});\n";

		echo "\n//]]>";
		echo '</script>';
	}
}

?>
