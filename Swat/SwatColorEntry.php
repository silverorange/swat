<?php

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatState.php';

/**
 * A color selector widget with palette
 *
 * The colors are stored internally and accessed externally as 3 or 6 digit
 * hexidecimal values.
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatColorEntry extends SwatControl implements SwatState
{
	/**
	 * Selected color of this widget in hexidecimal representation
	 *
	 * @var string
	 */
	public $value = null;

	/**
	 * Whether this entry widget is required or not
	 *
	 * Must have a non-empty value when processed.
	 *
	 * @var boolean
	 */
	public $required = false;

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

		$this->addJavaScript('swat/javascript/swat-color-entry.js');
		$this->addJavaScript('swat/javascript/swat-z-index-manager.js');
		$this->addStyleSheet('swat/styles/swat-color-entry.css');
	}

	/**
	 * Initializes this color selector widget 
	 */
	public function init()
	{
		parent::init();

		// an id is required for this widget.
		if ($this->id === null)
			$this->id = $this->getUniqueId();
	}

	/**
	 * Displays this color selection widget
	 *
	 * This draws the color palette and outputs appropriate controlling
	 * javascript.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'text';
		$input_tag->id = $this->id;
		$input_tag->name = $this->id;
		$input_tag->value = $this->value;
		$input_tag->class = 'swat-color-entry-input';
		$input_tag->disabled = 'true';
		$input_tag->display();

		$link_tag = new SwatHtmlTag('a');
		$link_tag->href = "javascript:{$this->id}_obj.toggle();";
		$link_tag->open();

		$img_tag = new SwatHtmlTag('img');
		$img_tag->src = 'swat/images/color-palette.png';
		$img_tag->id = $this->id.'_toggle';
		$img_tag->class = 'swat-color-entry-toggle';
		$img_tag->display();

		$link_tag->close();

		$this->displayPalette();
		$this->displayJavascript();
	}

	/**
	 * Processes this entry widget
	 *
	 * If any validation type errors occur, an error message is attached to
	 * this entry widget.
	 */
	public function process()
	{
		if (strlen($_POST[$this->id]) == 0)
			$this->value = null;
		else
			$this->value = $_POST[$this->id];

		$len = ($this->value === null) ? 0 : strlen($this->value);

		if (!$this->required && $this->value === null) {
			return;

		} elseif ($this->value === null) {
			$msg = Swat::_('The %s field is required.');
			$this->addMessage(new SwatMessage($msg, SwatMessage::ERROR));
			
		} elseif ($this->maxlength !== null && $len > $this->maxlength) {

			$msg = sprintf(Swat::_('The %%s field must be less than %s characters.'),
				$this->maxlength);

			$this->addMessage(new SwatMessage($msg, SwatMessage::ERROR));

		} elseif ($this->minlength !== null && $len < $this->minlength) {

			$msg = sprintf(Swat::_('The %%s field must be more than %s characters.'),
				$this->minlength);

			$this->addMessage(new SwatMessage($msg, SwatMessage::ERROR));

		}
	}

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

	/**
	 * Sets the current state of this color selector
	 *
	 * @param string $state the new state of this color selector.
	 *
	 * @see SwatState::setState()
	 */
	public function setState($state)
	{
		$this->value = new SwatDate($state);
	}

	/**
	 * Includes the javascript required for this control to function
	 *
	 * This creates an instance of the JavaScript object SwatColorEntry with
	 * the name $this->id.'_obj'.
	 */
	private function displayJavascript()
	{
		echo '<script type="text/javascript">';
		echo "//<![CDATA[\n";
		echo "var {$this->id}_obj = new SwatColorEntry('{$this->id}');";
		echo "\n//]]>";
		echo '</script>';
	}

	/**
	 * Displays the color palette XHTML
	 */
	private function displayPalette()
	{
		$wrapper_div = new SwatHtmlTag('div');
		$wrapper_div->id = $this->id.'_wrapper';
		$wrapper_div->class = 'swat-color-entry-wrapper';

		$control_div = new SwatHtmlTag('div');
		$control_div->content = '&nbsp;';

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
		$swatch_div->content = '&nbsp;';

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
}

?>
