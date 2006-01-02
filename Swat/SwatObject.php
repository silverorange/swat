<?php

require_once 'Swat/Swat.php';
require_once 'Swat/exceptions/SwatException.php';

/**
 * The base object type
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatObject
{
	public function __toString()
	{
		ob_start();
		Swat::printObject($this);
		return ob_get_clean();
	}
}

?>
