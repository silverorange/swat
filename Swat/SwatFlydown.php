<?php

/**
 * A flydown (aka combo-box) selection widget
 *
 * @package   Swat
 * @copyright 2004-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatFlydown extends SwatOptionControl implements SwatState
{
	// {{{ public properties

	/**
	 * Flydown value
	 *
	 * The index value of the selected option, or null if no option is
	 * selected.
	 *
	 * @var string
	 */
	public $value = null;

	/**
	 * Show a blank option
	 *
	 * Whether or not to show a blank value at the top of the flydown.
	 *
	 * @var boolean
	 */
	public $show_blank = true;

	/**
	 * Blank title
	 *
	 * The user visible title to display in the blank field.
	 *
	 * @var string
	 */
	public $blank_title = '';

	// }}}
	// {{{ public function display()

	/**
	 * Displays this flydown
	 *
	 * Displays this flydown as a XHTML select.
	 */
	public function display()
	{
		if (!$this->visible) {
			return;
		}

		parent::display();

		$wrapper_classes = array_merge(
			array_diff($this->getCSSClassNames(), array('swat-flydown')),
			array('swat-flydown-wrapper')
		);
		$wrapper = new SwatHtmlTag('span');
		$wrapper->class = implode(' ', $wrapper_classes);
		$wrapper->open();

		$options = $this->getOptions();
		$selected = false;

		if ($this->show_blank) {
			$options = array_merge(array($this->getBlankOption()), $options);
		}

		// only show a select if there is more than one option
		if (count($options) > 1) {
			$flydown_value = $this->serialize_values
				? $this->value
				: (string) $this->value;

			if ($this->serialize_values) {
				$salt = $this->getForm()->getSalt();
			}

			$select_tag = new SwatHtmlTag('select');
			$select_tag->name = $this->id;
			$select_tag->id = $this->id;
			$select_tag->class = $this->getCSSClassString();

			if (!$this->isSensitive()) {
				$select_tag->disabled = 'disabled';
			}

			$option_tag = new SwatHtmlTag('option');

			$select_tag->open();

			foreach ($options as $flydown_option) {
				if ($this->serialize_values) {
					$option_tag->value = SwatString::signedSerialize(
						$flydown_option->value,
						$salt
					);
				} else {
					$option_tag->value = (string) $flydown_option->value;
				}

				if ($flydown_option instanceof SwatFlydownDivider) {
					$option_tag->disabled = 'disabled';
					$option_tag->class = 'swat-flydown-option-divider';
				} elseif ($flydown_option instanceof SwatFlydownBlankOption) {
					$option_tag->removeAttribute('disabled');
					$option_tag->class = 'swat-blank-option';
				} else {
					$option_tag->removeAttribute('disabled');
					$option_tag->removeAttribute('class');

					// add option-specific CSS classes from option metadata
					$classes = $this->getOptionMetadata(
						$flydown_option,
						'classes'
					);

					if (is_array($classes)) {
						$option_tag->class = implode(' ', $classes);
					} elseif ($classes) {
						$option_tag->class = strval($classes);
					}
				}

				$value = $this->serialize_values
					? $flydown_option->value
					: (string) $flydown_option->value;

				if (
					$flydown_value === $value &&
					!$selected &&
					!($flydown_option instanceof SwatFlydownDivider)
				) {
					$option_tag->selected = 'selected';
					$selected = true;
				} else {
					$option_tag->removeAttribute('selected');
				}

				$option_tag->setContent(
					$flydown_option->title,
					$flydown_option->content_type
				);

				$option_tag->display();
			}

			$select_tag->close();
		} elseif (count($options) == 1) {
			// get first and only element
			$this->displaySingle(current($options));
		}

		$wrapper->close();
	}

	// }}}
	// {{{ public function process()

	/**
	 * Figures out what option was selected
	 *
	 * Processes this widget and figures out what select element from this
	 * flydown was selected. Any validation errors cause an error message to
	 * be attached to this widget in this method.
	 */
	public function process()
	{
		parent::process();

		if (!$this->processValue()) {
			return;
		}

		if ($this->required && $this->isSensitive()) {
			// When values are not serialized, an empty string is treated as
			// null. As a result, you should not use a null value and an empty
			// string value in the same flydown except when using serialized
			// values.
			if (
				($this->serialize_values && $this->value === null) ||
				(!$this->serialize_values && $this->value == '')
			) {
				$this->addMessage($this->getValidationMessage('required'));
			}
		}
	}

	// }}}
	// {{{ public function addDivider()

	/**
	 * Adds a divider to this flydown
	 *
	 * A divider is an unselectable flydown option.
	 *
	 * @param string $title the title of the divider. Defaults to two em
	 *                       dashes.
	 * @param string $content_type optional. The content type of the divider. If
	 *                              not specified, defaults to 'text/plain'.
	 */
	public function addDivider($title = '——', $content_type = 'text/plain')
	{
		$this->options[] = new SwatFlydownDivider(null, $title, $content_type);
	}

	// }}}
	// {{{ public function reset()

	/**
	 * Resets this flydown
	 *
	 * Resets this flydown to its default state. This method is useful to
	 * call from a display() method when form persistence is not desired.
	 */
	public function reset()
	{
		reset($this->options);
		$this->value = null;
	}

	// }}}
	// {{{ public function getState()

	/**
	 * Gets the current state of this flydown
	 *
	 * @return boolean the current state of this flydown.
	 *
	 * @see SwatState::getState()
	 */
	public function getState()
	{
		return $this->value;
	}

	// }}}
	// {{{ public function setState()

	/**
	 * Sets the current state of this flydown
	 *
	 * @param boolean $state the new state of this flydown.
	 *
	 * @see SwatState::setState()
	 */
	public function setState($state)
	{
		$this->value = $state;
	}

	// }}}
	// {{{ public function getFocusableHtmlId()

	/**
	 * Gets the id attribute of the XHTML element displayed by this widget
	 * that should receive focus
	 *
	 * @return string the id attribute of the XHTML element displayed by this
	 *                 widget that should receive focus or null if there is
	 *                 no such element.
	 *
	 * @see SwatWidget::getFocusableHtmlId()
	 */
	public function getFocusableHtmlId()
	{
		$focusable_id = null;

		if ($this->visible) {
			$count = count($this->getOptions());
			if ($this->show_blank) {
				$count++;
			}

			if ($count > 1) {
				$focusable_id = $this->id;
			}
		}

		return $focusable_id;
	}

	// }}}
	// {{{ protected function processValue()

	/**
	 * Processes the value of this flydown from user-submitted form data
	 *
	 * @return boolean true if the value was processed from form data
	 */
	protected function processValue()
	{
		$form = $this->getForm();

		$data = &$form->getFormData();
		if (!isset($data[$this->id])) {
			return false;
		}

		if ($this->serialize_values) {
			$salt = $form->getSalt();
			$this->value = SwatString::signedUnserialize(
				$data[$this->id],
				$salt
			);
		} else {
			$this->value = (string) $data[$this->id];
		}

		return true;
	}

	// }}}
	// {{{ protected function displaySingle()

	/**
	 * Displays this flydown if there is only a single option
	 */
	protected function displaySingle(SwatOption $flydown_option)
	{
		$title = $flydown_option->title;
		$value = $flydown_option->value;

		$hidden_tag = new SwatHtmlTag('input');
		$hidden_tag->type = 'hidden';
		$hidden_tag->name = $this->id;

		if ($this->serialize_values) {
			$salt = $this->getForm()->getSalt();
			$hidden_tag->value = SwatString::signedSerialize($value, $salt);
		} else {
			$hidden_tag->value = (string) $value;
		}

		$hidden_tag->display();

		$span_tag = new SwatHtmlTag('span');
		$span_tag->class = 'swat-flydown-single';
		$span_tag->setContent($title, $flydown_option->content_type);
		$span_tag->display();
	}

	// }}}
	// {{{ protected function getBlankOption()

	/**
	 * Gets the the blank option for this flydown.
	 *
	 * @return SwatFlydownBlankOption the blank value option.
	 */
	protected function getBlankOption()
	{
		return new SwatFlydownBlankOption(null, $this->blank_title);
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this flydown
	 *
	 * @return array the array of CSS classes that are applied to this flydown.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-flydown');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
}
