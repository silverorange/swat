<?php

/**
 * A multi-line text entry widget
 *
 * @package   Swat
 * @copyright 2004-2022 silverorange
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
     * Tab index
     *
     * The ordinal tab index position of the XHTML textarea tag, or null.
     * Values 1 or greater will affect the tab index of this widget. A value
     * of 0 or null will use the position of this textarea in the XHTML
     * character stream to determine tab order.
     *
     * @var integer
     */
    public $tab_index = null;

    /**
     * Maximum number of allowable characters or null if any number of
     * characters may be entered
     *
     * @var integer
     */
    public $maxlength = null;

    /**
     * A string containing characters that are ignored when calculating the
     * length this textarea's value
     *
     * By default, no characters are ignored when calculating the length of
     * this textarea's value.
     *
     * @var string
     */
    public $maxlength_ignored_characters = '';

    /**
     * Whether or not this textarea is dynamically resizeable
     *
     * @var boolean
     */
    public $resizeable = true;

    /**
     * Read only?
     *
     * If read-only, the textarea will not allow editing its contents
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
        $this->addStyleSheet('packages/swat/styles/swat-textarea.css');
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
        if (!$this->visible) {
            return;
        }

        parent::display();

        $div_tag = new SwatHtmlTag('div');
        $div_tag->class = 'swat-textarea-container';
        $div_tag->open();

        $textarea_tag = $this->getTextareaTag();
        $textarea_tag->display();

        $div_tag->close();
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

        if (!isset($data[$this->id])) {
            return;
        }

        $this->value = $data[$this->id];
        $length = $this->getValueLength();

        if ($length === 0) {
            $this->value = null;
        }

        if ($this->required && $length === 0) {
            $message = $this->getValidationMessage('required');
            $this->addMessage($message);
        } elseif ($this->maxlength !== null && $length > $this->maxlength) {
            $message = $this->getValidationMessage('too-long');
            $message->primary_content = sprintf(
                $message->primary_content,
                $this->maxlength,
            );

            $this->addMessage($message);
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
        return $this->visible ? $this->id : null;
    }

    // }}}
    // {{{ protected function getTextareaTag()

    /**
     * Gets the textarea tag used to display this textarea control
     *
     * @return SwatHtmlTag the textarea tag used to display this textarea
     *                     control.
     */
    protected function getTextareaTag()
    {
        // textarea tags cannot be self-closing when using HTML parser on XHTML
        $value = $this->value === null ? '' : $this->value;

        // escape value for display because we actually want to show entities
        // for editing
        $value = htmlspecialchars($value);

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
        $textarea_tag->tabindex = $this->tab_index;

        if ($this->read_only) {
            $textarea_tag->readonly = 'readonly';
        }

        if (!$this->isSensitive()) {
            $textarea_tag->disabled = 'disabled';
        }

        if ($this->placeholder != '') {
            $textarea_tag->placeholder = $this->placeholder;
        }

        return $textarea_tag;
    }

    // }}}
    // {{{ protected function getValueLength()

    /**
     * Gets the computed length of the value of this textarea
     *
     * @return integer the length of the value of this textarea
     */
    protected function getValueLength()
    {
        if ($this->maxlength_ignored_characters != '') {
            $chars = preg_quote($this->maxlength_ignored_characters, '/');
            $pattern = sprintf('/[%s]/u', $chars);
            $value = preg_replace($pattern, '', $this->value);
            $length = mb_strlen($value);
        } else {
            $length = mb_strlen($this->value);
        }
        return $length;
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
        $classes = ['swat-textarea'];

        if ($this->resizeable) {
            $classes[] = 'swat-textarea-resizeable';
        }

        return array_merge($classes, parent::getCSSClassNames());
    }

    // }}}
}
