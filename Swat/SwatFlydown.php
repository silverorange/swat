<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatControl.php');
require_once('Swat/SwatHtmlTag.php');

/**
 * A flydown (aka combo-box) selection widget.
 */
class SwatFlydown extends SwatControl {
	
	/**
	 * An array of options for the flydown in the form value => title.
	 * @var array
	 */
	public $options = null;

	/**
	 * The value of the selected option, or null.
	 * @var string
	 */
	public $value = null;

	/**
	 * The onchange attribute of the HTML select tag, or null.
	 * @var string
	 */
	public $onchange = null;

	function display() {
		$select_tag = new SwatHtmlTag('select');
		$select_tag->name = $this->name;
		$select_tag->id = $this->name;

		if ($this->onchange != null)
			$select_tag->onchange = $this->onchange;

		$option_tag = new SwatHtmlTag('option');

		$select_tag->open();

		if ($this->options != null) {
			foreach ($this->options as $value => $title) {
				$option_tag->value = (string)$value;
				$option_tag->removeAttr('selected');
				
				/* Type juggling evalutes the expression (null == 0) as true.
				 * This can occur in the second part of the expression below
				 * ($this->value == $value), so we first explicitly check for
				 * null.  The indentical operator (===) is not used in the 
				 * second part since type juggling is desired by some users of
				 * this class that treat the $value property as an integer.
				 */
				if ($this->value !== null && $this->value == $value)
					$option_tag->selected = "selected";

				$option_tag->open();
				echo $title;
				$option_tag->close();
			}
		}

		$select_tag->close();
	}	

	function process() {
		$this->value = $_POST[$this->name];
	}
}

?>
