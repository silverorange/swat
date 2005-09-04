<?php

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatNavBarEntry.php';

/**
 * Visible navbar navigation tool
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatNavBar extends SwatControl
{
	// {{{ private properties

	/**
	 * Array of SwatNavBarEntry objects displayed in this navbar
	 *
	 * @var array
	 *
	 * @see SwatNavBarEntry
	 */
	private $entries = array();

	// }}}
	// {{{ public function createEntry()

	/**
	 * Creates a SwatNavBarEntry and adds it to the end of this navigation bar
	 *
	 * @param string $title the entry title.
	 * @param string $uri an optional entry URI.
	 */
	public function createEntry($title, $uri = null)
	{
		$this->addEntry(new SwatNavBarEntry($title, $uri));
	}

	// }}}
	// {{{ public function addEntry()

	/**
	 * Adds a SwatNavBarEntry to the end of this navigation bar
	 *
	 * @param SwatNavBarEntry $entry the entry to add.
	 */
	public function addEntry($entry)
	{
		$this->entries[] = $entry;
	}

	// }}}
	// {{{ public function replaceElement()

	/**
	 * Replaces an element in this navigation bar
	 *
	 * If the element is not in this navigation bar, an exception is thrown.
	 *
	 * @param integer $position position of the element to replace.
	 * @param string $title the replacement element title.
	 * @param string $uri an optional replacement element URI.
	 *
	 * @thows SwatException
	 */
	/*
	public function replaceElement($position, $title, $uri = null)
	{
		if (isset($this->elements[$position])) {
			$new_element = array('title' => $title, 'uri' => $uri);
			$this->elements[$position] = &$new_element;
		} else {
			throw new SwatException(sprintf(__CLASS__.': Cannot replace '.
				"element at position '%s' because element does not exist.",
				$position));
		}
	}
	*/

	// }}}
	// {{{ public function getEntryByPosition()

	/**
	 * Gets an entry from this navigation bar
	 *
	 * If the entry is not in this navigation bar, an exception is thrown.
	 *
	 * @param integer $position zero-based ordinal position of the entry to 
	 *                           fetch.
	 *
	 * @return SwatNavBarEntry the entry.
	 *
	 * @throws SwatException
	 */
	public function getEntryByPosition($position)
	{
		if (isset($this->entries[$position]))
			return $this->entries[$position];
		else
			throw new SwatException('Navbar does not contain an entry at '.
				"position '$position'");
	}

	// }}}
	// {{{ public function getCount()

	/**
	 * Gets the number of entries in this navigational bar
	 *
	 * @return integer number of entries in this navigational bar.
	 */
	public function getCount()
	{
		return count($this->entries);
	}

	// }}}
	// {{{ public function popEntry()

	/**
	 * Pops one or more entries off the end of this navigational bar
	 *
	 * If more entires are to be popped than currently exist, an exception is
	 * thrown.
	 *
	 * @param $number integer number of entries to pop off this navigational
	 *                         bar.
	 *
	 * @throws SwatException
	 */
	public function popEntry($number = 1)
	{
		if ($number <= $this->getCount()) {
			for ($i = 0; $i < $number; $i++)
				$ret = array_pop($this->entries);

			return $ret;
		} else {
			throw new SwatException("NavBar does contain $number entries.");
		}
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this navigational bar
	 *
	 * Displays each entry separated by a special character and outputs
	 * entries with URI's as anchor tags.
	 */
	public function display($link_last = true)
	{
		if (!$this->visible)
			return;

		$count = count($this->entries);
		$i = 0;

		foreach ($this->entries as $entry) {
			if ($i++ != 0)
				echo ' &#187; ';

			if ($entry->uri !== null && ($link_last || $i !== $count)) {
				$link_tag = new SwatHtmlTag('a');
				$link_tag->href = $entry->uri;
				$link_tag->content = $entry->title;
				$link_tag->display();
			} else {
				echo $entry->title;
			}
		}
	}

	// }}}
}

?>
