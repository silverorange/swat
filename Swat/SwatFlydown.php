<?php
require_once('Swat/SwatControl.php');
require_once('Swat/SwatHtmlTag.php');

/**
 * A flydown (aka combo-box) selection widget
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatFlydown extends SwatControl {
	
	/**
	 * Flydown options
	 *
	 * An array of options for the flydown in the form value => title.
	 * @var array
	 */
	public $options = null;

	/**
	 * Flydown value 
	 *
	 * The value of the selected option, or null.
	 * @var string
	 */
	public $value = null;

	/**
	 * On change
	 *
	 * The onchange attribute of the HTML select tag, or null.
	 * @var string
	 */
	public $onchange = null;

	public function display() {
		$options = $this->getOptions();

		$select_tag = new SwatHtmlTag('select');
		$select_tag->name = $this->name;
		$select_tag->id = $this->name;

		if ($this->onchange !== null)
			$select_tag->onchange = $this->onchange;

		$option_tag = new SwatHtmlTag('option');

		$select_tag->open();

		if ($options !== null) {
			foreach ($options as $value => $title) {
				$option_tag->value = (string)$value;
				$option_tag->removeAttr('selected');
				
				if ((string)$this->value === (string)$value)
					$option_tag->selected = "selected";

				$option_tag->open();
				echo $title;
				$option_tag->close();
			}
		}

		$select_tag->close();
	}	

	public function process() {
		$this->value = $_POST[$this->name];
	}

	protected function &getOptions() {
		return $this->options;
	}

	/**
	 * Reset the flydown.
	 *
	 * Reset the flydown to its default state.  This is useful to call from a 
	 * display() method when persistence is not desired.
	 */
	public function reset() {
		reset($this->options);
		$this->value = key($this->options);
	}
	
	public function getState() {
		return $this->value;
	}

	public function setState($state) {
		$this->value = $state;
	}
}

?>
