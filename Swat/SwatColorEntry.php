<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatInputControl.php';
require_once 'Swat/SwatState.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * A color selector widget with palette
 *
 * The colors are stored internally and accessed externally as 3 or 6 digit
 * hexidecimal values.
 *
 * @package    Swat
 * @copyright  2005-2014 silverorange
 * @license    http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @deprecated This widget is unmaintained and has known browser-compatibility
 *             issues. At this point, either a suitable replacement is needed
 *             or the widget JavaScript needs some major overhauling.
 */
class SwatColorEntry extends SwatInputControl implements SwatState
{
	// {{{ public properties

	/**
	 * Selected color of this widget in hexidecimal representation
	 *
	 * @var string
	 */
	public $value = null;

	/**
	 * Access key
	 *
	 * Access key for this color input control, for keyboard nagivation.
	 *
	 * @var string
	 */
	public $access_key = null;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new color entry widget
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->requires_id = true;

		$this->addStyleSheet('packages/swat/styles/swat-color-entry.css');
		$this->addJavaScript('packages/swat/javascript/swat-color-entry.js');
		$this->addJavaScript(
			'packages/swat/javascript/swat-z-index-manager.js'
		);
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this color selection widget
	 *
	 * This draws the color palette and outputs appropriate controlling
	 * JavaScript.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		parent::display();

		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id;
		$div_tag->class = $this->getCSSClassString();
		$div_tag->open();

		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'text';
		$input_tag->id = $this->id.'_value';
		$input_tag->name = $this->id;
		$input_tag->value = $this->value;
		$input_tag->class = 'swat-color-entry-input';
		$input_tag->disabled = 'disabled';
		$input_tag->accesskey = $this->access_key;

		$input_tag->display();

		$link_tag = new SwatHtmlTag('a');
		$link_tag->href = "javascript:{$this->id}_obj.toggle();";
		$link_tag->open();

		$img_tag = new SwatHtmlTag('img');
		$img_tag->src = 'packages/swat/images/color-palette.png';
		$img_tag->alt = Swat::_('Color entry toggle graphic.');
		$img_tag->id = $this->id.'_toggle';
		$img_tag->class = 'swat-color-entry-toggle';
		$img_tag->display();

		$link_tag->close();

		$this->displayPalette();

		$div_tag->close();

		Swat::displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ public function process()

	/**
	 * Processes this color entry widget
	 *
	 * If any validation type errors occur, an error message is attached to
	 * this entry widget.
	 */
	public function process()
	{
		parent::process();

		$data = &$this->getForm->getFormData();

		if (!isset($data[$this->id.'_value']))
			return;
		elseif ($data[$this->id.'_value'] == '')
			$this->value = null;
		else
			$this->value = $data[$this->id.'_value'];

		$len = ($this->value === null) ? 0 : mb_strlen($this->value);

		if (!$this->required && $this->value === null) {
			return;

		} elseif ($this->value === null) {
			$message = Swat::_('The %s field is required.');
			$this->addMessage(new SwatMessage($message, 'error'));
		}
	}

	// }}}
	// {{{ public function getState()

	/**
	 * Gets the current state of this color selector
	 *
	 * @return string the current state of this color selector.
	 *
	 * @see SwatState::getState()
	 */
	public function getState()
	{
		if ($this->value === null)
			return null;
		else
			return $this->value;
	}

	// }}}
	// {{{ public function setState()

	/**
	 * Sets the current state of this color selector
	 *
	 * @param string $state the new state of this color selector.
	 *
	 * @see SwatState::setState()
	 */
	public function setState($state)
	{
		$hex_color = '/#([a-f0-9]{3}|[a-f0-9]{6})/i';
		if (preg_match($hex_color, $state) === 1)
			$this->value = $state;
	}

	// }}}
	// {{{ protected function getInlineJavaScript()

	/**
	 * Gets the inline JavaScript required for this control to function
	 *
	 * The inline JavaScript creates an instance of the JavaScript object
	 * SwatColorEntry with the name $this->id.'_obj'.
	 *
	 * @return string the inline JavaScript required for this control to
	 *                 function.
	 */
	protected function getInlineJavaScript()
	{
		return "var {$this->id}_obj = new SwatColorEntry('{$this->id}');";
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this color entry
	 * widget
	 *
	 * @return array the array of CSS classes that are applied to this color
	 *                entry widget.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-color-entry');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
	// {{{ private function displayPalette()

	/**
	 * Displays the color palette XHTML
	 */
	private function displayPalette()
	{
		$wrapper_div = new SwatHtmlTag('div');
		$wrapper_div->id = $this->id.'_wrapper';
		$wrapper_div->class = 'swat-color-entry-wrapper';

		$control_div = new SwatHtmlTag('div');
		$control_div->setContent('&nbsp;');

		$wrapper_div->open();

		$control_div->class = 'palette';
		$control_div->id = $this->id.'_color_palette';
		$control_div->display();

		$control_div->class = 'scale';
		$control_div->id = $this->id.'_grayscale';
		$control_div->display();

		$control_div->id = $this->id.'_tintscale';
		$control_div->display();

		echo '<div class="swatches">';

		$swatch_div = new SwatHtmlTag('div');
		$swatch_div->setContent('&nbsp;');

		$swatch_div->class = 'swatch';
		$swatch_div->id = $this->id.'_swatch';
		$swatch_div->display();

		$swatch_div->class = 'active';
		$swatch_div->id = $this->id.'_active_swatch';
		$swatch_div->display();

		echo '</div>';
		echo '<div class="palette-footer"><div class="rgb">';

		$rgb_value_input = new SwatHtmlTag('input');
		$rgb_value_input->type = 'text';
		$rgb_value_input->onkeyup = $this->id.'_obj.setRGB();';
		$rgb_value_input->class = 'rgb-input';

		echo 'r: ';
		$rgb_value_input->id = $this->id.'_color_input_r';
		$rgb_value_input->display();

		echo 'g: ';
		$rgb_value_input->id = $this->id.'_color_input_g';
		$rgb_value_input->display();

		echo 'b: ';
		$rgb_value_input->id = $this->id.'_color_input_b';
		$rgb_value_input->display();

		echo '</div><div class="hex">';

		echo 'hex: ';
		$rgb_value_input->maxlength = 6;
		$rgb_value_input->onkeyup = $this->id.'_obj.setHex(this.value);';
		$rgb_value_input->id = $this->id.'_color_input_hex';
		$rgb_value_input->class = 'hex-input';
		$rgb_value_input->display();

		echo '</div></div><div class="palette-buttons">';

		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'button';

		$input_tag->class = 'button-set';
		$input_tag->onclick = $this->id.'_obj.apply();';
		$input_tag->value = Swat::_('Set Color');
		$input_tag->display();

		$input_tag->class = 'button-cancel';
		$input_tag->onclick = $this->id.'_obj.none();';
		$input_tag->value = Swat::_('Set None');
		$input_tag->display();

		$input_tag->onclick = $this->id.'_obj.toggle();';
		$input_tag->value = Swat::_('Cancel');
		$input_tag->display();

		echo '</div>';

		$wrapper_div->close();
	}

	// }}}
}

?>
