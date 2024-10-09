<?php

/**
 * A checkbox entry widget.
 *
 * @copyright 2004-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatCheckbox extends SwatInputControl implements SwatState
{
    /**
     * Checkbox value.
     *
     * The state of the widget.
     *
     * @var bool
     */
    public $value = false;

    /**
     * Access key.
     *
     * Access key for this checkbox input, for keyboard nagivation.
     *
     * @var string
     */
    public $access_key;

    /**
     * The ordinal tab index position of the XHTML input tag.
     *
     * Values 1 or greater will affect the tab index of this widget. A value
     * of 0 or null will use the position of the input tag in the XHTML
     * character stream to determine tab order.
     *
     * @var int
     */
    public $tab_index;

    /**
     * Creates a new checkbox.
     *
     * @param string $id a non-visible unique id for this widget
     *
     * @see SwatWidget::__construct()
     */
    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->requires_id = true;
    }

    /**
     * Displays this checkbox.
     *
     * Outputs an appropriate XHTML tag.
     */
    public function display()
    {
        if (!$this->visible) {
            return;
        }

        parent::display();

        $this->getForm()->addHiddenField($this->id . '_submitted', 1);

        $input_tag = new SwatHtmlTag('input');
        $input_tag->type = 'checkbox';
        $input_tag->class = $this->getCSSClassString();
        $input_tag->name = $this->id;
        $input_tag->id = $this->id;
        $input_tag->value = '1';
        $input_tag->accesskey = $this->access_key;
        $input_tag->tabindex = $this->tab_index;

        if ($this->value) {
            $input_tag->checked = 'checked';
        }

        if (!$this->isSensitive()) {
            $input_tag->disabled = 'disabled';
        }

        echo '<span class="swat-checkbox-wrapper">';
        $input_tag->display();
        echo '<span class="swat-checkbox-shim"></span>';
        echo '</span>';
    }

    /**
     * Processes this checkbox.
     *
     * Sets the internal value of this checkbox based on submitted form data.
     */
    public function process()
    {
        parent::process();

        if (
            $this->getForm()->getHiddenField($this->id . '_submitted') === null
        ) {
            return;
        }

        $data = &$this->getForm()->getFormData();
        $this->value = array_key_exists($this->id, $data);
    }

    /**
     * Gets the current state of this checkbox.
     *
     * @return bool the current state of this checkbox
     *
     * @see SwatState::getState()
     */
    public function getState()
    {
        return $this->value;
    }

    /**
     * Sets the current state of this checkbox.
     *
     * @param bool $state the new state of this checkbox
     *
     * @see SwatState::setState()
     */
    public function setState($state)
    {
        $this->value = $state;
    }

    /**
     * Gets the id attribute of the XHTML element displayed by this widget
     * that should receive focus.
     *
     * @return string the id attribute of the XHTML element displayed by this
     *                widget that should receive focus or null if there is
     *                no such element
     *
     * @see SwatWidget::getFocusableHtmlId()
     */
    public function getFocusableHtmlId()
    {
        return $this->visible ? $this->id : null;
    }

    /**
     * Gets the array of CSS classes that are applied to this checkbox.
     *
     * @return array the array of CSS classes that are applied to this
     *               checkbox
     */
    protected function getCSSClassNames()
    {
        $classes = ['swat-checkbox'];

        return array_merge($classes, parent::getCSSClassNames());
    }
}
