<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatFlydown.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatString.php';

/**
 * A radio list selection widget
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatRadioList extends SwatFlydown
{
	// {{{ private properties

	/**
	 * Used for displaying radio buttons
	 *
	 * @var SwatHtmlTag
	 */
	private $input_tag;

	/**
	 * Used for displaying radio button labels
	 *
	 * @var SwatHtmlTag
	 */
	private $label_tag;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new radiolist
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->show_blank  = false;
		$this->requires_id = true;

		$this->addStyleSheet('packages/swat/styles/swat-radio-list.css',
			Swat::PACKAGE_ID);
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this radio list
	 */
	public function display()
	{
		$options = $this->getOptions();

		if (!$this->visible || $options === null)
			return;

		SwatWidget::display();

		// add a hidden field so we can check if this list was submitted on
		// the process step
		$this->getForm()->addHiddenField($this->id.'_submitted', 1);

		if (count($options) == 1) {
			// get first and only element
			$this->displaySingle(current($options));
			return;
		}

		$ul_tag = new SwatHtmlTag('ul');
		$ul_tag->id = $this->id;
		$ul_tag->class = $this->getCSSClassString();
		$ul_tag->open();

		foreach ($options as $option) {
			echo '<li>';

			if ($option instanceof SwatFlydownDivider) {
				$this->displayDivider($option);
			} else {
				$this->displayOption($option);
				$this->displayOptionLabel($option);
			}

			echo '</li>';
		}

		$ul_tag->close();
	}

	// }}}
	// {{{ protected function processValue()

	/**
	 * Processes the value of this radio list from user-submitted form data
	 *
	 * @return boolean true if the value was processed from form data
	 */
	protected function processValue()
	{
		$form = $this->getForm();

		if ($form->getHiddenField($this->id.'_submitted') === null)
			return false;

		$data = &$form->getFormData();
		$salt = $form->getSalt();

		if (isset($data[$this->id]))
			if ($this->serialize_values) {
				$this->value =
					SwatString::signedUnserialize($data[$this->id], $salt);
			} else {
				$this->value = $data[$this->id];
			}
		else
			$this->value = null;

		return true;
	}

	// }}}
	// {{{ protected function displayDivider()

	/**
	 * Displays a divider option in this radio list
	 *
	 * @param SwatOption $option
	 */
	protected function displayDivider(SwatOption $option)
	{
		$span_tag = new SwatHtmlTag('span');
		$span_tag->class = 'swat-radio-list-divider';
		if ($option->value !== null)
			$span_tag->id = $this->id.'_'.(string)$option->value;

		$span_tag->setContent($option->title);
		$span_tag->display();
	}

	// }}}
	// {{{ protected function displayOption()

	/**
	 * Displays an option in the radio list
	 *
	 * @param SwatOption $option
	 */
	protected function displayOption(SwatOption $option)
	{
		if ($this->input_tag === null) {
			$this->input_tag = new SwatHtmlTag('input');
			$this->input_tag->type = 'radio';
			$this->input_tag->name = $this->id;
		}

		if (!$this->isSensitive())
			$this->input_tag->disabled = 'disabled';

		if ($this->serialize_values) {
			$salt = $this->getForm()->getSalt();
			$this->input_tag->value =
				SwatString::signedSerialize($option->value, $salt);
		} else {
			$this->input_tag->value = (string)$option->value;
		}

		$this->input_tag->removeAttribute('checked');
		$this->input_tag->id = $this->id.'_'.(string)$option->value;

		if ($option->value === $this->value)
			$this->input_tag->checked = 'checked';

		$this->input_tag->display();
	}

	// }}}
	// {{{ protected function displayOptionLabel()

	/**
	 * Displays an option in the radio list
	 *
	 * @param SwatOption $option
	 */
	protected function displayOptionLabel(SwatOption $option)
	{
		if ($this->label_tag === null) {
			$this->label_tag = new SwatHtmlTag('label');
			$this->label_tag->class = 'swat-control';
		}

		$this->label_tag->for = $this->id.'_'.(string)$option->value;
		$this->label_tag->setContent($option->title, $option->content_type);
		$this->label_tag->display();
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this radio list
	 *
	 * @return array the array of CSS classes that are applied to this radio
	 *                list.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-radio-list');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
}

?>
