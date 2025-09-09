<?php

/**
 * An implementation of SwatErrorLogger that does not log anywhere.
 *
 * This logger can be used to avoid conditional statements in your code.
 *
 * @copyright 2025 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see       SwatErrorLogger
 */
class SwatNullErrorLogger extends SwatErrorLogger
{
    /**
     * {@inheritDoc}
     */
    public function log(SwatError $e) {}
}
