<?php

require_once('Swat/SwatControl.php');
require_once('Swat/SwatHtmlTag.php');
require_once('Swat/SwatState.php');

/**
 * A flydown (aka combo-box) selection widget
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatFlydown extends SwatControl implements SwatState {
	
	/**
	 * Flydown options
	 *
	 * An array of options for the flydown in the form value => title.
	 *
	 * @var array
	 */
	public $options = null;

	/**
	 * Flydown value 
	 *
	 * The value of the selected option, or null.
	 *
	 * @var string
	 */
	public $value = null;
	
	/**
	 * Required
	 *
	 * Must have a non-empty value when processed.
	 *
	 * @var bool
	 */
	public $required = false;

	/**
	 * Show a blank option
	 *
	 * @var boolean
	 */
	public $show_blank = true;

	/**
	 * Blank title
	 *
	 * @var string
	 */
	public $blank_title = '';

	/**
	 * On change
	 *
	 * The onchange attribute of the HTML select tag, or null.
	 *
	 * @var string
	 */
	public $onchange = null;

	public function display() {
		$options = $this->getOptions();

		$select_tag = new SwatHtmlTag('select');
		$select_tag->name = $this->id;
		$select_tag->id = $this->id;

		if ($this->onchange !== null)
			$select_tag->onchange = $this->onchange;

		$option_tag = new SwatHtmlTag('option');

		$select_tag->open();

		if ($options !== null) {
			if ($this->show_blank) {
				// Empty string HTML option value is considered to be null
				$option_tag->value = '';
				$option_tag->open();
				echo $this->blank_title;
				$option_tag->close();
			}
			
			foreach ($options as $value => $title) {
				$option_tag->value = (string)$value;
				$option_tag->removeAttr('selected');
				
				if ((string)$this->value === (string)$value)
					$option_tag->selected = 'selected';

				$option_tag->open();
				echo $title;
				$option_tag->close();
			}
		}

		$select_tag->close();
	}	

	public function process() {
		$value = $_POST[$this->id];

		// Empty string HTML option value is considered to be null
		if (strlen($value) == 0)
			$this->value = null;
		else
			$this->value = $value;
		
		if ($this->required && $this->value === null) {
			$msg = _S("The %s field is required.");
			$this->addMessage(new SwatMessage($msg, SwatMessage::USER_ERROR));
		}
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
		$this->value = null;	
	}
	
	public function getState() {
		return $this->value;
	}

	public function setState($state) {
		$this->value = $state;
	}
}

?>
