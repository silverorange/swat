<?php
//test
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
	 * Minimum length
	 *
	 * Minimum number of allowable characters in HTML text form input, or null.
	 *
	 * @var integer
	 */
	public $minlength = null;

	/**
	 * The type of input tag
	 *
	 * @var string
	 */
	protected $html_input_type = 'text';

	/**
	 * Displays this entry widget
	 *
	 * Outputs an appropriate XHTML tag.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = $this->html_input_type;
		$input_tag->name = $this->id;
		$input_tag->class = 'swat-entry';
		$input_tag->id = $this->id;
		$input_tag->onfocus = 'this.select();';
		if (!$this->isSensitive())
			$input_tag->disabled = 'disabled';

		$value = $this->getDisplayValue();
		if ($value !== null)
			$input_tag->value = $value;

		if ($this->size !== null)
			$input_tag->size = $this->size;

		if ($this->maxlength !== null)
			$input_tag->maxlength = $this->maxlength;

		$input_tag->display();
	}

	/**
	 * Processes this entry widget
	 *
	 * If any validation type errors occur, an error message is attached to
	 * this entry widget.
	 */
	public function process()
	{
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
			$msg = Swat::_('The %s field is required.');
			$this->addMessage(new SwatMessage($msg, SwatMessage::ERROR));

		} elseif ($this->maxlength !== null && $len > $this->maxlength) {
			$msg = sprintf(
				Swat::_('The %%s field can be at most %s characters long.'),
				$this->maxlength);

			$this->addMessage(new SwatMessage($msg, SwatMessage::ERROR));

		} elseif ($this->minlength !== null && $len < $this->minlength) {
			$msg = sprintf(
				Swat::_('The %%s field must be more than %s characters long.'),
				$this->minlength);

			$this->addMessage(new SwatMessage($msg, SwatMessage::ERROR));

		}
	}

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
}

?>
