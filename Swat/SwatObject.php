<?php

/**
 * The base object type.
 *
 * @copyright 2004-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatObject implements Stringable
{
    /**
     * Gets this object as a string.
     *
     * This is a magic method called by PHP when this object is used
     * in a string context. For example:
     *
     * ```php
     * $my_object = new SwatMessage('Hello, World!');
     * echo $my_object;
     * ```
     *
     * @return string this object represented as a string
     */
    public function __toString(): string
    {
        ob_start();
        Swat::printObject($this);

        return ob_get_clean();
    }
}
