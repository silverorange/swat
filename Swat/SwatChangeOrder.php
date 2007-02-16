<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatOptionControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatState.php';
require_once 'Swat/SwatYUI.php';

/**
 * An element ordering widget
 *
 * This widget uses JavaScript to present an orderable list of elements. The
 * ordering of elements is what this widget returns.
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatChangeOrder extends SwatOptionControl implements SwatState
{
	// {{{ public properties

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

	// }}}
	// {{{ public function __construct()

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
		$this->unique_values = true;

		$yui = new SwatYUI(array('dom', 'event'));
		$this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());

		$this->addJavaScript('packages/swat/javascript/swat-change-order.js',
			Swat::PACKAGE_ID);

		$this->addJavaScript('packages/swat/javascript/swat-z-index-manager.js',
			Swat::PACKAGE_ID);

		$this->addStyleSheet('packages/swat/styles/swat-change-order.css',
			Swat::PACKAGE_ID);
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this change-order control
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		if ($this->values !== null) {
			$ordered_options = array();
			foreach ($this->values as $value)
				$ordered_options[$value] = $this->getOption($value);

			$this->options = $ordered_options;
		}

		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id;
		$div_tag->class = $this->getCSSClassString();
		$div_tag->open();

		$list_div = new SwatHtmlTag('div');
		$list_div->style = "width: {$this->width}; height: {$this->height};";
		$list_div->id = "{$this->id}_list";
		$list_div->class = 'swat-change-order-list';
		$list_div->open();

		$option_div = new SwatHtmltag('div');
		$option_div->class = 'swat-change-order-item';

		$count = 0;
		foreach ($this->options as $option) {
			$title = ($option->title === null) ? '' : $option->title;
			$option_div->setContent($title, $option->content_type);
			$option_div->display();
			$count++;
		}

		$list_div->close();

		$this->displayButtons();

		echo '<div style="clear: both;"></div>';

		$hidden_tag = new SwatHtmlTag('input');
		$hidden_tag->type = 'hidden';
		$hidden_tag->id = $this->id.'_value';
		$hidden_tag->name = $this->id;
		$hidden_tag->value = implode(',', array_keys($this->options));
		$hidden_tag->display();

		$div_tag->close();

		$this->displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ public function process()

	public function process()
	{
		parent::process();

		$data = &$this->getForm()->getFormData();
		$this->values = explode(',', $data[$this->id]);
	}

	// }}}
	// {{{ public function getNote()

	/**
	 * Gets a note letting the user know drag-and-drop is available for
	 * ordering items
	 *
	 * @return SwatMessage a note letting the user know drag-and-drop is
	 *                      available for ordering items.
	 *
	 * @see SwatControl::getNote()
	 */
	public function getNote()
	{
		$message = Swat::_('Items can be ordered by dragging-and-dropping '.
			'with the mouse.');

		return new SwatMessage($message);
	}

	// }}}
	// {{{ public function getState()

	public function getState()
	{
		if ($this->values === null)
			return array_keys($this->options);
		else
			return $this->values;
	}

	// }}}
	// {{{ public function setState()

	public function setState($state)
	{
		$this->values = $state;
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this change-order
	 * widget 
	 *
	 * @return array the array of CSS classes that are applied to this
	 *                change-order widget.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-change-order');

		if (!$this->isSensitive())
			$classes[] = 'swat-change-order-insensitive';

		$classes = array_merge($classes, $this->classes);
		return $classes;
	}

	// }}}
	// {{{ protected function getInlineJavaScript()

	/**
	 * Gets the inline JavaScript required by this change-order control
	 *
	 * @return string the inline JavaScript required by this change-order
	 *                 control.
	 */
	protected function getInlineJavaScript()
	{
		return sprintf("var %s_obj = new SwatChangeOrder('%s', %s);",
			$this->id,
			$this->id,
			$this->isSensitive() ? 'true' : 'false');
	}

	// }}}
	// {{{ private function displayButtons()

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

	// }}}
}

?>
