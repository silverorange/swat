<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatInputControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatState.php';

/**
 * A single line text entry widget
 *
 * @package   Swat
 * @copyright 2004-2016 silverorange
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
	 * Values 1 or greater will affect the tab index of this widget. A value
	 * of 0 or null will use the position of the input tag in the XHTML
	 * character stream to determine tab order.
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

	/**
	 * Read only?
	 *
	 * If read-only, the input will not allow editing its contents
	 *
	 * @var boolean
	 */
	public $read_only = false;

	/**
	 * Some text hinting to the user what should be entered in this entry
	 *
	 * @var string
	 */
	public $placeholder;

	/**
	 * Whether or not to select the content of this entry on receiving focus
	 *
	 * @var boolean
	 */
	public $select_on_focus = false;

	// }}}
	// {{{ protected properties

	/**
	 * If autocomplete is turned off, this nonce is used to obfuscate the
	 * name of the XHTML input tag.
	 *
	 * @var string
	 */
	protected $nonce = null;

	/**
	 * Whether or not to trim the value on process.
	 *
	 * If auto_trim is true, then we trim all values before doing other
	 * processing. When trimming, zero length strings are converted to null.
	 *
	 * @var boolean
	 */
	public $auto_trim = true;


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

		// if nothing was submitted by the user, shortcut return
		if (!$this->hasRawValue()) {
			$this->value = null;
			return;
		}

		$this->value = $this->getRawValue();

		if ($this->auto_trim) {
			$this->value = trim($this->value);
			if ($this->value === '')
				$this->value = null;
		}

		$len = ($this->value === null) ? 0 : mb_strlen($this->value);

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
		case 'too-short':
			$text = $this->show_field_title_in_messages ?
				Swat::_('The %%s must be at least %s characters long.') :
				Swat::_('This field must be at least %s characters long.');

			break;
		default:
			return parent::getValidationMessage($id);
		}

		$message = new SwatMessage($text, 'error');
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

		// event handlers to select on focus
		if ($this->select_on_focus) {
			$tag->onmousedown = 'if(!this._focused){this._focus_click=true;}';
			$tag->onmouseup = 'if(this._focus_click){'.
				'this.select();this._focus_click=false;}';

			$tag->onfocus = 'this._focused=true;'.
				'if(!this._focus_click){this.select();}';

			$tag->onblur = 'this._focused=false;this._focus_click=false;';
		}

		if ($this->read_only)
			$tag->readonly = 'readonly';

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

		if ($this->placeholder != '') {
			$tag->placeholder = $this->placeholder;
		}

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
	// {{{ protected function getRawValue()

	/**
	 * Gets the raw value entered by the user before processing
	 *
	 * @return string the raw value entred by the user before processing or
	 *                 null if no value was entered by the user.
	 *
	 * @see SwatEntry::hasRawValue()
	 */
	protected function getRawValue()
	{
		$value = null;

		$data = &$this->getForm()->getFormData();

		if ($this->autocomplete) {
			$id = $this->id;
			if (isset($data[$id]) && $data[$id] != '') {
				$value = $data[$id];
			}
		} else {
			if (isset($data[$this->id.'_nonce'])) {
				$id = $data[$this->id.'_nonce'];
				if (isset($data[$id]) && $data[$id] != '') {
					$value = $data[$id];
				}
			}
		}

		return $value;
	}

	// }}}
	// {{{ protected function hasRawValue()

	/**
	 * Gets whether or not a value was submitted by the user for this entry
	 *
	 * Note: Users can submit a value of nothing and this method will return
	 * true. This method only returns false if no data was submitted at all.
	 *
	 * @return boolean true if a value was submitted by the user for this entry
	 *                  and false if no value was submitted by the user.
	 *
	 * @see SwatEntry::getRawValue()
	 */
	protected function hasRawValue()
	{
		$has_value = false;

		$data = &$this->getForm()->getFormData();

		if ($this->autocomplete) {
			$id = $this->id;
			if (isset($data[$id])) {
				$has_value = true;
			}
		} else {
			if (isset($data[$this->id.'_nonce'])) {
				$id = $data[$this->id.'_nonce'];
				if (isset($data[$id])) {
					$has_value = true;
				}
			}
		}

		return $has_value;
	}

	// }}}
}

?>
