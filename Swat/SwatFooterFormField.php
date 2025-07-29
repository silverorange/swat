<?php

/**
 * A container to use around control widgets in a form.
 *
 * Adds a label and space to output messages.
 *
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatFooterFormField extends SwatFormField
{
    /**
     * Gets the array of CSS classes that are applied to this footer form field.
     *
     * @return array the array of CSS classes that are applied to this footer
     *               form field
     */
    protected function getCSSClassNames()
    {
        $classes = parent::getCSSClassNames();
        array_unshift($classes, 'swat-footer-form-field');

        return $classes;
    }
}
