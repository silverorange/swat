<?php

/**
 * Abstract base class for logging SwatException objects
 *
 * A custom exception logger can be used to change how uncaught exceptions
 * are logged in an application. For example, you may want to log exceptions in
 * a database or store exception details in a separate file.
 *
 * @package   Swat
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       SwatException::setLogger()
 */
abstract class SwatExceptionLogger
{


    /**
     * Logs a SwatException
     *
     * This is called by SwatException::process().
     */
    abstract public function log(SwatException $e);

}
