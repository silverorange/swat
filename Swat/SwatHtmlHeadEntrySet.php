<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */


/**
 * A collection of HTML head entries
 *
 * This collection class manages all the sorting, merging and globbing
 * of entries.
 *
 * @package   Swat
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatHtmlHeadEntrySet implements Countable, IteratorAggregate
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
	 * Maps HTML head entry URIs to {@link SwatHtmlHeadEntry} class names
	 *
	 * @see SwatHtmlHeadEntrySet::addEntry()
	 * @see SwatHtmlHeadEntrySet::addTypeMapping()
	 */
	protected $type_map = array(
		'/\.js$/'  => 'SwatJavaScriptHtmlHeadEntry',
		'/\.css$/' => 'SwatStyleSheetHtmlHeadEntry',
		'/\.less$/' => 'SwatLessStyleSheetHtmlHeadEntry',
	);

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
	 * @param SwatHtmlHeadEntry|string $entry the entry to add.
	 */
	public function addEntry($entry)
	{
		if (is_string($entry)) {
			$class = $this->getClassFromType($entry);

			if ($class === null) {
				throw new SwatClassNotFoundException(
					'SwatHtmlHeadEntry class not found for entry string of "'.
					$entry.'".');
			}

			$entry = new $class($entry);
		}

		if (!($entry instanceof SwatHtmlHeadEntry)) {
			throw new SwatInvalidTypeException(
				'Added entry must be either a string or an instance of a'.
				'SwatHtmlHeadEntry.', 0, $entry);
		}

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
	// {{{ public function count()

	/**
	 * Gets the number of entries in this set
	 *
	 * Fulfills the Coutnable interface.
	 *
	 * @return integer the number of entries in this set.
	 */
	public function count()
	{
		return count($this->entries);
	}

	// }}}
	// {{{ public function getIterator()

	/**
	 * Gets an iterator over the entries in this set
	 *
	 * Fulfills the IteratorAggregate interface.
	 *
	 * @return Iterable an iterator over the entries in this set.
	 */
	public function getIterator()
	{
		// return an array copy by design to fulfil the IteratorAggregate
		// interface.
		return $this->entries;
	}

	// }}}
	// {{{ public function addTypeMapping()

	public function setTypeMapping($type, $class = null)
	{
		if (is_string($type)) {
			if ($class === null) {
				throw new InvalidArgumentException(
					'If $type is specified, $class is required');
			}
			$type = array($type => (string)$class);
			$class = null;
		}

		if (!is_array($type)) {
			throw new InvalidArgumentException(
				'Type must either be an array or a string.');
		}

		if ($class !== null) {
			throw new InvalidArgumentException(
				'If $type is an array, $class must not be specified.');
		}

		$this->type_map = array_merge($this->type_map, $type);
	}

	// }}}
	// {{{ public function getByType()

	/**
	 * Gets a subset of this set by the entry type
	 *
	 * @param string $type the type of HTML head entry to get. For example,
	 *                      'SwatJavaScriptHtmlHeadEntry'.
	 *
	 * @return SwatHtmlHeadEntrySet a subset of this set containing only
	 *                              entries of the specified type. If no such
	 *                              entries exist, an empty set is returned.
	 */
	public function getByType($type)
	{
		$class = __CLASS__;
		$set = new $class();
		foreach ($this->entries as $entry) {
			if ($entry->getType() === $type) {
				$set->addEntry($entry);
			}
		}
		return $set;
	}

	// }}}
	// {{{ protected function getClassFromType()

	protected function getClassFromType($entry)
	{
		$class = null;

		foreach ($this->type_map as $type => $type_class) {
			if (preg_match($type, $entry) === 1) {
				$class = $type_class;
				break;
			}
		}

		return $class;
	}

	// }}}
}

?>
