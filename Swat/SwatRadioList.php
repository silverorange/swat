<?php

require_once 'Swat/SwatFlydown.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatState.php';

/**
 * A radio list selection widget
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatRadioList extends SwatFlydown implements SwatState
{
	/**
	 * Creates a new radiolist
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct();

		$this->show_blank  = false;
		$this->requires_id = true;
	}

	/**
	 * Displays this radio list
	 */
	public function display()
	{
		if (!$this->visible || $this->getOptions() === null)
			return;

		$options = $this->getOptions();

		// Empty string XHTML option value is assumed to be null
		// when processing.
		if ($this->show_blank)
			$options = array_merge(
				array(new SwatOption('', $this->blank_title)),
				$options);

		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id.'_div';
		$div_tag->class = 'swat-radio-list';
		$div_tag->open();

		echo '<ul>';

		foreach ($options as $option) {
			echo '<li>';

			if ($option instanceof SwatFlydownDivider) {
				//ignore these for now TODO: make dividers work with radiolists
			} else {					
				$this->displayOption($option);
				$this->displayOptionLabel($option);
			}

			echo '</li>';
		}
		
		echo '</ul>';
		$div_tag->close();
	}

	/**
	 * Displays an option in the radio list
	 */
	public function displayOption($option)
	{
		static $input_tag = null;

		if ($input_tag === null) {
			$input_tag = new SwatHtmlTag('input');
			$input_tag->type = 'radio';
			$input_tag->name = $this->id;
		
			if ($this->onchange !== null)
				$input_tag->onchange = $this->onchange;
		}

		$value = (string)$option->value;
		$input_tag->value = $value;
		$input_tag->removeAttribute('checked');
		$input_tag->id = $this->id.'_'.$value;

		if ($value === (string)$this->value)
			$input_tag->checked = "checked";

		$input_tag->display();
	}

	/**
	 * Displays an option in the radio list
	 */
	public function displayOptionLabel($option)
	{
		static $label_tag = null;

		if ($label_tag === null) {
			$label_tag = new SwatHtmlTag('label');
			$label_tag->class = 'swat-control';
		}

		$value = (string)$option->value;
		$label_tag->for = $this->id.'_'.$value;
		$label_tag->setContent($option->title, $option->content_type);

		$label_tag->display();
	}
}

?>
