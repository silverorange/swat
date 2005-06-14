<?php

require_once 'Swat/SwatException.php';

/**
 * Container for package wide static methods
 *
 * @package   Swat
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class Swat
{
	const GETTEXT_DOMAIN = 'swat';

	function _($message)
	{
		return Swat::gettext($message);
	}

	function gettext($message)
	{
		return dgettext(Swat::GETTEXT_DOMAIN, $message);
	}

	function ngettext($singular_message, $plural_message, $number)
	{
		return dngettext(Swat::GETTEXT_DOMAIN, $singular_message, $plural_message, $number);
	}

}

?>
