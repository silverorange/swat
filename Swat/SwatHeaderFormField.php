<?php

/**
 * A container to use around control widgets in a form
 *
 * Adds a label and space to output messages.
 *
 * @package   Swat
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatHeaderFormField extends SwatFormField
{
    // {{{ protected function getCSSClassNames()

    /**
     * Gets the array of CSS classes that are applied to this header form field
     *
     * @return array the array of CSS classes that are applied to this header
     *                form field.
     */
    protected function getCSSClassNames()
    {
        $classes = parent::getCSSClassNames();
        array_unshift($classes, 'swat-header-form-field');
        return $classes;
    }

    // }}}
}
