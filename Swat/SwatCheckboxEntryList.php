<?php

require_once 'Swat/SwatCheckboxList.php';

/**
 * A checkbox list widget with entries per item.
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatCheckboxEntryList extends SwatCheckboxList
{
	/**
	 * Entry values 
	 *
	 * The values of entries accompanying each list option.
	 *
	 * @var array
	 */
	public $entry_values = array();

	/**
	 * Size of the entries
	 *
	 * The size of the embedded entry widgets.
	 *
	 * @var integer
	 */
	public $entry_size = 30;

	private $entry_widgets = array();

	/**
	 * Initializes this checkbox list
	 */
	public function init()
	{
		parent::init();
	}

	/**
	 * Displays this checkbox list
	 *
	 * The check-all widget is only displayed if more than one checkable item
	 * is displayed.
	 */
	public function display()
	{
		if (!$this->visible || $this->options === null)
			return;

		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id.'_div';
		$div_tag->class = 'swat-checkbox-entry-list';
		$div_tag->open();

		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'checkbox';
		$input_tag->name = $this->id.'[]';
		if ($this->onchange !== null)
			$input_tag->onchange = $this->onchange;

		$label_tag = new SwatHtmlTag('label');
		$label_tag->class = 'swat-control';

		echo '<table>';

		foreach ($this->options as $value => $title) {

			echo '<tr><td>';

			$input_tag->value = (string)$value;
			$input_tag->removeAttribute('checked');

			if (in_array($value, $this->values))
				$input_tag->checked = 'checked';

			$input_tag->id = $this->id.'_'.$input_tag->value;
			$input_tag->display();

			$label_tag->for = $this->id.'_'.$input_tag->value;
			$label_tag->setContent($title, $this->content_type);
			$label_tag->display();

			echo '</td><td>';

			$widget = $this->getEntryWidget($value);
			if (isset($this->entry_values[$value]))
				$widget->value = $this->entry_values[$value];

			$widget->display();

			echo '</td></tr>';
		}

		echo '</table>';

		// Only show the check all control if more than one checkable item is
		// displayed.
		$this->check_all->visible = (count($this->options) > 1);
		$this->check_all->display();

		$this->displayJavaScript();

		$div_tag->close();
	}

	/**
	 * Processes this checkbox list widget
	 *
	 * @return array Array of checked values
	 */
	public function process()
	{
		parent::process();
		$this->entry_values = array();

		foreach ($this->values as $value) {
			$widget = $this->getEntryWidget($value);
			$widget->process();
			$this->entry_values[$value] = $widget->value;
		}
	}

	/**
	 * Reset this checkbox list.
	 *
	 * Reset the list to its default state. This is useful to call from a 
	 * display() method when persistence is not desired.
	 */
	public function reset()
	{
		parent::reset();
		$this->entry_values = key($this->options);
	}

	private function getEntryWidget($id)
	{ 
		if (!isset($this->entry_widgets[$id])) {
			$widget = new SwatEntry($this->id.'_'.$id);
			$widget->size = $this->entry_size;
			$widget->parent = $this;
			$widget->init();
			$this->entry_widgets[$id] = $widget;
		}

		return $this->entry_widgets[$id];
	}
}

?>
