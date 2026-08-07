<?php

/**
 * Thrown when a property that cannot be marshalled is asked to be
 * marshalled.
 *
 * @copyright 2013-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDBMarshallException extends SwatDBException
{
    public function __construct(
        $message,
        $code = 0,
        protected string $property = ''
    ) {
        parent::__construct($message, $code);
    }

    public function getProperty(): string
    {
        return $this->property;
    }
}
