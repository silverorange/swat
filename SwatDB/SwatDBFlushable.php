<?php

/**
 * Interface that supports setting a flushable cache
 *
 * @package   SwatDB
 * @copyright 2014-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
interface SwatDBFlushable
{

	/**
	 * Sets the flushable cache to use for this object
	 *
	 * @param SwatDBCacheNsFlushable $cache The flushable cache to use for
	 *                                      this object.
	 * @see SwatDBCacheNsFlushable
	 */
	public function setFlushableCache(SwatDBCacheNsFlushable $cache);

}

?>
