<?php

/**
 * Thrown when the ids of two objects collide
 *
 * @package   Swat
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDuplicateIdException extends SwatException
{
    // {{{ protected properties

    /**
     * The id that is colliding
     *
     * @var string
     */
    protected $id = null;

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new duplicate id exception
     *
     * @param string $message the message of the exception.
     * @param integer $code the code of the exception.
     * @param string $id the id that is colliding.
     */
    public function __construct($message = null, $code = 0, $id = null)
    {
        parent::__construct($message, $code);
        $this->id = $id;
    }

    // }}}
    // {{{ public function getId()

    /**
     * Gets the id that is colliding
     *
     * @return string the id that is colliding.
     */
    public function getId()
    {
        return $this->id;
    }

    // }}}
}
