<?php
require_once('Swat/SwatControl.php');
require_once('Swat/SwatHtmlTag.php');
require_once('Swat/SwatCheckAll.php');

/**
 * A checkbox list widget
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatCheckboxList extends SwatControl {
	
	/**
	 * Checkbox list options
	 *
	 * An array of options for the radio list in the form value => title.
	 * @var array
	 */
	public $options = null;

	/**
	 * List values 
	 *
	 * The values of the selected items.
	 * @var array
	 */
	public $values = array();

	/**
	 * On change
	 *
	 * The onchange attribute of the HTML input type=checkbox tags, or null.
	 * @var string
	 */
	public $onchange = null;

	public function display() {
		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'checkbox';
		$input_tag->name = $this->name.'[]';
		if ($this->onchange !== null)
			$input_tag->onchange = $this->onchange;
			
		$label_tag = new SwatHtmlTag('label');
		$label_tag->class = 'swat-control';
		
		if ($this->options !== null) {
			foreach ($this->options as $value => $title) {
				
				$input_tag->value = (string)$value;
				$input_tag->removeAttr('checked');
				
				if (in_array($value, $this->values))
					$input_tag->checked = "checked";
				
				$input_tag->id = $this->name.'_'.$input_tag->value;
				$input_tag->display();
			
				$label_tag->for = $this->name.'_'.$input_tag->value;
				$label_tag->open();
				echo $title;
				$label_tag->close();
				
				echo '<br />';
			}

			if (count($this->options) > 1) {
				$chk_all = new SwatCheckAll();
				$chk_all->series_name = $this->name;
				$chk_all->display();
			}
		}
	}	

	public function process() {
		if (isset($_POST[$this->name]))
			$this->values = $_POST[$this->name];
		else
			$this->values = array();
	}

	/**
	 * Reset the checkbox list.
	 *
	 * Reset the list to its default state.  This is useful to call from a 
	 * display() method when persistence is not desired.
	 */
	public function reset() {
		reset($this->options);
		$this->values = key($this->options);
	}
}

?>
