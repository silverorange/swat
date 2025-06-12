<?php

/**
 * Thrown when an invalid property type is used.
 *
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatInvalidPropertyTypeException extends SwatException
{
    // {{{ protected properties

    /**
     * The name of the type that is invalid.
     *
     * @var string
     */
    protected $type;

    /**
     * The object the property is invalid for.
     *
     * @var mixed
     */
    protected $object;

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new invalid class exception.
     *
     * @param string     $message the message of the exception
     * @param int        $code    the code of the exception
     * @param mixed      $object  the object the property is invalid for
     * @param mixed|null $type
     */
    public function __construct(
        $message = null,
        $code = 0,
        $object = null,
        $type = null,
    ) {
        parent::__construct($message, $code);
        $this->object = $object;
        $this->type = $type;
    }

    // }}}
    // {{{ public function getObject()

    /**
     * Gets the object the property is invalid for.
     *
     * @return mixed the object the property is invalid for
     */
    public function getObject()
    {
        return $this->object;
    }

    // }}}
    // {{{ public function getType()

    /**
     * Gets the name of the type that is invalid.
     *
     * @return string the name of the type that is invalid
     */
    public function getType()
    {
        return $this->type;
    }

    // }}}
}
