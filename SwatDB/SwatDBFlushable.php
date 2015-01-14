<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

/**
 * Interface that supports setting a flushable cache 
 *
 * @package   SwatDB
 * @copyright 2014-2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
interface SwatDBFlushable
{
	// {{{ public function setFlushableCache()

	/**
	 * Sets the flushable cache to use for this object
	 *
	 * @param SwatDBCacheNsFlushable $cache The flushable cache to use for
	 *                                      this object.
 	 * @see SwatDBCacheNsFlushable
	 */
	public function setFlushableCache(SwatDBCacheNsFlushable $cache);

	// }}}
}

?>
