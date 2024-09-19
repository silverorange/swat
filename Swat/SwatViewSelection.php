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


    /**
     * The selected items of this selection
     *
     * @var array
     */
    private $selected_items = [];

    /**
     * Current array index of the selected items of this selection
     *
     * Used for implementing the Iterator interface.
     *
     * @var integer
     */
    private $current_index = 0;



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



    /**
     * Returns the current selected item
     *
     * @return mixed the current selected item.
     */
    public function current(): mixed
    {
        return $this->selected_items[$this->current_index];
    }



    /**
     * Returns the key of the current selected item
     *
     * @return int the key of the current selected item
     */
    public function key(): int
    {
        return $this->current_index;
    }



    /**
     * Moves forward to the next selected item
     */
    public function next(): void
    {
        $this->current_index++;
    }



    /**
     * Moves forward to the previous selected item
     */
    public function prev(): void
    {
        $this->current_index--;
    }



    /**
     * Rewinds this iterator to the first selected item
     */
    public function rewind(): void
    {
        $this->current_index = 0;
    }



    /**
     * Checks is there is a current selected item after calls to rewind() and
     * next()
     *
     * @return boolean true if there is a current selected item and false if
     *                  there is not.
     */
    public function valid(): bool
    {
        return isset($this->selected_items[$this->current_index]);
    }



    /**
     * Gets the number of items in this selection
     *
     * This satisfies the Countable interface.
     *
     * @return integer the number of items in this selection.
     */
    public function count(): int
    {
        return count($this->selected_items);
    }



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

}
