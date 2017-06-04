<?php

/**
 * A SwatDB Exception.
 *
 * @package   SwatDB
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDBException extends SwatException
{
	// {{{ private function ___construct()

	public function __construct($message = null, $code = 0)
	{
		if (is_object($message) && ($message instanceof PEAR_Error)) {
			$error = $message;
			$message = $error->getMessage();
			$message.= "\n".$error->getUserInfo();
			$code = $error->getCode();
		}

		parent::__construct($message, $code);
	}

	// }}}
}

?>
