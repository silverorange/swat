<?php

/**
 * A checkbox tree widget with which tracks option dependency
 *
 * Any time a checkbox is checked all child options will also be checked.
 *
 * @package   Swat
 * @copyright 2021 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatCheckboxChildDependencyTree extends SwatCheckboxTree
{
    // {{{ public function __construct()

    /**
     * Creates a new checkbox list
     *
     * @param string $id a non-visible unique id for this widget.
     *
     * @see SwatCheckboxList::__construct()
     */
    public function __construct($id = null)
    {
        parent::__construct($id);

        $this->addJavaScript(
            'packages/swat/javascript/swat-checkbox-child-dependency-tree.js'
        );
    }

    // }}}
    // {{{ protected function getJavaScriptClassName()

    /**
     * Get the name of the JavaScript class for this widget
     *
     * @return string JavaScript class name.
     */
    protected function getJavaScriptClassName()
    {
        return 'SwatCheckboxChildDependencyTree';
    }

    // }}}
}
