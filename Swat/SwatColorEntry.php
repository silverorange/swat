<?php
require_once('Swat/SwatControl.php');
require_once('Swat/SwatState.php');

/**
 * A color entry widget with palette
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatColorEntry extends SwatControl implements SwatState {
	
	/**
	 * Color of the widget in hex, or null.
	 * @var string
	 */
	public $value = null;
	
	/**
	 * Required
	 *
	 * Must have a non-empty value when processed.
	 * @var bool
	 */
	public $required = false;

	public function init() {
		
	}
	
	public function display() {
		$this->displayJavascript();
		
		?>
		<input type="text" id="<?=$this->name?>" name="<?=$this->name?>"
			value="<?=$this->value?>" class="swat-color-entry-input"
			disabled="true" />
		<a href="javascript:<?=$this->name?>_obj.toggle();">
			<img src="swat/images/b_palette.gif" id="<?=$this->name?>_toggle" class="swat-color-entry-toggle" />
		</a>
		
		<?php
		$this->displayPalette();
		?>
		<script type="text/javascript">
			var <?=$this->name?>_obj = new SwatColorEntry('<?=$this->name?>');
		</script>
		<?php
	}
	
	public function process() {
	
	}
	
	private function displayJavascript() {
		echo '<script type="text/javascript">';
		include_once('Swat/javascript/swat-color-entry.js');
		echo '</script>';
	}

	private function displayPalette() {
		?>
		<div id="<?=$this->name?>_wrapper" class="swat-color-entry-wrapper">

		<div class="palette" id="<?=$this->name?>_color_palette">&nbsp;</div>
		<div class="scale" id="<?=$this->name?>_grayscale">&nbsp;</div>
		<div class="scale" id="<?=$this->name?>_tintscale">&nbsp;</div>

		<div class="swatches">
			<div class="swatch" id="<?=$this->name?>_swatch">&nbsp;</div>
			<div class="active" id="<?=$this->name?>_active_swatch">&nbsp;</div>
		</div>

		<div class="palette-footer">
			<div class="rgb">
			r: <input maxlength="3" type="text"
				onkeyup="<?=$this->name?>_obj.setRGB()"
				id="<?=$this->name?>_color_input_r"
				class="rgb-input" />
			g: <input maxlength="3" type="text"
				onkeyup="<?=$this->name?>_obj.setRGB()"
				id="<?=$this->name?>_color_input_g"
				class="rgb-input" />
			b: <input maxlength="3" type="text"
				onkeyup="<?=$this->name?>_obj.setRGB()"
				id="<?=$this->name?>_color_input_b"
				class="rgb-input" />
			</div>
	
			<div class="hex">
				hex: <input maxlength="6" type="text"
					onkeyup="<?=$this->name?>_obj.setHex(this.value)" id="<?=$this->name?>_color_input_hex" class="hex-input" />
			</div>
		</div>
	
		<div class="palette-buttons">
			<input type="button" class="button-set" onclick="<?=$this->name?>_obj.apply()" value="Set Color">
			<input type="button" class="button-cancel" onclick="<?=$this->name?>_obj.none()" value="Set None">
			<input type="button" class="button-cancel" onclick="<?=$this->name?>_obj.toggle()" value="Cancel">
		</div>
		</div>
		<?php
	}
	
	public function getState() {
		if ($this->value === null)
			return null;
		else
			return $this->value->getDate();	
	}

	public function setState($state) {
		$this->value = new SwatDate($state);
	}

}
?>
