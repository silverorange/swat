<?php

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * A checkbox entry widget
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatCheckbox extends SwatControl implements SwatState
{
	/**
	 * Checkbox value
	 *
	 * The state of the widget.
	 *
	 * @var bool
	 */
	public $value = false;
	
	/**
	 * Displays this checkbox
	 *
	 * Outputs an appropriate XHTML tag.
	 */
	public function display()
	{
		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'checkbox';
		$input_tag->name = $this->id;
		$input_tag->id = $this->id;
		$input_tag->value = '1';

		if ($this->value)
			$input_tag->checked = 'checked';

		$input_tag->display();
	}	

	/**
	 * Processes this checkbox
	 *
	 * Sets the internal value of this checkbox based on submitted form data.
	 */
	public function process()
	{
		$this->value = array_key_exists($this->id, $_POST);
	}

	/**
	 * Gets the current state of this checkbox
	 *
	 * @return boolean the current state of this checkbox.
	 *
	 * @see SwatState::getState()
	 */
	public function getState()
	{
		return $this->value;
	}
	
	/**
	 * Sets the current state of this checkbox
	 *
	 * @param boolean $state the new state of this checkbox.
	 *
	 * @see SwatState::setState()
	 */
	public function setState($state)
	{
		$this->value = $state;
	}
}

?>
