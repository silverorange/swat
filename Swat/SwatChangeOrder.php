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
		$this->addStyleSheet('swat/styles/swat-change-order.css');
	}

	/**
	 * Displays this change-order control
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
		$div_tag->id = "{$this->id}_control";

		if ($this->isSensitive())
			$div_tag->class = 'swat-change-order';
		else
			$div_tag->class = 'swat-change-order swat-change-order-insensitive';

		$div_tag->open();

		$list_div = new SwatHtmlTag('div');
		$list_div->style = "width: {$this->width}; height: {$this->height};";
		$list_div->id = "{$this->id}_list";
		$list_div->class = 'swat-change-order-list';
		$list_div->open();

		$option_tag = new SwatHtmltag('div');
		$option_tag->onclick = "{$this->id}_obj.choose(this);";
		$option_tag->class = 'swat-change-order-item';
		if ($this->onclick !== null) 
			$option_tag->onclick.=' '.$this->onclick;
			
		$count = 0;
		foreach ($this->options as $key => $option) {
			$option_tag->content = $option;
			$option_tag->display();
			$count++;
		}

		$list_div->close();

		$this->displayButtons();

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
		
		printf("%s_obj = new SwatChangeOrder('%s', %s);\n",
			$this->id,
			$this->id,
			$this->isSensitive() ? 'true' : 'false');

		echo "\n//]]>";
		echo '</script>';
	}

	private function displayButtons()
	{
		$buttons_div = new SwatHtmlTag('div');
		$buttons_div->class = 'swat-change-order-buttons';
		$buttons_div->open();
		
		$btn_tag = new SwatHtmlTag('input');
		$btn_tag->type = 'button';
		if (!$this->isSensitive())
			$btn_tag->disabled = 'disabled';

		$btn_tag->value = Swat::_('Move to Top');
		$btn_tag->onclick = "{$this->id}_obj.moveToTop();";
		$btn_tag->name = "{$this->id}_buttons";
		$btn_tag->class = 'swat-change-order-top';
		$btn_tag->display();
		
		echo '<br />';

		$btn_tag->value = Swat::_('Move Up');
		$btn_tag->onclick = "{$this->id}_obj.moveUp();";
		$btn_tag->name = "{$this->id}_buttons";
		$btn_tag->class = 'swat-change-order-up';
		$btn_tag->display();
		
		echo '<br />';

		$btn_tag->value = Swat::_('Move Down');
		$btn_tag->onclick = "{$this->id}_obj.moveDown();";
		$btn_tag->name = "{$this->id}_buttons";
		$btn_tag->class = 'swat-change-order-down';
		$btn_tag->display();
		
		echo '<br />';
		
		$btn_tag->value = Swat::_('Move to Bottom');
		$btn_tag->onclick = "{$this->id}_obj.moveToBottom();";
		$btn_tag->name = "{$this->id}_buttons";
		$btn_tag->class = 'swat-change-order-bottom';
		$btn_tag->display();

		$buttons_div->close();
	}
}

?>
