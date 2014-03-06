<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

/**
 * Interface for a class that supports flushing cache name-spaces 
 *
 * @package   SwatDB
 * @copyright 2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
interface SwatDBCacheNsFlushable
{
	// {{{ public function flushNs()
	/**
	 * Flushes a cache name-space
	 *
	 * @param string $name_space Name space
	 */
	public function flushNs($ns);

	// }}}
}

?>
