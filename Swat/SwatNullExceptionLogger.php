<?php

/**
 * An implementation of SwatExceptionLogger that does not log anywhere.
 *
 * This logger can be used to avoid conditional statements in your code.
 *
 * @copyright 2025 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see       SwatExceptionLogger
 */
class SwatNullExceptionLogger extends SwatExceptionLogger
{
    /**
     * {@inheritDoc}
     */
    public function log(SwatException $e) {}
}
