<?php

/**
 * Base class for containers that display an XHTML element.
 *
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDisplayableContainer extends SwatContainer
{
    /**
     * Displays this container.
     */
    public function display()
    {
        if (!$this->visible) {
            return;
        }

        SwatWidget::display();

        $div = new SwatHtmlTag('div');
        $div->id = $this->id;
        $div->class = $this->getCSSClassString();

        $div->open();
        $this->displayChildren();
        $div->close();
    }

    /**
     * Gets the array of CSS classes that are applied to this displayable
     * container.
     *
     * @return array the array of CSS classes that are applied to this
     *               displayable container
     */
    protected function getCSSClassNames()
    {
        $classes = ['swat-displayable-container'];

        return array_merge($classes, parent::getCSSClassNames());
    }
}
