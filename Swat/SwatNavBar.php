<?php

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatNavBarEntry.php';
require_once 'Swat/SwatString.php';

/**
 * Visible navigation tool
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
	 * @param string $link an optional entry URI.
	 */
	public function createEntry($title, $link = null)
	{
		$this->addEntry(new SwatNavBarEntry($title, $link));
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
	// {{{ public function addEntryToStart()

	/**
	 * Adds a SwatNavBarEntry to the beginning of this navigation bar
	 *
	 * @param SwatNavBarEntry $entry the entry to add.
	 */
	public function addEntryToStart($entry)
	{
		array_unshift($this->entries, $entry);
	}

	// }}}
	// {{{ public function replaceEntryByPosition()

	/**
	 * Replaces an entry in this navigation bar
	 *
	 * If the entry is not in this navigation bar, an exception is thrown.
	 *
	 * @param integer $position zero-based ordinal position of the entry
	 *                           to replace.
	 * @param SwatNavBarEntry $entry the navbar entry to replace the element
	 *                                at the given position with.
	 *
	 * @return SwatNavBarEntry the replaced entry.
	 * 
	 * @thows SwatException
	 */

	public function replaceEntryByPosition($position,
		SwatNavBarEntry $new_entry)
	{
		if (isset($this->entries[$position])) {
			$old_entry = $this->entries[$position];
			$this->entries[$position] = $new_entry;

			return $old_entry;
		}

		throw new SwatException(sprintf('Cannot replace element at position '.
			'%s because NavBar does not contain an entry at position %s.',
			$position,
			$opsition));
	}

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
			throw new SwatException(sprintf('Navbar does not contain an '.
				'entry at position %s.',
				$position));
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
	 * Pops the last entry off the end of this navigational bar
	 *
	 * If no entries currently exist, an exception is thrown.
	 *
	 * @return SwatNavBarEntry the entry that was popped.
	 *
	 * @throws SwatException
	 */
	public function popEntry()
	{
		if ($this->getCount() < 1)
			throw new SwatException('Cannot pop entry. NavBar does not '.
				'contain any entries.');
		else
			return array_pop($this->entries);
	}

	// }}}
	// {{{ public function popEntries()

	/**
	 * Pops one or more entries off the end of this navigational bar
	 *
	 * If more entries are to be popped than currently exist, an exception is
	 * thrown.
	 *
	 * @param $number integer number of entries to pop off this navigational
	 *                         bar.
	 *
	 * @return array an array of SwatNavBarEntry objects that were popped off
	 *                the navagational bar.
	 *
	 * @throws SwatException
	 */
	public function popEntries($number)
	{
		if ($this->getCount() < $number) {
			$count = $this->getCount();

			throw new SwatException(printf('Unable to pop %s entries. NavBar '.
				'only contains %s entries.',
				$number,
				$count));

		} else {
			return array_splice($this->entries, -$number);
		}
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this navigational bar
	 *
	 * Displays each entry separated by a special character and outputs
	 * entries with URI's as anchor tags.
	 *
	 * @param boolean $link_last_entry if true will show the last entry as
	 *                                  a hyperlink. If false, will show the
	 *                                  last entry as just text.
	 */
	public function display($link_last_entry = true)
	{
		if (!$this->visible)
			return;

		$count = $this->getCount();
		$i = 1;

		foreach ($this->entries as $entry) {
			
			// display separator
			if ($i > 1)
				echo '&nbsp;&#187; ';

			// link all entries or link all but the last entry
			$link = ($link_last_entry || $i < $count);
			
			$this->displayEntry($entry, $link);

			$i++;
		}
	}

	// }}}
	// {{{ protected function displayEntry()

	/**
	 * Displays an entry in this navigational bar
	 *
	 * @param SwatNavBarEntry $entry the entry to display.
	 * @param boolean $link whether or not to hyperlink the given entry if the
	 *                       entry has a link set.
	 */
	protected function displayEntry(SwatNavBarEntry $entry, $show_link = true)
	{
		$title = ($entry->title === null) ? '' : $entry->title;

		if ($entry->link !== null && $show_link) {
			$link_tag = new SwatHtmlTag('a');
			$link_tag->href = $entry->link;
			$link_tag->setContent($title);
			$link_tag->display();
		} else {
			echo SwatString::minimizeEntities($title);
		}
	}

	// }}}
}

?>
