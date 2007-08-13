<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatCheckboxList.php';
require_once 'Swat/SwatYUI.php';

/**
 * A checkbox list widget with entries per item
 *
 * @package   Swat
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @todo      Write better API for getting and setting entry values for a
 *            checkbox option.
 */
class SwatCheckboxEntryList extends SwatCheckboxList
{
	// {{{ public properties

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
	 * An optional maximum length to apply to entry widgets
	 *
	 * @var integer
	 */
	public $entry_maxlength = null;

	// }}}
	// {{{ private properties

	/**
	 * A list of entry widgets used by this checkbox entry list
	 *
	 * @var array
	 */
	private $entry_widgets = array();

	// }}}
	// {{{ public function __construct()

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

		$yui = new SwatYUI(array('dom', 'event'));
		$this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());

		$this->addJavaScript(
			'packages/swat/javascript/swat-checkbox-entry-list.js',
			Swat::PACKAGE_ID);

		$this->addStyleSheet(
			'packages/swat/styles/swat-checkbox-entry-list.css',
			Swat::PACKAGE_ID);
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this checkbox list
	 *
	 * @see SwatCheckboxList::display()
	 */
	public function display()
	{
		$options = $this->getOptions();

		if (!$this->visible || count($options) == 0)
			return;

		$this->getForm()->addHiddenField($this->id.'_submitted', 1);

		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id;
		$div_tag->class = $this->getCSSClassString();
		$div_tag->open();

		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'checkbox';

		$label_tag = new SwatHtmlTag('label');
		$label_tag->class = 'swat-control';

		echo '<table>';

		if ($this->entry_column_title !== null) {
			echo '<thead><tr><th>&nbsp;</th><th>';
			echo $this->entry_column_title;
			echo '</th></tr></thead>';
		}

		// Only show the check all control if more than one checkable item is
		// displayed.
		if (count($options) > 1) {
			echo '<tfoot><tr><td colspan="2">';
			$this->check_all->display();
			echo '</td></tr></tfoot>';
		}

		echo '<tbody>';
		foreach ($options as $key => $option) {
			echo '<tr><td>';

			$checkbox_id = $key.'_'.$option->value;

			$input_tag->value = (string)$option->value;
			$input_tag->removeAttribute('checked');
			$input_tag->name = $this->id.'['.$key.']';

			if (in_array($option->value, $this->values))
				$input_tag->checked = 'checked';

			$input_tag->id = $this->id.'_'.$checkbox_id;
			$input_tag->display();

			$label_tag->for = $this->id.'_'.$checkbox_id;
			$label_tag->setContent($option->title, $option->content_type);
			$label_tag->display();

			echo '</td><td>';

			$widget = $this->getEntryWidget($checkbox_id);
			if (isset($this->entry_values[$option->value]))
				$widget->value = $this->entry_values[$option->value];

			$widget->display();

			echo '</td></tr>';
		}
		echo '</tbody>';

		echo '</table>';

		$div_tag->close();

		Swat::displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ public function process()

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
		if ($this->getForm()->getHiddenField($this->id.'_submitted') === null)
			return;

		parent::process();

		$this->entry_values = array();

		foreach ($this->values as $key => $value) {
			$checkbox_id = $key.'_'.$value;
			$widget = $this->getEntryWidget($checkbox_id);
			$widget->process();
			$this->entry_values[$value] = $widget->value;
		}
	}

	// }}}
	// {{{ public function reset()

	/**
	 * Resets this checkbox entry list
	 *
	 * Resets the list to its default state. This is useful to call from a
	 * display() method when persistence is not desired.
	 */
	public function reset()
	{
		parent::reset();
		$this->entry_values = array();
	}

	// }}}
	// {{{ protected function getInlineJavaScript()

	/**
	 * Gets the inline JavaScript for this checkbox entry list
	 *
	 * @return string the inline JavaScript for this checkbox entry list.
	 */
	protected function getInlineJavaScript()
	{
		$javascript = sprintf(
			"var %s_obj = new SwatCheckboxEntryList('%s');",
			$this->id, $this->id);

		// set check-all controller if it is visible
		if (count($this->getOptions()) > 1)
			$javascript.= sprintf("\n%s_obj.setController(%s_obj);",
				$this->check_all->id, $this->id);

		return $javascript;
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this checkbox entry
	 * list
	 *
	 * @return array the array of CSS classes that are applied to this checkbox
	 *                entry list.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-checkbox-entry-list');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
	// {{{ private function getEntryWidget()

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
			$widget->maxlength = $this->entry_maxlength;
			$widget->parent = $this;
			$widget->init();
			$this->entry_widgets[$id] = $widget;
		}

		return $this->entry_widgets[$id];
	}

	// }}}
}

?>
