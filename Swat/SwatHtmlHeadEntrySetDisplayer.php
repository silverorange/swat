<?php

/**
 * Displays HTML head entries
 *
 * This class manages all the sorting, combining and displaying of HTML head
 * entries.
 *
 * @package   Swat
 * @copyright 2010-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatHtmlHeadEntrySetDisplayer extends SwatObject
{
    // {{{ protected properties

    /**
     * @var Concentrate_Concentrator
     */
    protected $concentrator;

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new HTML head entry collection
     *
     * @param Concentrate_Concentrator $concentrator
     */
    public function __construct(Concentrate_Concentrator $concentrator)
    {
        $this->concentrator = $concentrator;
    }

    // }}}
    // {{{ public function display()

    /**
     * Displays a set of HTML head entries
     *
     * @param SwatHtmlHeadEntrySet $set the HTML head entry set to display.
     * @param string $uri_prefix an optional URI prefix to prepend to all the
     *                            displayed HTML head entries.
     * @param string $tag an optional tag to suffix the URI with. This is
     *                     suffixed as a HTTP get var and can be used to
     *                     explicitly refresh the browser cache.
     * @param boolean $combine whether or not to combine files. Defaults to
     *                          false.
     * @param boolean $minify whether or not to minify files. Defaults to
     *                         false.
     */
    public function display(
        SwatHtmlHeadEntrySet $set,
        $uri_prefix = '',
        $tag = null,
        $combine = false,
        $minify = false
    ) {
        // clone set so displaying doesn't modify it
        $set = clone $set;

        $entries = $set->toArray();

        // combine files
        if ($combine) {
            $info = $this->getCombinedEntries($entries);
            $entries = $info['entries'];
            $uris = $info['superset'];
        } else {
            $uris = array_keys($entries);
        }

        // check for conflicts in the displayed set
        $this->checkForConflicts($uris);

        // sort
        $entries = $this->getSortedEntries($entries);

        // display entries
        $current_type = null;
        foreach ($entries as $entry) {
            if ($this->compareTypes($current_type, $entry->getType()) !== 0) {
                $current_type = $entry->getType();
                echo "\n";
            }

            echo "\t";

            $prefix = $uri_prefix;

            if ($minify && $this->concentrator->isMinified($entry->getUri())) {
                $prefix = $prefix . 'min/';
            }

            if ($entry->getType() === 'SwatLessStyleSheetHtmlHeadEntry') {
                $prefix = $prefix . 'compiled/';
                $entry = $entry->getStyleSheetHeadEntry();
            }

            $entry->display($prefix, $tag);
            echo "\n";
        }

        echo "\n";
    }

    // }}}
    // {{{ public function displayInline()

    /**
     * Displays the contents of the set of HTML head entries inline
     */
    public function displayInline(
        SwatHtmlHeadEntrySet $set,
        $path,
        $type = null
    ) {
        $entries = $set->toArray();

        $uris = array_keys($entries);

        // check for conflicts in the displayed set
        $this->checkForConflicts($uris);

        // sort
        $entries = $this->getSortedEntries($entries);

        // display entries inline
        // TODO: Use Concentrate_Inliner to display CSS inline
        foreach ($entries as $entry) {
            if ($type === null || $entry->getType() === $type) {
                echo "\t", '<!-- ', $entry->getUri(), ' -->', "\n";
                $entry->displayInline($path);
                echo "\n\t";
            }
        }

        echo "\n";
    }

    // }}}
    // {{{ protected function getCombinedEntries()

    /**
     * Gets the entries of this set accounting for combining
     *
     * @param array $entries
     *
     * @return array the entries of this set accounting for combinations.
     */
    protected function getCombinedEntries(array $entries)
    {
        $info = $this->concentrator->getCombines(array_keys($entries));

        // add combines to set of entries
        foreach ($info['combines'] as $combine) {
            if (mb_substr($combine, -4) === '.css') {
                $class_name = 'SwatStyleSheetHtmlHeadEntry';
            } elseif (mb_substr($combine, -5) === '.less') {
                $class_name = 'SwatLessStyleSheetHtmlHeadEntry';
            } else {
                $class_name = 'SwatJavaScriptHtmlHeadEntry';
            }
            $entries[$combine] = new $class_name($combine, '__combine__');
        }

        // remove files included in combines
        $entries = array_intersect_key($entries, array_flip($info['files']));

        return array(
            'entries' => $entries,
            'superset' => $info['superset']
        );
    }

    // }}}
    // {{{ protected function getSortedEntries()

    /**
     * Gets the entries of this set sorted by their correct display order
     *
     * @param array $original_entries
     *
     * @return array the entries of this set sorted by their correct display
     *               order.
     */
    protected function getSortedEntries(array $original_entries)
    {
        $entries = array();

        // get array of entries with native ordering so we can do a
        // stable, user-defined sort
        $count = 0;
        foreach ($original_entries as $uri => $entry) {
            $entries[] = array(
                'order' => $count,
                'uri' => $uri,
                'object' => $entry
            );
            $count++;
        }

        // stable-sort entries
        usort($entries, array($this, 'compareEntries'));

        // put back in a flat array
        $sorted_entries = array();
        foreach ($entries as $uri => $entry) {
            $sorted_entries[$uri] = $entry['object'];
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
     * @param array $b right side of comparison. A two element array containing
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
        $type_comparison = $this->compareTypes(
            $a_object->getType(),
            $b_object->getType()
        );

        if ($type_comparison !== 0) {
            return $type_comparison;
        }

        // compare dependency order from concentrate data
        $a_uri = $a['uri'];
        $b_uri = $b['uri'];
        $compare = $this->concentrator->compareFiles($a_uri, $b_uri);
        if ($compare != 0) {
            return $compare;
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
    // {{{ protected function compareTypes()

    /**
     * Compares two HTML head entry types
     *
     * @param string $a left side of comparison.
     * @param string $b right side of comparison.
     *
     * @return integer a tri-value where -1 means the left side is less than
     *                  the right side, 1 means the left side is greater than
     *                  the right side and 0 means the left side and right
     *                  side are equivalent.
     */
    protected function compareTypes($a, $b)
    {
        // compare entry type order
        $type_order = $this->getTypeOrder();

        if (!array_key_exists($a, $type_order)) {
            $a = '__unknown__';
        }

        if (!array_key_exists($b, $type_order)) {
            $b = '__unknown__';
        }

        if ($type_order[$a] > $type_order[$b]) {
            return 1;
        }

        if ($type_order[$a] < $type_order[$b]) {
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
            'SwatLessStyleSheetHtmlHeadEntry' => 0, // Consider same as CSS
            'SwatLinkHtmlHeadEntry' => 1,
            'SwatInlineJavaScriptHtmlHeadEntry' => 2,
            'SwatJavaScriptHtmlHeadEntry' => 3,
            'SwatCommentHtmlHeadEntry' => 4,
            '__unknown__' => 5
        );
    }

    // }}}
    // {{{ protected function checkForConflicts()

    /**
     * Check for conflicts in a set of HTML head entry URIs
     *
     * If a conflict is detected, an exception is thrown explaining the
     * conflict.
     *
     * @param array $uris the HTML head entry URIs to check.
     *
     * @throws SwatException if one or more conflicts are present.
     */
    protected function checkForConflicts(array $uris)
    {
        $conflicts = $this->concentrator->getConflicts($uris);
        if (count($conflicts) > 0) {
            $conflict_list = '';
            $count = 0;
            foreach ($conflicts as $file => $conflict) {
                $conflict_list .= sprintf(
                    "\n- %s conflicts with %s",
                    $file,
                    implode(', ', $conflict)
                );

                $count++;
            }
            throw new SwatException(
                'Could not display head entries because the following ' .
                    'conflicts were detected: ' .
                    $conflict_list
            );
        }
    }

    // }}}
}
