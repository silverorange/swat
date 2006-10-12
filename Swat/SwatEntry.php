<?php

require_once 'Swat/SwatInputControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatState.php';

/**
 * A single line text entry widget
 *
 * @package   Swat
 * @copyright 2004-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatEntry extends SwatInputControl implements SwatState
{
	// {{{ public properties

	/**
	 * Entry value
	 *
	 * Text content of the widget, or null.
	 *
	 * @var string
	 */
	public $value = null;

	/**
	 * Input size
	 *
	 * Size in characters of the HTML text form input, or null.
	 *
	 * @var integer
	 */
	public $size = 50;

	/**
	 * Maximum length
	 *
	 * Maximum number of allowable characters in HTML text form input, or null.
	 *
	 * @var integer
	 */
	public $maxlength = null;

	/**
	 * Access key
	 *
	 * Access key for this form input, for keyboard nagivation.
	 *
	 * @var string
	 */
	public $access_key = null;

	/**
	 * Minimum length
	 *
	 * Minimum number of allowable characters in HTML text form input, or null.
	 *
	 * @var integer
	 */
	public $minlength = null;

	/**
	 * Tab index
	 *
	 * The ordinal tab index position of the XHTML input tag, or null.
	 *
	 * @var integer
	 */
	public $tab_index = null;

	/**
	 * Whether or not to use browser-based autocompletion on this entry
	 *
	 * Note: Setting this property to true results in non-standard XHTML
	 * output.
	 *
	 * @var boolean
	 */
	public $autocomplete = true;

	// }}}
	// {{{ public function display()

	/**
	 * Displays this entry widget
	 *
	 * Outputs an appropriate XHTML tag.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$input_tag = $this->getInputTag();
		$input_tag->display();
	}

	// }}}
	// {{{ public function process()

	/**
	 * Processes this entry widget
	 *
	 * If any validation type errors occur, an error message is attached to
	 * this entry widget.
	 */
	public function process()
	{
		parent::process();

		$data = &$this->getForm()->getFormData();

		if (!isset($data[$this->id])) {
			$this->value = null;
			return;
		} elseif (strlen($data[$this->id]) == 0) {
			$this->value = null;
		} else {
			$this->value = $data[$this->id];
		}

		$len = ($this->value === null) ? 0 : strlen($this->value);

		if (!$this->required && $this->value === null) {
			return;

		} elseif ($this->value === null) {
			$message = $this->getValidationMessage('required');
			$this->addMessage(new SwatMessage($message, SwatMessage::ERROR));

		} elseif ($this->maxlength !== null && $len > $this->maxlength) {
			$message = sprintf(
				$this->getValidationMessage('too-long'),
				$this->maxlength);

			$this->addMessage(new SwatMessage($message, SwatMessage::ERROR));

		} elseif ($this->minlength !== null && $len < $this->minlength) {
			$message = sprintf(
				$this->getValidationMessage('too-short'),
				$this->minlength);

			$this->addMessage(new SwatMessage($message, SwatMessage::ERROR));

		}
	}

	// }}}
	// {{{ public function getState()

	/**
	 * Gets the current state of this entry widget
	 *
	 * @return string the current state of this entry widget.
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
	 * Sets the current state of this entry widget
	 *
	 * @param string $state the new state of this entry widget.
	 *
	 * @see SwatState::setState()
	 */
	public function setState($state)
	{
		$this->value = $state;
	}

	// }}}
	// {{{ protected function getValidationMessage()

	/**
	 * Get validation message
	 *
	 * Can be used by sub-classes to change the validation messages.
	 *
	 * @param string $id a string identifier for the message.
	 * @return string The validation message.
	 */
	protected function getValidationMessage($id)
	{
		switch ($id) {
		case 'required':
			return Swat::_('The %s field is required.');
		case 'too-long':
			return Swat::_('The %s field is required.');
		case 'too-short':
			return Swat::_('The %%s field can be at most %s characters long.');
		default:
			return null;
		}
	}

	// }}}
	// {{{ protected function getInputTag()

	/**
	 * Get the input tag to display
	 *
	 * Can be used by sub-classes to change the setup of the input tag.
	 *
	 * @return SwatHtmlTag Input tag to display.
	 */
	protected function getInputTag()
	{
		$tag = new SwatHtmlTag('input');
		$tag->type = 'text';
		$tag->name = $this->id;
		$tag->id = $this->id;
		$tag->class = $this->getCSSClassString();
		$tag->onfocus = 'this.select();';

		if (!$this->autocomplete)
			$tag->autocomplete = 'off';

		if (!$this->isSensitive())
			$tag->disabled = 'disabled';

		$tag->value = $this->getDisplayValue();

		$tag->size = $this->size;
		$tag->maxlength = $this->maxlength;
		$tag->accesskey = $this->access_key;
		$tag->tabindex = $this->tab_index;

		return $tag;
	}

	// }}}
	// {{{ protected function getDisplayValue()

	/**
	 * Get value to display
	 *
	 * Can be used by sub-classes to change what is displayed
	 *
	 * @return string Value to display
	 */
	protected function getDisplayValue()
	{
		return $this->value;
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
		return $this->id;
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this entry widget
	 *
	 * @return array the array of CSS classes that are applied to this entry
	 *                widget.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-entry');
		$classes = array_merge($classes, $this->classes);
		return $classes;
	}

	// }}}
}

?>
