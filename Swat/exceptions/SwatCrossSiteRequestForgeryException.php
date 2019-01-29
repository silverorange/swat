<?php

/**
 * Thrown by {@link SwatForm} when a possible cross-site request forgery is
 * detected
 *
 * By design, it is not possible to get the correct authentication token from
 * this exception. Since it is not possible to get the correct authentication
 * token, the incorrect token is not useful and is also not availble in this
 * exception.
 *
 * @package   Swat
 * @copyright 2007-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatCrossSiteRequestForgeryException extends SwatException
{
	// {{{ protected properties

	/**
	 * The form that did not authenticate
	 *
	 * @var SwatForm
	 */
	protected $form = null;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new cross-site request forgery exception
	 *
	 * @param string $message the message of the exception.
	 * @param integer $code the code of the exception.
	 * @param SwatForm $form the form that did not authenticate.
	 */
	public function __construct($message = null, $code = 0, SwatForm $form)
	{
		parent::__construct($message, $code);
		$this->form = $form;
	}

	// }}}
	// {{{ public function getForm()

	/**
	 * Gets the form that did not authenticate
	 *
	 * @return SwatForm the form that did not authenticate.
	 */
	public function getForm()
	{
		return $this->form;
	}

	// }}}
}
