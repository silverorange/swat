<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

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
	 * Note: Be careful writing JavaScript when using this property as it
	 * changes the id of the XHTML element.
	 *
	 * @var boolean
	 */
	public $autocomplete = true;

	// }}}
	// {{{ protected properties

	/**
	 * If autocomplete is turned off, this nonce is used to obfuscate the
	 * name of the XHTML input tag.
	 *
	 * @var string
	 */
	protected $nonce = null;

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

		parent::display();

		$input_tag = $this->getInputTag();
		$input_tag->display();

		if (!$this->autocomplete) {
			$nonce_tag = new SwatHtmlTag('input');
			$nonce_tag->type = 'hidden';
			$nonce_tag->name = $this->id.'_nonce';
			$nonce_tag->value = $this->getNonce();
			$nonce_tag->display();
		}
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

		if ($this->autocomplete) {
			$id = $this->id;
		} else {
			if (isset($data[$this->id.'_nonce'])) {
				$id = $data[$this->id.'_nonce'];
			} else {
				$this->value = null;
				return;
			}
		}

		if (!isset($data[$id])) {
			$this->value = null;
			return;
		} elseif (strlen($data[$id]) == 0) {
			$this->value = null;
		} else {
			$this->value = $data[$id];
		}

		$len = ($this->value === null) ? 0 : strlen($this->value);

		if (!$this->required && $this->value === null) {
			return;

		} elseif ($this->value === null) {
			$this->addMessage($this->getValidationMessage('required'));

		} elseif ($this->maxlength !== null && $len > $this->maxlength) {
			$message = $this->getValidationMessage('too-long');
			$message->primary_content =
				sprintf($message->primary_content, $this->maxlength);

			$this->addMessage($message);

		} elseif ($this->minlength !== null && $len < $this->minlength) {
			$message = $this->getValidationMessage('too-short');
			$message->primary_content =
				sprintf($message->primary_content, $this->minlength);

			$this->addMessage($message);
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
		if ($this->visible)
			return ($this->autocomplete) ? $this->id : $this->getNonce();
		else
			return null;
	}

	// }}}
	// {{{ protected function getValidationMessage()

	/**
	 * Gets a validation message for this entry
	 *
	 * Can be used by sub-classes to change the validation messages.
	 *
	 * @param string $id the string identifier of the validation message.
	 *
	 * @return SwatMessage the validation message.
	 */
	protected function getValidationMessage($id)
	{
		switch ($id) {
		case 'required':
			$text = Swat::_('The %s field is required.');
			break;
		case 'too-long':
			$text = Swat::_('The %%s field can be at most %s characters long.');
			break;
		case 'too-short':
			$text = Swat::_('The %%s must be at least %s characters long.');
			break;
		default:
			$text = Swat::_('There is problem with the %s field.');
			break;
		}

		$message = new SwatMessage($text, SwatMessage::ERROR);
		return $message;
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
		$tag->name = ($this->autocomplete) ? $this->id : $this->getNonce();
		$tag->id = ($this->autocomplete) ? $this->id : $this->getNonce();
		$tag->class = $this->getCSSClassString();
		$tag->onfocus = 'this.select();';

		if (!$this->isSensitive())
			$tag->disabled = 'disabled';

		$value = $this->getDisplayValue($this->value);

		// escape value for display because we actually want to show entities
		// for editing
		$value = htmlspecialchars($value);
		$tag->value = $value;

		$tag->size = $this->size;
		$tag->maxlength = $this->maxlength;
		$tag->accesskey = $this->access_key;
		$tag->tabindex = $this->tab_index;

		return $tag;
	}

	// }}}
	// {{{ protected function getDisplayValue()

	/**
	 * Formats a value to display
	 *
	 * Can be used by subclasses to change what is displayed.
	 *
	 * @param string $value the value to format for display.
	 *
	 * @return string the formatted value.
	 */
	protected function getDisplayValue($value)
	{
		return $value;
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
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
	// {{{ protected function getNonce()

	protected function getNonce()
	{
		if ($this->nonce === null)
			$this->nonce = 'n'.md5(rand());

		return $this->nonce;
	}

	// }}}
}

?>
