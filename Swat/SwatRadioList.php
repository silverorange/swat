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
		parent::__construct();

		$this->show_blank  = false;
		$this->requires_id = true;
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this radio list
	 */
	public function display()
	{
		if (!$this->visible || $this->getOptions() === null)
			return;

		// add a hidden field so we can check if this list was submitted on
		// the process step
		$this->getForm()->addHiddenField($this->id.'_submitted', 1);

		$options = $this->getOptions();

		if ($this->show_blank)
			$options = array_merge(
				array(new SwatOption(null, $this->blank_title)),
				$options);

		$ul_tag = new SwatHtmlTag('ul');
		$ul_tag->id = $this->id;
		$ul_tag->class = $this->getCSSClassString();
		$ul_tag->open();

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

		$ul_tag->close();
	}

	// }}}
	// {{{ public function process()

	/**
	 * Figures out what option was selected
	 */
	public function process()
	{
		// do not process this radio list if it was not submitted
		if ($this->getForm()->getHiddenField($this->id.'_submitted') === null)
			return;

		parent::process();
	}

	// }}}
	// {{{ protected function processValue()

	/**
	 * Processes the value of this radio list from user-submitted form data
	 */
	protected function processValue()
	{
		$data = &$this->getForm()->getFormData();

		if (isset($data[$this->id]))
			$this->value = unserialize($data[$this->id]);
		else
			$this->value = null;
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

		$this->input_tag->value = serialize($option->value);
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
		$classes = array_merge($classes, $this->classes);
		return $classes;
	}

	// }}}
}

?>
