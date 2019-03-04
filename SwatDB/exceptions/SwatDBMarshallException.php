<?php

/**
 * Thrown when a property that can not be marshalled is asked to be
 * marshalled
 *
 * @package   SwatDB
 * @copyright 2013-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDBMarshallException extends SwatDBException
{
    // {{{ protected properties

    /**
     * @var string
     */
    protected $property = '';

    // }}}
    // {{{ public function __construct()

    public function __construct($message, $code = 0, $property = '')
    {
        parent::__construct($message, $code);
        $this->property = $property;
    }

    // }}}
    // {{{ public function getProperty()

    public function getProperty()
    {
        return $this->property;
    }

    // }}}
}
