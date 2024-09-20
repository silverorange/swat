<?php

/**
 * Thrown when a message type is used that is not defined.
 *
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatUndefinedMessageTypeException extends SwatException
{
    /**
     * The name of the message type that is undefined.
     */
    protected ?string $message_type = null;

    /**
     * Creates a new undefined message type exception.
     *
     * @param string  $message      the message of the exception
     * @param int     $code         the code of the exception
     * @param ?string $message_type
     */
    public function __construct(
        $message = null,
        $code = 0,
        $message_type = null,
    ) {
        parent::__construct($message, $code);
        $this->message_type = $message_type;
    }

    /**
     * Gets the name of the message type that is undefined.
     *
     * @return ?string the name of the message type that is undefined
     */
    public function getMessageType(): ?string
    {
        return $this->message_type;
    }
}
