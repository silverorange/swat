<?php

/**
 * Readahead iterator.
 *
 * This allows you to get the next element of the current iteration of an
 * iterator. This is most useful when iterating over a set of values that
 * define a range.
 *
 * ```php
 * $iterator = new SwatDBReadaheadIterator($recordset);
 * while($iterator->iterate()) {
 *     $current = $iterator->getCurrent();
 *     $next = $iterator->getNext();
 * }
 * ```
 *
 * @copyright 2007-2026 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDBReadaheadIterator extends SwatObject
{
    /**
     * The iterator object being iterated.
     */
    private Iterator $iterator;

    /**
     * The item of the current iteration.
     *
     * @var mixed
     */
    private $current;

    /**
     * The key of the item of the current iteration.
     *
     * @var mixed
     */
    private $key;

    /**
     * Creates a new readahead iterator.
     *
     * @param array|Iterator $iterator either an array or Iterator object to use for
     *                                 readahead iteration
     */
    public function __construct(array|Iterator $iterator)
    {
        if (is_array($iterator)) {
            $iterator = new ArrayIterator($iterator);
        }

        $this->iterator = $iterator;
        $this->rewind();
    }

    /**
     * Gets the current item.
     *
     * @return mixed the current item. If the iterator contains no items this
     *               will return null. This may also return null if the current
     *               item is null.
     */
    public function getCurrent(): mixed
    {
        return $this->current;
    }

    /**
     * Gets the key of the current item.
     *
     * @return mixed the key of the current item. If the iterator contains no
     *               items this will return null.
     *
     * @see SwatDBReadaheadIterator::getCurrent()
     */
    public function getKey(): mixed
    {
        return $this->key;
    }

    /**
     * Gets the next item.
     *
     * @return mixed the next item in the iterator. If there is no next item,
     *               null is returned. This may or may not mean the current
     *               item is the last item. Use
     *               {@link SwatDBReadaheadIterator::isLast()} to check if
     *               the current item is the last item.
     */
    public function getNext(): mixed
    {
        return $this->isLast() ? null : $this->iterator->current();
    }

    /**
     * Gets the next item key.
     *
     * @return mixed the key of the next item in the iterator. If there is no
     *               next item, null is returned.
     *
     * @see SwatDBReadaheadIterator::getNext();
     */
    public function getNextKey(): mixed
    {
        return $this->isLast() ? null : $this->iterator->key();
    }

    /**
     * Gets whether the current item is the last item.
     *
     * @return bool true if the current item is the last item and false if
     *              it is not
     */
    public function isLast(): bool
    {
        return !$this->iterator->valid();
    }

    /**
     * Iterates over this readahead iterator.
     *
     * @return bool true if there is a next item
     */
    public function iterate(): bool
    {
        $this->current = $this->getNext();
        $this->key = $this->getNextKey();

        $valid = $this->current !== null;

        if ($valid) {
            $this->iterator->next();
        }

        return $valid;
    }

    /**
     * Rewinds this readahead iterator back to the start.
     */
    public function rewind(): void
    {
        $this->iterator->rewind();
        $this->current = null;
        $this->key = null;
    }
}
