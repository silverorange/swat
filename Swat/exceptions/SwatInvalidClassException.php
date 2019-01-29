<?php

/**
 * Thrown when an object is of the wrong class
 *
 * @package   Swat
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatInvalidClassException extends SwatException
{
	// {{{ protected properties

	/**
	 * The object that is of the wrong class
	 *
	 * @var mixed
	 */
	protected $object = null;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new invalid class exception
	 *
	 * @param string $message the message of the exception.
	 * @param integer $code the code of the exception.
	 * @param mixed $object the object that is of the wrong class.
	 */
	public function __construct($message = null, $code = 0, $object = null)
	{
		parent::__construct($message, $code);
		$this->object = $object;
	}

	// }}}
	// {{{ public function getObject()

	/**
	 * Gets the object that is of the wrong class
	 *
	 * @return mixed the object that is of the wrong class.
	 */
	public function getObject()
	{
		return $this->object;
	}

	// }}}
}
