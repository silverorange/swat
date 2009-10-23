<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatObject.php';
require_once 'Swat/SwatHtmlHeadEntry.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';

/**
 * A collection of HTML head entries
 *
 * This collection class manages all the sorting and merging of entries.
 *
 * @package   Swat
 * @copyright 2006-2009 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatHtmlHeadEntrySet extends SwatObject
{
	// {{{ protected properties

	/**
	 * HTML head entries managed by this collection
	 *
	 * @var array
	 */
	protected $entries = array();

	/**
	 * HTML head entries organized by type
	 *
	 * @var array
	 */
	protected $entries_by_type = array();

	/**
	 * HTML head entries organized by package
	 *
	 * @var array
	 */
	protected $entries_by_package = array();

	/**
	 * A lookup table of entry URI's that have already been added
	 *
	 * Array keys are URI's. Array values are <kbd>true</kbd>. This table is
	 * used to avoid adding duplicate entries.
	 *
	 * @var array
	 */
	protected $uris = array();

	/**
	 * Cache of the package dependency ordering for this entry set
	 *
	 * @var array
	 *
	 * @see SwatHtmlHeadEntrySet::getDependencyOrder()
	 */
	protected $dependency_order = null;

	/**
	 * A lookup table of packages that have already been displayed.
	 *
	 * This table is used by the recursive displayEntriesRecursive() method.
	 *
	 * @var array
	 */
	protected $displayed_packages;

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

		if (!array_key_exists($uri, $this->uris)) {

			// add hash entry so we don't add entry twice
			$this->uris[$uri] = true;

			// add entry to the entries array
			$this->entries[] = $entry;

			// add entry by type
			$type = $entry->getType();
			if (!isset($this->entries_by_type[$type])) {
				$this->entries_by_type[$type] = array();
			}
			$this->entries_by_type[$type][] = $entry;

			// add entry by package
			$package = $entry->getPackageId();
			if ($package === null) {
				$package = '';
			}
			if (!isset($this->entries_by_package[$package])) {
				$this->entries_by_package[$package] = array();
			}
			$this->entries_by_package[$package][] = $entry;

			// clear package dependency order cache if a new package is
			// introduced
			if (is_array($this->dependency_order) &&
				!array_key_exists($package, $this->dependency_order)) {
				$this->dependency_order = null;
			}
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
		foreach ($set->entries as $entry) {
			$this->addEntry($entry);
		}
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this set of HTML head entries
	 *
	 * @param string $uri_prefix an optional URI prefix to prepend to all the
	 *                            displayed HTML head entries.
	 * @param string $tag an optional tag to suffix the URI with. This is
	 *                     suffixed as a HTTP get var and can be used to
	 *                     explicitly refresh the browser cache.
	 */
	public function display($uri_prefix = '', $tag = null)
	{
		$entries = $this->getSortedEntries();

		// display entries
		$current_package = null;
		$current_type    = null;
		foreach ($entries as $entry) {

			if ($current_package !== $entry->getPackageId() ||
				$current_type !== $entry->getType()) {

				$current_package = $entry->getPackageId();
				$current_type    = $entry->getType();
				echo "\n";
			}

			echo "\t";

			$entry->display($uri_prefix, $tag);
			echo "\n";
		}

		echo "\n";
	}

	// }}}
	// {{{ public function displayInline()

	/**
	 * Displays the contents of the set of HTML head entries inline
	 */
	public function displayInline($path, $type = null)
	{
		$entries = $this->getSortedEntries();

		// display entries inline
		$current_package = null;
		$current_type    = null;
		foreach ($entries as $entry) {
			if ($type === null || $entry->getType() === $type) {
				$entry->displayInline($path);
				echo "\n\t";
			}
		}

		echo "\n";
	}

	// }}}
	// {{{ protected function getSortedEntries()

	/**
	 * Gets the entries of this set sorted by their correct display order
	 *
	 * @return array the entries of this set sorted by their correct display
	 *               order.
	 */
	protected function getSortedEntries()
	{
		$entries = array();

		// get array of entries with native ordering so we can do a
		// stable, user-defined sort
		foreach ($this->entries as $key => $value) {
			$entries[] = array(
				'order'  => $key,
				'object' => $value,
			);
		}

		// stable-sort entries
		usort($entries, array($this, 'compareEntries'));

		// put back in a flat array
		$sorted_entries = array();
		foreach ($entries as $entry) {
			$sorted_entries[] = $entry['object'];
		}

		return $sorted_entries;
	}

	// }}}
	// {{{ protected function compareEntries()

	/**
	 * Compares two {@link SwatHtmlHeadEntry} objects to get their display
	 * order
	 *
	 * @param array $a left side of comparison. A two element array containing
	 *                  the keys 'order' and 'object'. The 'order' key contains
	 *                  the native ordering of the entry and the 'object' key
	 *                  contains the entry object.
	 * @param array $b left side of comparison. A two element array containing
	 *                  the keys 'order' and 'object'. The 'order' key contains
	 *                  the native ordering of the entry and the 'object' key
	 *                  contains the entry object.
	 *
	 * @return integer a tri-value where -1 means the left side is less than
	 *                  the right side, 1 means the left side is greater than
	 *                  the right side and 0 means the left side and right
	 *                  side are equivalent.
	 */
	protected function compareEntries(array $a, array $b)
	{

		$a_object = $a['object'];
		$b_object = $b['object'];

		// compare entry type order
		$type_order = $this->getTypeOrder();

		$a_type = $a_object->getType();
		$b_type = $b_object->getType();

		if (!array_key_exists($a_type, $type_order)) {
			$a_type = '__unknown__';
		}

		if (!array_key_exists($b_type, $type_order)) {
			$b_type = '__unknown__';
		}

		if ($type_order[$a_type] > $type_order[$b_type]) {
			return 1;
		}

		if ($type_order[$a_type] < $type_order[$b_type]) {
			return -1;
		}

		// compare package dependency order
		$dep_order = $this->getDependencyOrder();

		$a_package_id = $a_object->getPackageId();
		if ($a_package_id === null) {
			$a_package_id = '__site__';
		}

		$b_package_id = $b_object->getPackageId();
		if ($b_package_id === null) {
			$b_package_id = '__site__';
		}

		if ($dep_order[$a_package_id] > $dep_order[$b_package_id]) {
			return 1;
		}

		if ($dep_order[$a_package_id] < $dep_order[$b_package_id]) {
			return -1;
		}

		// compare added order (keeps sort stable)
		if ($a['order'] > $b['order']) {
			return 1;
		}

		if ($a['order'] < $b['order']) {
			return -1;
		}

		return 0;
	}

	// }}}
	// {{{ protected function getTypeOrder()

	/**
	 * Gets the order in which HTML head entry types should be displayed
	 *
	 * This order is dependent on the way browsers parallelize requests and is
	 * chosen to give the greatest amount of parallelization.
	 *
	 * @return array the order in which HTML head entries should be displayed.
	 *               This is an associative array where the array key is the
	 *               entry type and the array value is the relative display
	 *               order, with lower values being displayed first.
	 */
	protected function getTypeOrder()
	{
		return array(
			'SwatStyleSheetHtmlHeadEntry' => 0,
			'SwatJavaScriptHtmlHeadEntry' => 1,
			'SwatLinkHtmlHeadEntry'       => 2,
			'SwatCommentHtmlHeadEntry'    => 3,
			'__unknown__'                 => 4,
		);
	}

	// }}}
	// {{{ protected function getDependencyOrder()

	/**
	 * Gets the order in which HTML head entry packages should be displayed
	 *
	 * This order is determined by the package dependencies specified in the
	 * static package info classes. For example, Swat's package dependencies
	 * are found in {@link Swat::getDependencies()}.
	 *
	 * @return array the order in which HTML head entries should be displayed.
	 *               This is an associative array where the array key is the
	 *               package id and the array value is the relative display
	 *               order, with lower values being displayed first.
	 *
	 * @see SwatHtmlHeadEntrySet::getDependencyOrderRecursive()
	 */
	protected function getDependencyOrder()
	{
		if ($this->dependency_order === null) {
			$package_ids = array_keys($this->entries_by_package);

			// get ordering of packages in this set
			$order = $this->getDependencyOrderRecursive($package_ids);

			// add site-code as last dependent
			$order[] = '__site__';

			$this->dependency_order = array_flip($order);
		}

		return $this->dependency_order;
	}

	// }}}
	// {{{ protected function getDependencyOrderRecursive()

	/**
	 * Recursively gets the package dependency order of entries in this set
	 *
	 * @param array $package_ids unsorted array of package ids.
	 * @param array $already_sorted package ids that have already been sorted.
	 *
	 * @return array a sorted array of package ids.
	 *
	 * @see SwatHtmlHeadEntrySet::getDependencyOrder()
	 */
	protected function getDependencyOrderRecursive(array $package_ids,
		array $already_sorted = array())
	{
		$return = array();

		foreach ($package_ids as $package_id) {

			// skip site-code for dependency sorting, it goes at the end
			if ($package_id == '') {
				continue;
			}

			// if package is not already sorted
			if (!in_array($package_id, $already_sorted)) {

				// first check for sub-packages
				$dependency_method = array($package_id, 'getDependencies');
				if (is_callable($dependency_method)) {
					$sub_packages = call_user_func($dependency_method);

					// don't consider already sorted sub-packages
					$sub_packages = array_diff($sub_packages, $already_sorted);

					if (count($sub_packages) > 0) {

						// get sub-package dependencies
						$sub_package_dependencies =
							$this->getDependencyOrderRecursive(
								$sub_packages,
								$already_sorted
							);

						// append sorted sub-package dependencies
						$return = array_merge(
							$return,
							$sub_package_dependencies
						);

						$already_sorted = array_merge(
							$already_sorted,
							$sub_package_dependencies
						);
					}
				}

				// finally, append current package, after its dependencies
				$return[] = $package_id;

				$already_sorted[] = $package_id;
			}
		}

		return $return;
	}

	// }}}
}

?>
