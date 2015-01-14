<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatObject.php';

/**
 * Readahead iterator
 *
 * This allows you to get the next element of the current iteration of an
 * iterator. This is most useful when iterating over a set of values that
 * define a range.
 *
 * Usage:
 * <code>
 * <?php
 * $iterator = new SwatDBReadaheadIterator($recordset);
 * while($iterator->iterate()) {
 *     $current = $iterator->getCurrent();
 *     $next = $iterator->getNext();
 * }
 * ?>
 * </code>
 *
 * @package   SwatDB
 * @copyright 2007-2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDBReadaheadIterator extends SwatObject
{
	// {{{ private properties

	/**
	 * The iterator object being iterated
	 *
	 * @var Iterator
	 */
	private $iterator;

	/**
	 * The item of the current iteration
	 *
	 * @var mixed
	 */
	private $current;

	/**
	 * The key of the item of the current iteration
	 *
	 * @var mixed
	 */
	private $key;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new readahead iterator
	 *
	 * @param array|Iterator either an array or Iterator object to use for
	 *                        readahead iteration.
	 *
	 * @throws InvalidArgumentException if the <i>$iterator</i> is not an array
	 *                                  or an Iterator.
	 */
	public function __construct($iterator)
	{
		if (is_array($iterator)) {
			$iterator = new ArrayIterator($iterator);
		}

		if (!($iterator instanceof Iterator)) {
			throw new InvalidArgumentException(
				'$iterator must be either an array or an Iterator.');
		}

		$this->iterator = $iterator;
		$this->rewind();
	}

	// }}}
	// {{{ public function getCurrent()

	/**
	 * Gets the current item
	 *
	 * @return mixed the current item. If the iterator contains no items this
	 *                will return null. This may also return null if the current
	 *                item is null.
	 */
	public function getCurrent()
	{
		return $this->current;
	}

	// }}}
	// {{{ public function getKey()

	/**
	 * Gets the key of the current item
	 *
	 * @return mixed the key of the current item. If the iterator contains no
	 *                items this will return null.
	 *
	 * @see SwatDBReadaheadIterator::getCurrent()
	 */
	public function getKey()
	{
		return $this->key;
	}

	// }}}
	// {{{ public function getNext()

	/**
	 * Gets the next item
	 *
	 * @return mixed the next item in the iterator. If there is no next item,
	 *                null is returned. This may or may not mean the current
	 *                item is the last item. Use
	 *                {@link SwatDBReadaheadIterator::isLast()} to check if
	 *                the current item is the last item.
	 */
	public function getNext()
	{
		return ($this->isLast()) ? null : $this->iterator->current();
	}

	// }}}
	// {{{ public function getNextKey()

	/**
	 * Gets the next item key
	 *
	 * @return mixed the key of the next item in the iterator. If there is no
	 *                next item, null is returned.
	 *
	 * @see SwatDBReadaheadIterator::getNext();
	 */
	public function getNextKey()
	{
		return ($this->isLast()) ? null : $this->iterator->key();
	}

	// }}}
	// {{{ public function isLast()

	/**
	 * Gets whether the current item is the last item
	 *
	 * @return boolean true if the current item is the last item and false if
	 *                  it is not.
	 */
	public function isLast()
	{
		return (!$this->iterator->valid());
	}

	// }}}
	// {{{ public function iterate()

	/**
	 * Iterates over this readahead iterator
	 *
	 * @return boolean true if there is a next item.
	 */
	public function iterate()
	{
		$this->current = $this->getNext();
		$this->key = $this->getNextKey();

		$valid = ($this->current !== null);

		if ($valid) {
			$this->iterator->next();
		}

		return $valid;
	}

	// }}}
	// {{{ public function rewind()

	/**
	 * Rewinds this readahead iterator back to the start
	 */
	public function rewind()
	{
		$this->iterator->rewind();
		$this->current = null;
		$this->key = null;
	}

	// }}}
}

?>
