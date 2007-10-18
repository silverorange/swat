<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatOptionControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatCheckAll.php';
require_once 'Swat/SwatState.php';
require_once 'Swat/SwatString.php';
require_once 'Swat/SwatYUI.php';
require_once 'Swat/exceptions/SwatException.php';

/**
 * A checkbox list widget
 *
 * @package   Swat
 * @copyright 2005-2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatCheckboxList extends SwatOptionControl implements SwatState
{
	// {{{ public properties

	/**
	 * List values
	 *
	 * The values of the selected items.
	 *
	 * @var array
	 */
	public $values = array();

	/**
	 * Whether to show the check all box
	 *
	 * @var boolean
	 */
	public $show_check_all = true;

	/**
	 * Number of columns in which this list is displayed
	 *
	 * If unspecified, the list will be displayed in one column.
	 *
	 * Each column is displayed using a separate XHTML unordered list
	 * (<code>&lt;ul&gt;</code>).
	 *
	 * @var integer
	 */
	public $columns = 1;

	// }}}
	// {{{ protected properties

	/**
	 * The check-all widget for this list
	 *
	 * The check-all is displayed if the list has more than one checkable
	 * item and is an easy way for users to check/uncheck the entire list.
	 *
	 * @var SwatCheckAll
	 */
	protected $check_all = null;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new checkbox list
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);
		$this->requires_id = true;
		$this->check_all = new SwatCheckAll();
		$this->check_all->parent = $this;
		$yui = new SwatYUI(array('event'));
		$this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());
		$this->addJavaScript('packages/swat/javascript/swat-checkbox-list.js',
			Swat::PACKAGE_ID);

		$this->addStyleSheet('packages/swat/styles/swat.css',
			Swat::PACKAGE_ID);
	}

	// }}}
	// {{{ public function init()

	/**
	 * Initializes this checkbox list
	 *
	 * @throws SwatException if there are duplicate values in the options array
	 */
	public function init()
	{
		parent::init();
		$this->check_all->init();

		// checks to see if there are duplicate values in the options array
		$options_count =  array();
		foreach ($this->getOptions() as $option)
			$options_count[] = $option->value;

		foreach ((array_count_values($options_count)) as $count) {
			if ($count > 1)
				throw new SwatException(sprintf('Duplicate option values '.
					'found in %s', $this->id));
		}
	}

	// }}}
	// {{{ public function getHtmlHeadEntrySet()

	/**
	 * Gets the SwatHtmlHeadEntry objects needed by this checkbox list
	 *
	 * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects needed by
	 *                               this checkbox list.
	 *
	 * @see SwatUIObject::getHtmlHeadEntrySet()
	 */
	public function getHtmlHeadEntrySet()
	{
		$set = parent::getHtmlHeadEntrySet();
		$set->addEntrySet($this->check_all->getHtmlHeadEntrySet());
		return $set;
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this checkbox list
	 *
	 * The check-all widget is only displayed if more than one checkable item
	 * is displayed.
	 */
	public function display()
	{
		$options = $this->getOptions();

		if (!$this->visible || count($options) == 0)
			return;

		parent::display();

		// prevent divide by zero and negative columns
		$columns = ($this->columns > 0) ? $this->columns : 1;

		$this->getForm()->addHiddenField($this->id.'_submitted', 1);

		// outer div is required because the check-all widget is outside the
		// unordered list
		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id;
		$div_tag->class = $this->getCSSClassString();
		$div_tag->open();

		// maximum number of options in each column
		$num_column_options = ceil(count($options) / $columns);
		$current_column = 1;
		$count = 0;

		$ul_tag = new SwatHtmlTag('ul');
		if ($columns > 1) {
			$ul_tag->id = sprintf('%_column_%s', $this->id, $current_column);
			$ul_tag->class = 'swat-checkbox-list-column';
		}
		$ul_tag->open();

		foreach ($options as $index => $option) {

			if ($count == $num_column_options) {
				$ul_tag->close();

				// start a new column
				$current_column++;
				$ul_tag->id =
					sprintf('%_column_%s', $this->id, $current_column);

				$ul_tag->open();
			}

			$count++;
			$this->displayOption($option, $index);
		}

		$ul_tag->close();

		// Only show the check all control if more than one checkable item is
		// displayed.
		$this->check_all->visible =
			$this->show_check_all && (count($options) > 1);

		// Show clear div if columns are used
		if ($columns > 1)
			echo '<div style="clear: left;"></div>';

		$this->check_all->display();

		$div_tag->close();

		Swat::displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ public function process()

	/**
	 * Processes this checkbox list widget
	 */
	public function process()
	{
		$form = $this->getForm();

		if ($form->getHiddenField($this->id.'_submitted') === null)
			return;

		parent::process();

		$this->check_all->process();

		$data = &$form->getFormData();

		if (isset($data[$this->id]))
			$this->values = $data[$this->id];
		else
			$this->values = array();
	}

	// }}}
	// {{{ public function reset()

	/**
	 * Reset this checkbox list.
	 *
	 * Reset the list to its default state. This is useful to call from a
	 * display() method when persistence is not desired.
	 */
	public function reset()
	{
		$this->values = array();
	}

	// }}}
	// {{{ public function setState()

	/**
	 * Sets the current state of this checkbox list
	 *
	 * @param array $state the new state of this checkbox list.
	 *
	 * @see SwatState::setState()
	 */
	public function setState($state)
	{
		$this->values = $state;
	}

	// }}}
	// {{{ public function getState()

	/**
	 * Gets the current state of this checkbox list
	 *
	 * @return array the current state of this checkbox list.
	 *
	 * @see SwatState::getState()
	 */
	public function getState()
	{
		return $this->values;
	}

	// }}}
	// {{{ protected function displayOption()

	/**
	 * Helper method to display a single option of this checkbox list
	 *
	 * @param SwatOption $option the option to display.
	 * @param integer $index a numeric index indicating which option is being
	 *                        displayed.
	 */
	protected function displayOption(SwatOption $option, $index)
	{
		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'checkbox';
		$input_tag->id = $this->id.'_'.$index.'_'.$input_tag->value;
		$input_tag->name = $this->id.'['.$index.']';
		$input_tag->value = (string)$option->value;
		$input_tag->removeAttribute('checked');

		if (in_array($option->value, $this->values))
			$input_tag->checked = 'checked';

		if (!$this->isSensitive())
			$this->input_tag->disabled = 'disabled';

		$label_tag = new SwatHtmlTag('label');
		$label_tag->class = 'swat-control';
		$label_tag->for = $this->id.'_'.$index.'_'.$input_tag->value;
		$label_tag->setContent($option->title, $option->content_type);

		$li_tag = $this->getLiTag($option);

		$li_tag->open();
		$input_tag->display();
		$label_tag->display();
		$li_tag->close();
	}

	// }}}
	// {{{ protected function getLiTag()

	protected function getLiTag($option)
	{
		$tag = new SwatHtmlTag('li');

		return $tag;
	}

	// }}}
	// {{{ protected function getInlineJavaScript()

	/**
	 * Gets the inline JavaScript for this checkbox list
	 *
	 * @return string the inline JavaScript for this checkbox list.
	 */
	protected function getInlineJavaScript()
	{
		$javascript = sprintf("var %s_obj = new SwatCheckboxList('%s');",
			$this->id, $this->id);

		// set check-all controller if it is visible
		if ($this->check_all->visible)
			$javascript.= sprintf("\n%s_obj.setController(%s_obj);",
				$this->check_all->id, $this->id);

		return $javascript;
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this checkbox list
	 *
	 * @return array the array of CSS classes that are applied to this checkbox
	 *                list.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-checkbox-list');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
}

?>
