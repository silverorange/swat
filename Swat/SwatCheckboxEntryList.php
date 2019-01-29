<?php

/**
 * A checkbox list widget with entries per option
 *
 * This widget can be used both to select multiple items from a list of options
 * and to submit an associated text field with each selected option.
 *
 * Selecting options works just like a normal checkbox list. Accessing the
 * associated text field is done using the
 * {@link SwatCheckboxEntryList::getEntryValue()} and
 * {@link SwatCheckboxEntryList::setEntryValue()} methods. For setting large
 * numbers of text fields (during initialization, for example) use the
 * {@link SwatCheckboxEntryList::setEntryValuesByArray()} method.
 *
 * @package   Swat
 * @copyright 2007-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatCheckboxEntryList extends SwatCheckboxList
{
	// {{{ public properties

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
	// {{{ protected properties

	/**
	 * The entry widgets used by this checkbox entry list
	 *
	 * This array is indexed by option values of this checkbox entry list.
	 *
	 * @var array
	 */
	protected $entry_widgets = array();

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
			'packages/swat/javascript/swat-checkbox-entry-list.js'
		);

		$this->addStyleSheet(
			'packages/swat/styles/swat-checkbox-entry-list.css'
		);
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

		if (!$this->visible || count($options) == 0) {
			return;
		}

		SwatWidget::display();

		$this->getForm()->addHiddenField($this->id . '_submitted', 1);

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
		if ($this->show_check_all && count($options) > 1) {
			echo '<tfoot><tr><td colspan="2">';
			$this->getCompositeWidget('check_all')->display();
			echo '</td></tr></tfoot>';
		}

		echo '<tbody>';
		foreach ($options as $key => $option) {
			echo '<tr><td>';

			$checkbox_id = $key . '_' . $option->value;

			$input_tag->value = (string) $option->value;
			$input_tag->removeAttribute('checked');
			$input_tag->name = $this->id . '[' . $key . ']';

			if (in_array($option->value, $this->values)) {
				$input_tag->checked = 'checked';
			}

			$input_tag->id = $this->id . '_' . $checkbox_id;
			echo '<span class="swat-checkbox-wrapper">';
			$input_tag->display();
			echo '<span class="swat-checkbox-shim"></span>';
			echo '</span>';

			$label_tag->for = $this->id . '_' . $checkbox_id;
			$label_tag->setContent($option->title, $option->content_type);
			$label_tag->display();

			echo '</td><td>';

			$this->getEntryWidget($option->value)->display();

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
		if (
			$this->getForm()->getHiddenField($this->id . '_submitted') === null
		) {
			return;
		}

		parent::process();

		foreach ($this->values as $option_value) {
			$this->getEntryWidget($option_value)->process();
		}
	}

	// }}}
	// {{{ public function getMessages()

	/**
	 * Gets all messages
	 *
	 * @return array an array of gathered {@link SwatMessage} objects.
	 *
	 * @see SwatWidget::getMessages()
	 */
	public function getMessages()
	{
		$messages = parent::getMessages();

		$options = $this->getOptions();
		foreach ($options as $option) {
			$widget = $this->getEntryWidget($option->value);
			$messages = array_merge($messages, $widget->getMessages());
		}

		return $messages;
	}

	// }}}
	// {{{ public function hasMessage()

	/**
	 * Checks for the presence of messages
	 *
	 * @return boolean true if this checkbox list or any of the entry widgets
	 *                  in this checkbox list have one or more messages.
	 *
	 * @see SwatWidget::hasMessages()
	 */
	public function hasMessage()
	{
		$has_message = parent::hasMessage();

		if (!$has_message) {
			$options = $this->getOptions();
			foreach ($options as $option) {
				if ($this->getEntryWidget($option->value)->hasMessage()) {
					$has_message = true;
					break;
				}
			}
		}

		return $has_message;
	}

	// }}}
	// {{{ public function getEntryValue()

	/**
	 * Gets the value of an entry widget in this checkbox entry list
	 *
	 * @param string $option_value used to indentify the entry widget
	 *
	 * @return string the value of the specified entry widget or null if no
	 *                 such widget exists.
	 */
	public function getEntryValue($option_value)
	{
		$entry_value = null;

		if ($this->hasEntryWidget($option_value)) {
			$entry = $this->getEntryWidget($option_value)->getFirst();
			$entry_value = $entry->value;
		}

		return $entry_value;
	}

	// }}}
	// {{{ public function setEntryValue()

	/**
	 * Sets the value of an entry widget in this checkbox entry list
	 *
	 * @param string $option_value the value of the option for which to set
	 *                              the entry widget value.
	 * @param string $entry_value the value to set on the entry widget.
	 *
	 * @throws SwatInvalidPropertyException if the option value does not match
	 *                                      an existing option value in this
	 *                                      checkbox entry list.
	 */
	public function setEntryValue($option_value, $entry_value)
	{
		$options = $this->getOptions();
		$option_values = array();
		foreach ($options as $option) {
			$option_values[] = $option->value;
		}

		if (!in_array($option_value, $option_values)) {
			throw new SwatInvalidPropertyException(
				sprintf(
					'No option with a value of "%s" exists in this checkbox ' .
						'entry list',
					$option_value
				)
			);
		}

		$this->getEntryWidget($option_value)->getFirst()->value = $entry_value;
	}

	// }}}
	// {{{ public function setEntryValuesByArray()

	/**
	 * Sets the values of multiple entry widgets
	 *
	 * This is a convenience method to quickly set the entry values for one
	 * or more options in this checkbox entry list. This calls
	 * {@link SwatCheckboxEntryList::setEntryValue()} internally for each
	 * entry in the <i>$entry_values</i> array.
	 *
	 * @param array $entry_values an array indexed by option values of this
	 *                             checkbox entry list with values of the entry
	 *                             widget values.
	 *
	 * @throws SwatInvalidPropertyException if any option value (array key)
	 *                                      does not match an existing option
	 *                                      value in this checkbox entry list.
	 */
	public function setEntryValuesByArray(array $entry_values)
	{
		foreach ($entry_values as $option_value => $entry_value) {
			$this->setEntryValue($option_value, $entry_value);
		}
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
			$this->id,
			$this->id
		);

		// set check-all controller if it is visible
		if ($this->show_check_all && count($this->getOptions()) > 1) {
			$javascript .= sprintf(
				"\n%s_obj.setController(%s_obj);",
				$this->getCompositeWidget('check_all')->id,
				$this->id
			);
		}

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
	// {{{ protected function hasEntryWidget()

	/**
	 * Checks if this checkbox entry list has an entry widget for a given
	 * option value
	 *
	 * @param string $option_value the value of the option to check.
	 *
	 * @return boolean true if this checkbox entry list has an entry widget for
	 *                  the given option value and false if it does not.
	 */
	protected function hasEntryWidget($option_value)
	{
		return isset($this->entry_widgets[$option_value]);
	}

	// }}}
	// {{{ protected function getEntryWidget()

	/**
	 * Gets a widget tree for the entry widget of this checkbox entry list
	 *
	 * This is used internally to create the widget tree containing a
	 * {@link SwatEntry} widget for display and processing.
	 *
	 * @param string $option_value the value of the option for which to get
	 *                              the entry widget. If no entry widget exists
	 *                              for the given option value, one is created.
	 *
	 * @return SwatContainer the widget tree containing the entry widget for
	 *                       the given option value.
	 */
	protected function getEntryWidget($option_value)
	{
		if (!$this->hasEntryWidget($option_value)) {
			$container = new SwatFormField(
				$this->id . '_field_' . $option_value
			);
			$container->add($this->createEntryWidget($option_value));
			$container->parent = $this;
			$container->init();
			$this->entry_widgets[$option_value] = $container;
		}

		return $this->entry_widgets[$option_value];
	}

	// }}}
	// {{{ protected function createEntryWidget()

	/**
	 * Creates an entry widget of this checkbox entry list
	 *
	 * Subclasses may override this method to create a different widget type.
	 *
	 * @param string $option_value the value of the option for which to get
	 *                              the entry widget.
	 *
	 * @return SwatEntry the new entry widget for the given option value.
	 */
	protected function createEntryWidget($option_value)
	{
		$widget = new SwatEntry($this->id . '_entry_' . $option_value);
		$widget->size = $this->entry_size;
		$widget->maxlength = $this->entry_maxlength;
		return $widget;
	}

	// }}}
}
