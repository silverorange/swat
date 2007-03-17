<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatInputControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatState.php';
require_once 'Swat/SwatString.php';

/**
 * A multi-line text entry widget
 *
 * @package   Swat
 * @copyright 2004-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTextarea extends SwatInputControl implements SwatState
{
	// {{{ public properties

	/**
	 * Text content of the widget
	 *
	 * @var string
	 */
	public $value = null;

	/**
	 * Rows
	 *
	 * The number of rows for the XHTML textarea tag.
	 *
	 * @var integer
	 */
	public $rows = 10;

	/**
	 * Columns
	 *
	 * The number of columns for the XHTML textarea tag.
	 *
	 * @var integer
	 */
	public $cols = 50;

	/**
	 * Access key
	 *
	 * Access key for this textarea, for keyboard nagivation.
	 *
	 * @var string
	 */
	public $access_key = null;

	/**
	 * Maximum number of allowable characters or null if any number of
	 * characters may be entered
	 *
	 * @var integer
	 */
	public $maxlength = null;

	/**
	 * Whether or not this textarea is dynamically resizeable with JavaScript
	 *
	 * @var boolean
	 */
	public $resizeable = true;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new textarea widget
	 *
	 * Sets the widget title to a default value.
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);
		$yui = new SwatYUI(array('dom', 'event'));
		$this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());
		$this->addJavaScript('packages/swat/javascript/swat-textarea.js',
			Swat::PACKAGE_ID);

		$this->addStyleSheet('packages/swat/styles/swat-textarea.css',
			Swat::PACKAGE_ID);
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this textarea
	 *
	 * Outputs an appropriate XHTML tag.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		// textarea tags cannot be self-closing
		$value = ($this->value === null) ? '' : $this->value;

		// escape value for display because we actually want to show entities
		// for editing
		$value = htmlspecialchars($value);

		$div_tag = new SwatHtmlTag('div');
		$div_tag->class = 'swat-textarea-container';
		$div_tag->open();

		$textarea_tag = new SwatHtmlTag('textarea');
		$textarea_tag->name = $this->id;
		$textarea_tag->id = $this->id;
		$textarea_tag->class = $this->getCSSClassString();
		// NOTE: The attributes rows and cols are required in
		//       a textarea for XHTML strict.
		$textarea_tag->rows = $this->rows;
		$textarea_tag->cols = $this->cols;
		$textarea_tag->setContent($value, 'text/xml');
		$textarea_tag->accesskey = $this->access_key;

		if (!$this->isSensitive())
			$textarea_tag->disabled = 'disabled';

		$textarea_tag->display();

		$div_tag->close();

		Swat::displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ public function process()

	/**
	 * Processes this textarea
	 *
	 * If a validation error occurs, an error message is attached to this
	 * widget.
	 */
	public function process()
	{
		parent::process();

		$data = &$this->getForm()->getFormData();

		if (!isset($data[$this->id]))
			return;

		$this->value = $data[$this->id];
		$len = strlen($this->value);

		if ($len == 0)
			$this->value = null;

		if ($this->required && $len == 0) {
			$message = Swat::_('The %s field is required.');
			$this->addMessage(new SwatMessage($message, SwatMessage::ERROR));

		} elseif ($this->maxlength !== null && $len > $this->maxlength) {
			$message = sprintf(
				Swat::_('The %%s field can be at most %s characters long.'),
				$this->maxlength);

			$this->addMessage(new SwatMessage($message, SwatMessage::ERROR));
		}
	}

	// }}}
	// {{{ public function getState()

	/**
	 * Gets the current state of this textarea
	 *
	 * @return boolean the current state of this textarea.
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
	 * Sets the current state of this textarea
	 *
	 * @param boolean $state the new state of this textarea.
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
		return ($this->visible) ? $this->id : null;
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this textarea 
	 *
	 * @return array the array of CSS classes that are applied to this textarea.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-textarea');
		$classes = array_merge($classes, $this->classes);
		return $classes;
	}

	// }}}
	// {{{ protected function getInlineJavaScript()

	/**
	 * Gets the inline JavaScript for this textarea widget
	 *
	 * @return string the inline JavaScript for this textarea widget.
	 */
	protected function getInlineJavaScript()
	{
		$resizeable = ($this->resizeable) ? 'true' : 'false';
		return sprintf("var %s_obj = new SwatTextarea('%s', %s);",
			$this->id, $this->id, $resizeable);
	}

	// }}}
}

?>
