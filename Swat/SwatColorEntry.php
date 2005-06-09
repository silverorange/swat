<?php

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatState.php';

/**
 * A color entry widget with palette
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatColorEntry extends SwatControl implements SwatState
{
	
	/**
	 * Color of the widget in Hex
	 *
	 * @var string
	 */
	public $value = null;
	
	/**
	 * Whether this entry widget is required or not
	 *
	 * Must have a non-empty value when processed.
	 *
	 * @var bool
	 */
	public $required = false;

	public function display()
	{
		$this->displayJavascript();
		
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
		$img_tag->src = 'swat/images/b_palette.gif';
		$img_tag->id = $this->id.'_toggle';
		$img_tag->class = 'swat-color-entry-toggle';

		$link_tag->close();

		$this->displayPalette();

		echo '<script type="text/javascript">';
		echo "var {$this->name}_obj = new SwatColorEntry('{$this->name}');";
		echo '</script>';
	}
	
	private function displayJavascript()
	{
		echo '<script type="text/javascript">';
		include_once 'Swat/javascript/swat-color-entry.js';
		echo '</script>';
	}

	private function displayPalette()
	{
		//TODO: clean this up
		?>
		<div id="<?=$this->id?>_wrapper" class="swat-color-entry-wrapper">

		<div class="palette" id="<?=$this->id?>_color_palette">&nbsp;</div>
		<div class="scale" id="<?=$this->id?>_grayscale">&nbsp;</div>
		<div class="scale" id="<?=$this->id?>_tintscale">&nbsp;</div>

		<div class="swatches">
			<div class="swatch" id="<?=$this->id?>_swatch">&nbsp;</div>
			<div class="active" id="<?=$this->id?>_active_swatch">&nbsp;</div>
		</div>

		<div class="palette-footer">
			<div class="rgb">
			r: <input maxlength="3" type="text"
				onkeyup="<?=$this->id?>_obj.setRGB()"
				id="<?=$this->id?>_color_input_r"
				class="rgb-input" />
			g: <input maxlength="3" type="text"
				onkeyup="<?=$this->id?>_obj.setRGB()"
				id="<?=$this->id?>_color_input_g"
				class="rgb-input" />
			b: <input maxlength="3" type="text"
				onkeyup="<?=$this->id?>_obj.setRGB()"
				id="<?=$this->id?>_color_input_b"
				class="rgb-input" />
			</div>
	
			<div class="hex">
				hex: <input maxlength="6" type="text"
					onkeyup="<?=$this->id?>_obj.setHex(this.value)" id="<?=$this->id?>_color_input_hex" class="hex-input" />
			</div>
		</div>
	
		<div class="palette-buttons">
			<input type="button" class="button-set" onclick="<?=$this->id?>_obj.apply()" value="<?=_S("Set Color")?>">
			<input type="button" class="button-cancel" onclick="<?=$this->id?>_obj.none()" value="<?=_S("Set None")?>">
			<input type="button" class="button-cancel" onclick="<?=$this->id?>_obj.toggle()" value="<?=_S("Cancel")?>">
		</div>
		</div>
		<?php
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
			$msg = _S("The %s field is required.");
			$this->addMessage(new SwatMessage($msg, SwatMessage::USER_ERROR));
			
		} elseif ($this->maxlength !== null && $len > $this->maxlength) {
			$msg = sprintf(_S("The %%s field must be less than %s characters."),
				$this->maxlength);
			$this->addMessage(new SwatMessage($msg, SwatMessage::USER_ERROR));
			
		} elseif ($this->minlength !== null && $len < $this->minlength) {
			$msg = sprintf(_S("The %%s field must be more than %s characters."),
				$this->minlength);
			$this->addMessage(new SwatMessage($msg, SwatMessage::USER_ERROR));
			
		}
	}
	
	public function getState()
	{
		if ($this->value === null)
			return null;
		else
			return $this->value;	
	}

	public function setState($state)
	{
		$this->value = new SwatDate($state);
	}

}

?>
