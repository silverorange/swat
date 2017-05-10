<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

/**
 * Thrown when a file is not found
 *
 * @package   Swat
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatFileNotFoundException extends SwatException
{
	// {{{ protected properties

	/**
	 * The filename that caused this exception to be thrown.
	 *
	 * @var string
	 */
	protected $filename = '';

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new file not found exception
	 *
	 * @param string $message the message of the exception.
	 * @param integer $code the code of the exception.
	 * @param string $filename the filename of the file that is not found.
	 */
	public function __construct($message = null, $code = 0, $filename = '')
	{
		parent::__construct($message, $code);
		$this->filename = $filename;
	}

	// }}}
	// {{{ public function getFilename()

	/**
	 * Gets the filename of that caused this exception to be thrown
	 *
	 * @return string the filename that caused this exception to be thrown.
	 */
	public function getFilename()
	{
		return $this->filename;
	}

	// }}}
}

?>
