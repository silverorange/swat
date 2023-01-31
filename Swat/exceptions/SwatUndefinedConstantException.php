<?php

/**
 * Thrown when a constant is used that is not defined
 *
 * @package   Swat
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatUndefinedConstantException extends SwatException
{
    // {{{ protected properties

    /**
     * The name of the constant that is undefined
     *
     * @var string
     */
    protected $constant_name = null;

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new undefined constant exception
     *
     * @param string $message the message of the exception.
     * @param integer $code the code of the exception.
     * @param string $constant_name the name of the constant that is undefined.
     */
    public function __construct(
        $message = null,
        $code = 0,
        $constant_name = null,
    ) {
        parent::__construct($message, $code);
        $this->constant_name = $constant_name;
    }

    // }}}
    // {{{ public function getConstantName()

    /**
     * Gets the name of the constant that is undefined
     *
     * @return string the name of the constant that is undefined.
     */
    public function getConstantName()
    {
        return $this->constant_name;
    }

    // }}}
}
