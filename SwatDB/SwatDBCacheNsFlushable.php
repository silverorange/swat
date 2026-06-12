<?php

/**
 * Interface that supports flushing cache name-spaces.
 *
 * @copyright 2014-2026 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
interface SwatDBCacheNsFlushable
{
    /**
     * Flushes a cache name-space.
     *
     * @param string $ns The name-space to flush
     */
    public function flushNs(string $ns): void;
}
