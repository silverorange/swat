<?php

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatState.php';

/**
 * Simple color selector widget.
 *
 * This color selector displays a simple palette to the user with a set of
 * predefined color choices. It requires javascript to work correctly.
 *
 * @package   Swat
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatSimpleColorEntry extends SwatControl implements SwatState
{
	/**
	 * Selected color
	 *
	 * The selected color in three or six digit hexidecimal representation.
	 *
	 * @var string
	 */
	public $value = null;

	/**
	 * Array of colors to display in this color selector
	 *
	 * The array is flat and contains three or six digit hex color
	 * codes.
	 *
	 * @var array
	 *
	 * @todo create a default palette for the simple color entry widget.
	 */
	public $colors = array(
		'aa2208', 'ff3311', '667799', 'ffffff',
		'669977', '779966', '70ff90', '000066',
		'000099', '0000ff', '7090ff', 'ffffff',
		'885522', 'ffee88', 'eeeeee', '667799'
		);

	/**
	 * Initializes this simple color selector widget 
	 */
	public function init()
	{
		// an id is required for this widget.
		if ($this->id === null)
			$this->id = $this->getUniqueId();
	}
	
	/**
	 * Displays this simple color selector widget
	 */
	public function display()
	{
		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'hidden';
		$input_tag->id = $this->id;
		$input_tag->name = $this->id;
		$input_tag->value = $this->value;
		$input_tag->display();

		$swatch_div = new SwatHtmlTag('div');
		$swatch_div->class = 'swat-simple-color-entry-swatch';
		$swatch_div->id = $this->id.'_swatch';
		$swatch_div->content = '&nbsp;';
		$swatch_div->display();

		$img_tag = new SwatHtmlTag('img');
		$img_tag->src = 'swat/images/b_palette.gif';
		$img_tag->class = 'swat-simple-color-entry-toggle';
		$img_tag->id = $this->id.'_toggle';
		$img_tag->onmousedown = $this->id.'.toggle();';

		$img_tag->display();
		
		echo '<br />';

		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id.'_palette';
		$div_tag->class = 'swat-simple-color-palette-hidden';
		$div_tag->content = '&nbsp;';
		$div_tag->display();

		$this->displayJavascript();
	}

	/**
	 * Gets the current state of this simple color selector widget
	 *
	 * @return string the current state of this simple color selector widget.
	 *
	 * @see SwatState::getState()
	 */
	public function getState()
	{
		return $this->value;
	}

	/**
	 * Sets the current state of this simple color selector widget
	 *
	 * @param string $state the new state of this simple color selector widget.
	 *
	 * @see SwatState::setState()
	 */
	public function setState($state)
	{
		$this->value = $state;
	}

	/**
	 * Displays simple color selector javascript
	 *
	 * The javascript is the majority of the simple color selector code
	 */
	private function displayJavascript()
	{
		static $shown = false;

		if (!$shown) {
			echo '<script type="text/javascript" src="swat/javascript/swat-simple-color-entry.js"></script>';

			$shown = true;
		}

		$colors = "'".implode("', '", $this->colors)."'";
		
		echo '<script type="text/javascript">'."\n";
		
		echo "{$this->id} = new SwatSimpleColorEntry(".
			"'{$this->id}', [{$colors}]);";

		echo "\n</script>";
	}
}

?>
