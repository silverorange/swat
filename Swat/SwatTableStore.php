<?php

/**
 * A data structure that can be used with the SwatTableView.
 *
 * A new table store is empty by default. Use the
 * {@link SwatTableStore::add()} method to add rows to a table store.
 *
 * @copyright 2004-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTableStore extends SwatObject implements SwatTableModel
{
    /**
     * The individual rows for this data structure.
     *
     * @var list<mixed>
     */
    private array $rows = [];

    /**
     * The current index of the iterator interface.
     */
    private int $current_index = 0;

    /**
     * Gets the number of rows.
     *
     * This satisfies the Countable interface.
     *
     * @return int the number of rows in this data structure
     */
    public function count(): int
    {
        return count($this->rows);
    }

    /**
     * Returns the current element.
     *
     * @return mixed the current element
     */
    public function current(): mixed
    {
        return $this->rows[$this->current_index];
    }

    /**
     * Returns the key of the current element.
     *
     * @return int the key of the current element
     */
    public function key(): int
    {
        return $this->current_index;
    }

    /**
     * Moves forward to the next element.
     */
    public function next(): void
    {
        $this->current_index++;
    }

    /**
     * Moves forward to the previous element.
     */
    public function prev(): void
    {
        $this->current_index--;
    }

    /**
     * Rewinds this iterator to the first element.
     */
    public function rewind(): void
    {
        $this->current_index = 0;
    }

    /**
     * Checks is there is a current element after calls to rewind() and next().
     *
     * @return bool true if there is a current element and false if there
     *              is not
     */
    public function valid(): bool
    {
        return array_key_exists($this->current_index, $this->rows);
    }

    /**
     * Adds a row to this data structure.
     *
     * @param mixed $data the data of the row to add
     */
    public function add($data)
    {
        $this->rows[] = $data;
    }

    /**
     * Adds a row to the beginning of this data structure.
     *
     * @param $data mixed the data of the row to add
     */
    public function addToStart($data)
    {
        array_unshift($this->rows, $data);
        $this->current_index++;
    }

    /**
     * Gets the number of rows in this data structure.
     *
     * @deprecated Use Countable::count()
     */
    public function getRowCount()
    {
        return count($this->rows);
    }

    /**
     * Gets the rows of this data structure as an array.
     *
     * @return array the rows of this data structure
     *
     * @deprecated Use as an Iterator
     */
    public function &getRows()
    {
        return $this->rows;
    }

    /**
     * Adds a row to this data structure.
     *
     * @param $data mixed the data of the row to add
     * @param $id   mixed an optional uniqueid of the row to add
     *
     * @deprecated Use SwatTableStore::add()
     */
    public function addRow($data, $id = null)
    {
        $this->add($data);
    }
}
