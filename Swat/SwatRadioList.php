<?php

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatState.php';

/**
 * A radio list selection widget
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatRadioList extends SwatControl implements SwatState
{
	/**
	 * Radio list options
	 *
	 * An array of options for the radio list in the form:
	 *    value => title.
	 *
	 * @var array
	 */
	public $options = null;

	/**
	 * List value 
	 *
	 * The value of the selected item, or null.
	 *
	 * @var string
	 */
	public $value = null;

	/**
	 * On change
	 *
	 * The onchange attribute of the XHTML input type=radio tags, or null.
	 *
	 * @var string
	 */
	public $onchange = null;

	/**
	 * Displays this radio list
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'radio';
		$input_tag->name = $this->id;
		if ($this->onchange !== null)
			$input_tag->onchange = $this->onchange;

		$label_tag = new SwatHtmlTag('label');
		$label_tag->class = 'swat-control';

		if ($this->options !== null) {
			foreach ($this->options as $value => $title) {

				$input_tag->value = (string)$value;
				$input_tag->removeAttribute('checked');

				if ((string)$this->value === (string)$value)
					$input_tag->checked = "checked";

				$input_tag->id = $this->id.'_'.$input_tag->value;
				$input_tag->display();

				$label_tag->for = $this->id.'_'.$input_tag->value;
				$label_tag->content = $title;

				$label_tag->display();

				echo '<br />';
			}
		}
	}

	/**
	 * Processes this radio list
	 */
	public function process()
	{
		if (isset($_POST[$this->id]))
			$this->value = $_POST[$this->id];
		else
			$this->value = null;
	}

	/**
	 * Resets this radio list
	 *
	 * Resets this list to its default state. This methods is useful to call
	 * from a display() method when form persistence is not desired.
	 */
	public function reset()
	{
		reset($this->options);
		$this->value = key($this->options);
	}

	/**
	 * Gets the current state of this radio list
	 *
	 * @return boolean the current state of this radio list.
	 *
	 * @see SwatState::getState()
	 */
	public function getState()
	{
		return $this->value;
	}

	/**
	 * Sets the current state of this radio list
	 *
	 * @param boolean $state the new state of this radio list.
	 *
	 * @see SwatState::setState()
	 */
	public function setState($state)
	{
		$this->value = $state;
	}
}

?>
