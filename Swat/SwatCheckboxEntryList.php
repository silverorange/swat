<?php

require_once 'Swat/SwatCheckboxList.php';

/**
 * A checkbox list widget with entries per item
 *
 * @package   Swat
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatCheckboxEntryList extends SwatCheckboxList
{
	/**
	 * The values of the entry widgets accompanying each list option
	 *
	 * @var array
	 */
	public $entry_values = array();

	/**
	 * The size of all the embedded entry widgets
	 *
	 * @var integer
	 */
	public $entry_size = 30;

	/**
	 * An optional title to display above the column of entry widgets
	 *
	 * @var string
	 */
	public $entry_column_title = null;

	/**
	 * A list of entry widgets used by this checkbox entry list
	 *
	 * @var array
	 */
	private $entry_widgets = array();

	/**
	 * Creates a new checkbox entry list
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatCheckboxList::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);
		$this->addJavaScript('swat/javascript/swat-checkbox-entry-list.js');
	}

	/**
	 * Displays this checkbox list
	 *
	 * @see SwatCheckboxList::display()
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

		echo '<table><tbody>';
		if ($this->entry_column_title !== null) {
			echo '<thead><tr><th></th><th>';
			echo $this->entry_column_title;
			echo '</th></tr></thead>';
		}

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
		echo '</tbody>';

		// Only show the check all control if more than one checkable item is
		// displayed.
		if (count($this->options) > 1) {
			echo '<tfoot><tr><td colspan="2">';
			$this->check_all->display();
			echo '</td></tr></tfoot>';
		}

		echo '</table>';

		$this->displayJavaScript();

		$div_tag->close();
	}

	/**
	 * Processes this checkbox entry list
	 *
	 * Processes the checkboxes as well as each entry widget for each checked
	 * checkbox. The entry widgets for unchecked checkboxes are not processed.
	 *
	 * @see SwatCheckboxList::process()
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
	 * Resets this checkbox entry list
	 *
	 * Resets the list to its default state. This is useful to call from a
	 * display() method when persistence is not desired.
	 */
	public function reset()
	{
		parent::reset();
		$this->entry_values = key($this->options);
	}

	/**
	 * Displays the JavaScript for this checkbox entry list
	 */
	protected function displayJavaScript()
	{
		echo '<script type="text/javascript">';
		echo "//<![CDATA[\n";

		printf("var %s_obj = new SwatCheckboxEntryList('%s');\n",
			$this->id, $this->id);

		// set check-all controller if it is visible
		if (count($this->options) > 1)
			printf("%s_obj.setController(%s_obj);\n",
				$this->check_all->id, $this->id);

		echo "\n//]]>";
		echo '</script>';
	}

	/**
	 * Gets an entry widget of this checkbox entry list
	 *
	 * This is used internally to create {@link SwatEntry} widgets for display
	 * and processing.
	 *
	 * @param string $id the id of the entry widget to get. If no entry widget
	 *                    exists for the given id, one is created.
	 *
	 * @return SwatEntry the entry widget with the given id.
	 */
	private function getEntryWidget($id)
	{ 
		if (!isset($this->entry_widgets[$id])) {
			$widget = new SwatEntry($this->id.'_entry_'.$id);
			$widget->size = $this->entry_size;
			$widget->parent = $this;
			$widget->init();
			$this->entry_widgets[$id] = $widget;
		}

		return $this->entry_widgets[$id];
	}
}

?>
