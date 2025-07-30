<?php

/**
 * An exception in Swat.
 *
 * Exceptions in Swat have handy methods for outputting nicely formed error
 * messages.
 *
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatInvalidSwatMLException extends SwatException
{
    /**
     * The filename of the SwatML file that caused this exception to be thrown.
     *
     * @var string
     */
    protected $filename = '';

    /**
     * Creates a new invalid SwatML exception.
     *
     * @param string $message  the message of the exception
     * @param int    $code     the code of the exception
     * @param string $filename the filename of the SwatML file that is invalid
     */
    public function __construct($message = null, $code = 0, $filename = '')
    {
        parent::__construct($message, $code);
        $this->filename = $filename;
    }

    /**
     * Gets the filename of the SwatML file that caused this exception to be
     * thrown.
     *
     * @return string the filename of the SwatML file that caused this
     *                exception to be thrown
     */
    public function getFilename()
    {
        return $this->filename;
    }
}
