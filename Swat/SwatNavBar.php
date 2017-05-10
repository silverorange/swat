<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

/**
 * Visible navigation tool (breadcrumb trail)
 *
 * @package   Swat
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       SwatNavBarEntry
 */
class SwatNavBar extends SwatControl implements Countable
{
	// {{{ public properties

	/**
	 * Whether or not to display the last entry in this navbar as a link
	 *
	 * If set to false, the last entry is displayed as text even if the last
	 * navbar entry has a link. Defaults to true.
	 *
	 * @var boolean
	 */
	public $link_last_entry = true;

	/**
	 * Separator characters displayed between each navbar entry in this navbar
	 *
	 * The default separator is a non-breaking space followed by a right
	 * guillemet followed by a breaking space.
	 *
	 * @var string
	 */
	public $separator = ' » ';

	/**
	 * Optional container tag for this navigational bar
	 *
	 * The container tag wraps around all entries in this navigational bar.
	 *
	 * @var SwatHtmlTag the container tag for this navigational bar.
	 */
	public $container_tag;

	// }}}
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
	 * @param string $content_type an optional content type for the entry title.
	 */
	public function createEntry($title, $link = null,
		$content_type = 'text/plain')
	{
		$this->addEntry(new SwatNavBarEntry($title, $link, $content_type));
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
	// {{{ public function addEntries()

	/**
	 * Adds an array of SwatNavBarEntry to the end of this navigation bar
	 *
	 * @param array $entries array of entries to add.
	 */
	public function addEntries($entries)
	{
		foreach ($entries as $entry)
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
			$position));
	}

	// }}}
	// {{{ public function getEntryByPosition()

	/**
	 * Gets an entry from this navigation bar
	 *
	 * If the entry is not in this navigation bar, an exception is thrown.
	 *
	 * @param integer $position zero-based ordinal position of the entry to
	 *                           fetch.  If position is negative, the entry
	 *                           position is counted from the end of the nav
	 *                           bar (-1 will return one from the end).  Use
	 *                           getLastEntry() to get the last entry of the
	 *                           nav bar.
	 *
	 * @return SwatNavBarEntry the entry.
	 *
	 * @throws SwatException
	 */
	public function getEntryByPosition($position)
	{
		if ($position < 0)
			$position = count($this) + $position - 1;

		if (isset($this->entries[$position]))
			return $this->entries[$position];
		else
			throw new SwatException(sprintf('Navbar does not contain an '.
				'entry at position %s.',
				$position));
	}

	// }}}
	// {{{ public function getLastEntry()

	/**
	 * Gets the last entry from this navigation bar
	 *
	 * If the navigation bar is empty, an exception is thrown.
	 *
	 * @return SwatNavBarEntry the entry.
	 *
	 * @throws SwatException
	 */
	public function getLastEntry()
	{
		if (count($this->entries) == 0)
			throw new SwatException('Navbar is empty.');

		return end($this->entries);
	}

	// }}}
	// {{{ public function getCount()

	/**
	 * Gets the number of entries in this navigational bar
	 *
	 * @return integer number of entries in this navigational bar.
	 *
	 * @deprecated this class now implements Countable. Use count($object)
	 *              instead of $object->getCount().
	 */
	public function getCount()
	{
		return count($this->entries);
	}

	// }}}
	// {{{ public function count()

	/**
	 * Gets the number of entries in this navigational bar
	 *
	 * This satisfies the Countable interface.
	 *
	 * @return integer number of entries in this navigational bar.
	 */
	public function count()
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
		if (count($this) < 1)
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
		if (count($this) < $number) {
			$count = count($this);

			throw new SwatException(printf('Unable to pop %s entries. NavBar '.
				'only contains %s entries.',
				$number,
				$count));

		} else {
			return array_splice($this->entries, -$number);
		}
	}

	// }}}
	// {{{ public function clear()

	/**
	 * Clears all entries from this navigational bar
	 *
	 * @return array an array of SwatNavBarEntry objects that were cleared from
	 *                this navagational bar.
	 */
	public function clear()
	{
		$entries = $this->entries;
		$this->entries = array();
		return $entries;
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this navigational bar
	 *
	 * Displays each entry separated by a special character and outputs
	 * navbar entries with links as anchor tags.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		parent::display();

		$count = count($this);
		$i = 1;

		$container_tag = $this->getContainerTag();
		$container_tag->open();

		foreach ($this->entries as $entry) {
			// display separator
			if ($i > 1)
				echo SwatString::minimizeEntities($this->separator);

			// link all entries or link all but the last entry
			$link = ($this->link_last_entry || $i < $count);

			$this->displayEntry($entry, $link, ($i == 1));

			$i++;
		}

		$container_tag->close();
	}

	// }}}
	// {{{ protected function displayEntry()

	/**
	 * Displays an entry in this navigational bar
	 *
	 * @param SwatNavBarEntry $entry the entry to display.
	 * @param boolean $link whether or not to hyperlink the given entry if the
	 *                       entry has a link set.
	 * @param boolean $first whether or not this entry should be displayed as
	 *                        the first entry.
	 */
	protected function displayEntry(SwatNavBarEntry $entry, $show_link = true,
		$first = false)
	{
		$title = ($entry->title === null) ? '' : $entry->title;
		$link  = $this->getLink($entry);

		if ($link !== null && $show_link) {
			$a_tag = new SwatHtmlTag('a');
			$a_tag->href = $link;
			if ($first)
				$a_tag->class = 'swat-navbar-first';

			$a_tag->setContent($title, $entry->content_type);
			$a_tag->display();
		} else {
			$span_tag = new SwatHtmlTag('span');
			if ($first)
				$span_tag->class = 'swat-navbar-first';

			$span_tag->setContent($title, $entry->content_type);
			$span_tag->display();
		}
	}

	// }}}
	// {{{ protected function getLink()

	/**
	 * Gets the link from an entry.
	 *
	 * @param SwatNavBarEntry $entry the entry to get the link from.
	 *
	 * @return string the entries link.
	 */
	protected function getLink(SwatNavBarEntry $entry)
	{
		return $entry->link;
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this navigational bar
	 *
	 * @return array the array of CSS classes that are applied to this
	 *                navigational bar.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-nav-bar');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
	// {{{ protected function getContainerTag()

	/**
	 * Gets the container tag for this navigational bar
	 *
	 * The container tag wraps around all entries in this navigational bar.
	 *
	 * @return SwatHtmlTag the container tag for this navigational bar.
	 */
	protected function getContainerTag()
	{
		if ($this->container_tag === null)
			$tag = new SwatHtmlTag('div');
		else
			$tag = $this->container_tag;

		$tag->id = $this->id;
		$tag->class = $this->getCSSClassString();
		return $tag;
	}

	// }}}
}

?>
