<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */


/**
 * Thrown when a serialized value is poisioned (does not match salted value)
 *
 * @package   Swat
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatInvalidSerializedDataException extends SwatException
{
	// {{{ protected properties

	/**
	 * The unsafe serialized data
	 *
	 * @var string
	 */
	protected $data = null;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new invalid serialized data exception
	 *
	 * @param string $message the message of the exception.
	 * @param integer $code the code of the exception.
	 * @param mixed $data the unsafe serialized data.
	 */
	public function __construct($message = null, $code = 0, $data = null)
	{
		parent::__construct($message, $code);
		$this->data = $data;
	}

	// }}}
	// {{{ public function getData()

	/**
	 * Gets the unsafe serialized data
	 *
	 * @return string the unsafe serialized data that triggered this exception.
	 */
	public function getData()
	{
		return $this->data;
	}

	// }}}
}

?>
