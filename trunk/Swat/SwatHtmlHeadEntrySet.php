<?php

require_once 'Swat/SwatObject.php';
require_once 'Swat/SwatHtmlHeadEntry.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';

/**
 * A collection of HTML head entries
 *
 * This collection class manages all the sorting and merging of entries.
 *
 * @package   Swat
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatHtmlHeadEntrySet extends SwatObject
{
	// {{{ private properties

	/**
	 * HTML head entries managed by this collection
	 *
	 * @var array
	 */
	private $entries = array();

	/**
	 * HTML head entries orgainzed by type
	 *
	 * @var array
	 */
	private $entries_by_type = array();

	/**
	 * A lookup table of entry URIs that have already been added
	 *
	 * This table is used to avoid duplicates.
	 *
	 * @var array
	 */
	private $uris = array();

	/**
	 * A lookup table of packages that have already been displayed.
	 *
	 * This table is used by the recursive displayEntriesRecursive() method.
	 *
	 * @var array
	 */
	private $displayed_packages;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new HTML head entry collection
	 *
	 * @param SwatHtmlHeadEntrySet $set an optional existing HTML head entry
	 *                                   set to build this set from.
	 */
	public function __construct($set = null)
	{
		if ($set !== null) {
			if (!($set instanceof SwatHtmlHeadEntrySet))
				throw new SwatInvalidClassException('Set must be an instance '.
					'of SwatHtmlHeadEntrySet.');

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

		if (!in_array($uri, $this->uris)) {
			$this->uris[] = $uri;
			array_push($this->entries, $entry);

			$type = $entry->getType();
			if (!isset($this->entries_by_type[$type]))
				$this->entries_by_type[$type] = array();

			array_push($this->entries_by_type[$type], $entry);
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
		foreach ($set->entries as $entry)
			$this->addEntry($entry);
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this set of HTML head entries
	 *
	 * @param string $uri_prefix an optional URI prefix to prepend to all the
	 *                            displayed HTML head entries.
	 */
	public function display($uri_prefix = '')
	{
		$parameters = array($uri_prefix);

		$this->displayEntriesRecursive(null,
			'displayEntriesForPackage', $parameters);

		echo "\n";
	}

	// }}}
	// {{{ public function displayInline()

	/**
	 * Displays the contents of the set of HTML head entries inline
	 */
	public function displayInline($path, $type = null)
	{
		$parameters = array($path, $type);

		$this->displayEntriesRecursive(null,
			'displayInlineEntriesForPackage', $parameters);

		echo "\n";
	}

	// }}}
	// {{{ protected function displayEntriesRecursive()

	protected function displayEntriesRecursive($package_id,
		$display_method, $display_method_parameters = array())
	{
		if ($package_id === null) {
			$this->displayed_packages = array();

			/*
			 * Displaying entries for the site code, so any non-null package
			 * ids are dependencies of the site code and should be displayed
			 * first.
			 */
			foreach ($this->entries as $entry)
				if ($entry->getPackageId() !== null)
					$this->displayEntriesRecursive($entry->getPackageId(),
						$display_method, $display_method_parameters);

		} else {
			/*
			 * Displaying entries for a package, so find what packages are
			 * dependiencies of this package and display their entries first.
			 */
			$dependency_method = array($package_id, 'getDependencies');

			if (is_callable($dependency_method)) {
				$dependent_packages =
					call_user_func(array($package_id, 'getDependencies'));

				foreach ($dependent_packages as $dep_package_id)
					$this->displayEntriesRecursive($dep_package_id,
						$display_method, $display_method_parameters);
			}
		}

		/*
		 * Track which packages have already been displayed in order to
		 * display each package exactly once.
		 */
		if (in_array($package_id, $this->displayed_packages))
			return;
		else
			$this->displayed_packages[] = $package_id;

		array_unshift($display_method_parameters, $package_id);
		call_user_func_array(array($this, $display_method), $display_method_parameters);
	}

	// }}}
	// {{{ protected function displayEntriesForPackage()

	protected function displayEntriesForPackage($package_id, $uri_prefix)
	{
		echo "\n\t", '<!-- head entries for ',
			($package_id === null) ?
				'site code' : 'package '.$package_id, "-->\n\t";

		foreach ($this->entries_by_type as $entries) {
			foreach ($entries as $entry) {
				if ($entry->getPackageId() === $package_id) {
					$entry->display($uri_prefix);
					echo "\n\t";
				}
			}
		}
	}

	// }}}
	// {{{ protected function displayInlineEntriesForPackage()

	protected function displayInlineEntriesForPackage($package_id, $path,
		$type)
	{
		echo "\n\t", '<!-- inline entries for ',
			($package_id === null) ?
				'site code' : 'package '.$package_id, "-->\n\t";

		foreach ($this->entries as $entry) {
			if ($type === null || $entry->getType() === $type) {
				if ($entry->getPackageId() === $package_id) {
					$entry->displayInline($path);
					echo "\n\t";
				}
			}
		}
	}

	// }}}
}

?>
