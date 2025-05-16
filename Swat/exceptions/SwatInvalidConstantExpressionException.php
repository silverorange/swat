<?php

/**
 * Thrown when an invalid constant expression is used.
 *
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatInvalidConstantExpressionException extends SwatException
{
    // {{{ protected properties

    /**
     * The constant expression that is invalid.
     *
     * @var string
     */
    protected $expression;

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new class not found exception.
     *
     * @param string $message    the message of the exception
     * @param int    $code       the code of the exception
     * @param string $expression the constant expression that is invalid
     */
    public function __construct($message = null, $code = 0, $expression = null)
    {
        parent::__construct($message, $code);
        $this->expression = $expression;
    }

    // }}}
    // {{{ public function getExpression()

    /**
     * Gets the constant expression that is invalid.
     *
     * @return string the constant expression that is invalid
     */
    public function getExpression()
    {
        return $this->expression;
    }

    // }}}
}
