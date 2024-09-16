<?php

/**
 * Thrown when an invalid constant expression is used
 *
 * @package   Swat
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatInvalidConstantExpressionException extends SwatException
{


    /**
     * The constant expression that is invalid
     *
     * @var string
     */
    protected $expression = null;



    /**
     * Creates a new class not found exception
     *
     * @param string $message the message of the exception.
     * @param integer $code the code of the exception.
     * @param string $expression the constant expression that is invalid.
     */
    public function __construct($message = null, $code = 0, $expression = null)
    {
        parent::__construct($message, $code);
        $this->expression = $expression;
    }



    /**
     * Gets the constant expression that is invalid
     *
     * @return string the constant expression that is invalid.
     */
    public function getExpression()
    {
        return $this->expression;
    }

}
