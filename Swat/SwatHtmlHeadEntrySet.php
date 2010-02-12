<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Concentrate/Concentrator.php';
require_once 'Swat/SwatObject.php';
require_once 'Swat/SwatHtmlHeadEntry.php';

/**
 * A collection of HTML head entries
 *
 * This collection class manages all the sorting, merging and globbing
 * of entries.
 *
 * @package   Swat
 * @copyright 2006-2010 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatHtmlHeadEntrySet extends SwatObject
{
	// {{{ protected properties

	/**
	 * HTML head entries managed by this collection
	 *
	 * Entries are indexed by URI.
	 *
	 * @var array
	 */
	protected $entries = array();

	/**
	 * @var boolean
	 */
	protected $iterator_valid = true;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new HTML head entry collection
	 *
	 * @param SwatHtmlHeadEntrySet $set an optional existing HTML head entry
	 *                                   set to build this set from.
	 */
	public function __construct(SwatHtmlHeadEntrySet $set = null)
	{
		if ($set !== null) {
			$this->addEntrySet($set);
		}
	}

	// }}}
	// {{{ public function addEntry()

	/**
	 * Adds a HTML head entry to this set
	 *
	 * @param SwatHtmlHeadEntry $entry the entry to add.
	 */
	public function addEntry(SwatHtmlHeadEntry $entry)
	{
		$uri = $entry->getUri();
		if (!array_key_exists($uri, $this->entries)) {
			$this->entries[$uri] = $entry;
		}
	}

	// }}}
	// {{{ public function addEntrySet()

	/**
	 * Adds a set of HTML head entries to this set
	 *
	 * @param SwatHtmlHeadEntrySet $set the set to add.
	 */
	public function addEntrySet(SwatHtmlHeadEntrySet $set)
	{
		$this->entries = array_merge($this->entries, $set->entries);
	}

	// }}}
	// {{{ public function toArray()

	public function toArray()
	{
		return $this->entries;
	}

	// }}}
}

?>
