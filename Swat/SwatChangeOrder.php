<?php

/**
 * An element ordering widget.
 *
 * This widget uses JavaScript to present an orderable list of elements. The
 * ordering of elements is what this widget returns.
 *
 * If two options are added to this control with equivalent values the returned
 * order of the two options is arbitrary.
 *
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatChangeOrder extends SwatOptionControl implements SwatState
{
    /**
     * Value ordered array.
     *
     * The current ordering of options in the widget. If null, options are
     * displayed in the order of the options array.
     *
     * @var array
     */
    public $values;

    /**
     * Width of the order box (in stylesheet units).
     *
     * @var string
     */
    public $width = '600px';

    /**
     * Height of the order box (in stylesheet units).
     *
     * @var string
     */
    public $height = '300px';

    /**
     * Creates a new change-order widget.
     *
     * @param string $id a non-visible unique id for this widget
     *
     * @see SwatWidget::__construct()
     */
    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->requires_id = true;

        $yui = new SwatYUI(['dom', 'event']);
        $this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());

        $this->addStyleSheet('packages/swat/styles/swat-change-order.css');
        $this->addJavaScript('packages/swat/javascript/swat-change-order.js');
        $this->addJavaScript(
            'packages/swat/javascript/swat-z-index-manager.js',
        );
    }

    /**
     * Displays this change-order control.
     */
    public function display()
    {
        if (!$this->visible) {
            return;
        }

        parent::display();

        $ordered_options = $this->getOrderedOptions();

        $div_tag = new SwatHtmlTag('div');
        $div_tag->id = $this->id;
        $div_tag->class = $this->getCSSClassString();
        $div_tag->open();

        $list_div = new SwatHtmlTag('div');
        $list_div->style = "width: {$this->width}; height: {$this->height};";
        $list_div->id = "{$this->id}_list";
        $list_div->class = 'swat-change-order-list';
        $list_div->open();

        $option_div = new SwatHtmlTag('div');
        $option_div->class = 'swat-change-order-item';

        foreach ($ordered_options as $option) {
            $title = $option->title ?? '';
            $option_div->setContent($title, $option->content_type);
            $option_div->display();
        }

        $list_div->close();

        $this->displayButtons();

        echo '<div class="swat-clear"></div>';

        $values = [];
        foreach ($ordered_options as $option) {
            $values[] = SwatString::signedSerialize(
                $option->value,
                $this->getForm()->getSalt(),
            );
        }

        $hidden_tag = new SwatHtmlTag('input');
        $hidden_tag->type = 'hidden';
        $hidden_tag->id = $this->id . '_value';
        $hidden_tag->name = $this->id;
        $hidden_tag->value = implode(',', $values);
        $hidden_tag->display();

        $hidden_items_tag = new SwatHtmlTag('input');
        $hidden_items_tag->type = 'hidden';
        $hidden_items_tag->id = $this->id . '_dynamic_items';
        $hidden_items_tag->value = '';
        $hidden_items_tag->display();

        $div_tag->close();

        Swat::displayInlineJavaScript($this->getInlineJavaScript());
    }

    public function process()
    {
        parent::process();

        $form = $this->getForm();
        $data = &$form->getFormData();
        $this->values = [];
        if ($data[$this->id] !== '') {
            $values = explode(',', $data[$this->id]);
            foreach ($values as $value) {
                $value = SwatString::signedUnserialize(
                    $value,
                    $form->getSalt(),
                );

                $this->values[] = $value;
            }
        }
    }

    /**
     * Gets a note letting the user know drag-and-drop is available for
     * ordering items.
     *
     * @return SwatMessage a note letting the user know drag-and-drop is
     *                     available for ordering items
     *
     * @see SwatControl::getNote()
     */
    public function getNote()
    {
        $message = Swat::_(
            'Items can be ordered by dragging-and-dropping with the mouse.',
        );

        return new SwatMessage($message);
    }

    public function getState()
    {
        if ($this->values === null) {
            return array_keys($this->options);
        }

        return $this->values;
    }

    public function setState($state)
    {
        $this->values = $state;
    }

    /**
     * Gets the options of this change-order control ordered by the
     * values of this change-order.
     *
     * If this control has two or more equivalent values, the order of options
     * having those values is arbitrary.
     *
     * @return array the options of this change-order control ordered by the
     *               values of this change-order
     */
    public function &getOrderedOptions()
    {
        if ($this->values === null) {
            $ordered_options = $this->options;
        } else {
            // copy options array so we don't modify the original
            $options = $this->options;
            $ordered_options = [];
            foreach ($this->values as $value) {
                foreach ($options as $key => $option) {
                    if ($option->value === $value) {
                        $ordered_options[] = $option;
                        unset($options[$key]);
                        break;
                    }
                }
            }

            // add leftover options
            foreach ($options as $option) {
                $ordered_options[] = $option;
            }
        }

        return $ordered_options;
    }

    /**
     * Gets the array of CSS classes that are applied to this change-order
     * widget.
     *
     * @return array the array of CSS classes that are applied to this
     *               change-order widget
     */
    protected function getCSSClassNames()
    {
        $classes = ['swat-change-order'];

        return array_merge($classes, parent::getCSSClassNames());
    }

    /**
     * Gets the inline JavaScript required by this change-order control.
     *
     * @return string the inline JavaScript required by this change-order
     *                control
     */
    protected function getInlineJavaScript()
    {
        return sprintf(
            "var %s_obj = new SwatChangeOrder('%s', %s);",
            $this->id,
            $this->id,
            $this->isSensitive() ? 'true' : 'false',
        );
    }

    private function displayButtons()
    {
        $buttons_div = new SwatHtmlTag('div');
        $buttons_div->class = 'swat-change-order-buttons';
        $buttons_div->open();

        $btn_tag = new SwatHtmlTag('input');
        $btn_tag->type = 'button';
        if (!$this->isSensitive()) {
            $btn_tag->disabled = 'disabled';
        }

        $btn_tag->value = Swat::_('Move to Top');
        $btn_tag->onclick = "{$this->id}_obj.moveToTop();";
        $btn_tag->name = "{$this->id}_buttons";
        $btn_tag->class = 'swat-change-order-top';
        $btn_tag->display();

        echo '<br />';

        $btn_tag->value = Swat::_('Move Up');
        $btn_tag->onclick = "{$this->id}_obj.moveUp();";
        $btn_tag->name = "{$this->id}_buttons";
        $btn_tag->class = 'swat-change-order-up';
        $btn_tag->display();

        echo '<br />';

        $btn_tag->value = Swat::_('Move Down');
        $btn_tag->onclick = "{$this->id}_obj.moveDown();";
        $btn_tag->name = "{$this->id}_buttons";
        $btn_tag->class = 'swat-change-order-down';
        $btn_tag->display();

        echo '<br />';

        $btn_tag->value = Swat::_('Move to Bottom');
        $btn_tag->onclick = "{$this->id}_obj.moveToBottom();";
        $btn_tag->name = "{$this->id}_buttons";
        $btn_tag->class = 'swat-change-order-bottom';
        $btn_tag->display();

        $buttons_div->close();
    }
}
