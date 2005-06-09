<?php

require_once 'Swat/SwatException.php';

/**
 * The base object type
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatObject
{
}

function _S($msgid)
{
	// TODO: use this for gettext translation
	return $msgid;
}

function _nS($msgid1, $msgid2, $n)
{
	// TODO: use this for gettext translation
	return ($n == 1) ? $msgid1 : $msgid2;
}

?>
