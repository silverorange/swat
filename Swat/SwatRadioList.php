<?php
require_once('Swat/SwatControl.php');
require_once('Swat/SwatHtmlTag.php');

/**
 * A radio list selection widget
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatRadioList extends SwatControl {
	
	/**
	 * Radio list options
	 *
	 * An array of options for the radio list in the form value => title.
	 * @var array
	 */
	public $options = null;

	/**
	 * List value 
	 *
	 * The value of the selected item, or null.
	 * @var string
	 */
	public $value = null;

	/**
	 * On change
	 *
	 * The onchange attribute of the HTML input type=radio tags, or null.
	 * @var string
	 */
	public $onchange = null;

	public function display() {
		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'radio';
		$input_tag->name = $this->name;
		if ($this->onchange != null)
			$select_tag->onchange = $this->onchange;
			
		$label_tag = new SwatHtmlTag('label');
		$label_tag->class = 'swat-radio-control';
		
		if ($this->options != null) {
			foreach ($this->options as $value => $title) {
				
				$input_tag->value = (string)$value;
				$input_tag->removeAttr('checked');
				
				if ((string)$this->value === (string)$value)
					$input_tag->checked = "checked";
				
				$input_tag->id = $this->name.'_'.$input_tag->value;
				$input_tag->display();
			
				$label_tag->for = $this->name.'_'.$input_tag->value;
				$label_tag->open();
				echo $title;
				$label_tag->close();
				
				echo '<br />';
			}
		}
	}	

	public function process() {
		if (isset($_POST[$this->name]))
			$this->value = $_POST[$this->name];
		else
			return $this->value = null;
	}

	/**
	 * Reset the radio list.
	 *
	 * Reset the list to its default state.  This is useful to call from a 
	 * display() method when persistence is not desired.
	 */
	public function reset() {
		reset($this->options);
		$this->value = key($this->options);
	}
}

?>
