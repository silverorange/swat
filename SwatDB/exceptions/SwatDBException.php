<?php

/**
 * A SwatDB Exception.
 *
 * @copyright 2005-2026 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDBException extends SwatException
{
    public function __construct($message = null, $code = 0)
    {
        if ($message instanceof PEAR_Error) {
            $e = $message;
            $message = $e->getMessage() . "\n" . $e->getUserInfo();
            $code = $e->getCode();
        }

        parent::__construct($message, $code);
    }
}
