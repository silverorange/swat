<?php

require_once('Swat/SwatException.php');

/**
 * The base object type.
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatObject {
	
}

function _S($msgid) {
	// TODO: use this for gettext translation
	return $msgid;
}

function _nS($msgid1, $msgid2, $n) {
	// TODO: use this for gettext translation
	return ($n==1 ? $msgid1 : $msgid2);
}

?>
