<?php

/**
 * Thrown when a users tries to set a callback to a value that is not a
 * callback
 *
 * @package   Swat
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatInvalidCallbackException extends SwatException
{
	// {{{ protected properties

	/**
	 * The value the user tried to set the callback to
	 *
	 * @var mixed
	 */
	protected $callback = null;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new invalid callback exception
	 *
	 * @param string $message the message of the exception.
	 * @param integer $code the code of the exception.
	 * @param mixed $callback the value the user tried to set the callback to.
	 */
	public function __construct($message = null, $code = 0, $callback = null)
	{
		parent::__construct($message, $code);
		$this->callback = $callback;
	}

	// }}}
	// {{{ public function getCallback()

	/**
	 * Gets the value the user tried to set the callback to
	 *
	 * @return mixed the value the user tried to set the callback to.
	 */
	public function getCallback()
	{
		return $this->callback;
	}

	// }}}
}
