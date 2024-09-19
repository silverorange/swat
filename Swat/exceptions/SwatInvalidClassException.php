<?php

/**
 * Thrown when an object is of the wrong class.
 *
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatInvalidClassException extends SwatException
{
    /**
     * The object that is of the wrong class.
     *
     * @var mixed
     */
    protected $object;

    /**
     * Creates a new invalid class exception.
     *
     * @param string $message the message of the exception
     * @param int    $code    the code of the exception
     * @param mixed  $object  the object that is of the wrong class
     */
    public function __construct($message = null, $code = 0, $object = null)
    {
        parent::__construct($message, $code);
        $this->object = $object;
    }

    /**
     * Gets the object that is of the wrong class.
     *
     * @return mixed the object that is of the wrong class
     */
    public function getObject()
    {
        return $this->object;
    }
}
