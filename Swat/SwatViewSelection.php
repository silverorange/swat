<?php

/**
 * A selection on a view
 *
 * Selections are iteratable and countable. A usual pattern for working with
 * selections is to use it in a <code>foreach</code> statement as follows:
 *
 * <code>
 * foreach ($view->getSelection() as $id) {
 *     // perform an action with the id
 * }
 * </code>
 *
 * There can be multiple selections on a single view because each view can have
 * multiple selectors.
 *
 * Selections usually include only row ids, not rows themselves. Though this
 * may seem less useful, it is done because the selection may be used after
 * the view processed but before the view is displayed. Often the view data
 * is only created when the view is displayed. If the processing of a view
 * means the view does not need to be displayed this can remove the need for
 * unnecessary queries.
 *
 * @package   Swat
 * @copyright 2007-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       SwatView::getSelection()
 */
class SwatViewSelection extends SwatObject implements Countable, Iterator
{
    // {{{ private properties

    /**
     * The selected items of this selection
     *
     * @var array
     */
    private $selected_items = array();

    /**
     * Current array index of the selected items of this selection
     *
     * Used for implementing the Iterator interface.
     *
     * @var integer
     */
    private $current_index = 0;

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new selection object
     *
     * @param array $selected_items the selected items of this selection. This
     *                               is usually an array of item identifiers,
     *                               not an array of item objects.
     */
    public function __construct(array $selected_items)
    {
        $this->selected_items = array_values($selected_items);
    }

    // }}}
    // {{{ public function current()

    /**
     * Returns the current selected item
     *
     * @return mixed the current selected item.
     */
    public function current()
    {
        return $this->selected_items[$this->current_index];
    }

    // }}}
    // {{{ public function key()

    /**
     * Returns the key of the current selected item
     *
     * @return integer the key of the current selected item
     */
    public function key()
    {
        return $this->current_index;
    }

    // }}}
    // {{{ public function next()

    /**
     * Moves forward to the next selected item
     */
    public function next()
    {
        $this->current_index++;
    }

    // }}}
    // {{{ public function prev()

    /**
     * Moves forward to the previous selected item
     */
    public function prev()
    {
        $this->current_index--;
    }

    // }}}
    // {{{ public function rewind()

    /**
     * Rewinds this iterator to the first selected item
     */
    public function rewind()
    {
        $this->current_index = 0;
    }

    // }}}
    // {{{ public function valid()

    /**
     * Checks is there is a current selected item after calls to rewind() and
     * next()
     *
     * @return boolean true if there is a current selected item and false if
     *                  there is not.
     */
    public function valid()
    {
        return isset($this->selected_items[$this->current_index]);
    }

    // }}}
    // {{{ public funciton count()

    /**
     * Gets the number of items in this selection
     *
     * This satisfies the Countable interface.
     *
     * @return integer the number of items in this selection.
     */
    public function count()
    {
        return count($this->selected_items);
    }

    // }}}
    // {{{ public function contains()

    /**
     * Checks whether or not this selection contains an item
     *
     * @param mixed $item the item to check.
     *
     * @return boolean true if this selection contains the specified item and
     *                  false if it does not.
     */
    public function contains($item)
    {
        return in_array($item, $this->selected_items);
    }

    // }}}
}
