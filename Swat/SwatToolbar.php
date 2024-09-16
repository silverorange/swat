<?php

/**
 * A toolbar container for a group of related {@link SwatToolLink} objects
 *
 * @package   Swat
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatToolbar extends SwatDisplayableContainer
{


    /**
     * Creates a new toolbar
     *
     * @param string $id a non-visible unique id for this widget.
     *
     * @see SwatWidget::__construct()
     */
    public function __construct($id = null)
    {
        parent::__construct($id);

        $this->addStyleSheet('packages/swat/styles/swat-toolbar.css');
    }



    /**
     * Displays this toolbar as an unordered list with each sub-item
     * as a list item
     */
    public function display()
    {
        if (!$this->visible) {
            return;
        }

        SwatWidget::display();

        $toolbar_ul = new SwatHtmlTag('ul');
        $toolbar_ul->id = $this->id;
        $toolbar_ul->class = $this->getCSSClassString();

        $toolbar_ul->open();
        $this->displayChildren();
        $toolbar_ul->close();
    }



    /**
     * Sets the value of all {@link SwatToolLink} objects within this toolbar
     *
     * This is usually more convenient than setting all the values by hand
     * if the values are dynamic.
     *
     * @param string $value
     */
    public function setToolLinkValues($value)
    {
        foreach ($this->getToolLinks() as $tool) {
            $tool->value = $value;
        }
    }



    /**
     * Gets the tool links of this toolbar
     *
     * Returns an the array of {@link SwatToolLink} objects contained
     * by this toolbar.
     *
     * @return array the tool links contained by this toolbar.
     */
    public function getToolLinks()
    {
        $tools = [];
        foreach ($this->getDescendants('SwatToolLink') as $tool) {
            if ($tool->getFirstAncestor('SwatToolbar') === $this) {
                $tools[] = $tool;
            }
        }

        return $tools;
    }



    /**
     * Displays the child widgets of this container
     */
    protected function displayChildren()
    {
        foreach ($this->children as &$child) {
            ob_start();
            $child->display();
            $content = ob_get_clean();
            if ($content != '') {
                echo '<li>', $content, '</li>';
            }
        }
    }



    /**
     * Gets the array of CSS classes that are applied to this tool bar
     *
     * @return array the array of CSS classes that are applied to this tool bar.
     */
    protected function getCSSClassNames()
    {
        $classes = ['swat-toolbar'];

        if ($this->parent instanceof SwatContainer) {
            $children = $this->parent->getChildren();
            if (end($children) === $this) {
                $classes[] = 'swat-toolbar-end';
            }
        }

        $classes = array_merge($classes, parent::getCSSClassNames());
        return $classes;
    }

}
